<?php
require_once 'Conexion.php';
require_once 'Usuario.php';
require_once 'DetallePedido.php';

class Pedido
{
    private int $id_pedido;
    private int $id_usuario;
    private int $cuotas;
    private string $fecha_pedido;
    private string $estado;
    private string $metodo_pago;
    private string $metodo_envio;
    private string $direccion;
    private ?string $observaciones;
    private float $subtotal;
    private float $costo_envio;
    private float $total;

    // Getters

    public function getId(): int
    {
        return $this->id_pedido;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function getFechaPedido(): string
    {
        return $this->fecha_pedido;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getMetodoPago(): string
    {
        return $this->metodo_pago;
    }

    public function getMetodoEnvio(): string
    {
        return $this->metodo_envio;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getCostoEnvio(): float
    {
        return $this->costo_envio;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getCuotas(): int
    {
        return $this->cuotas;
    }

    // Métodos
    public static function crear(
        int $idUsuario,
        string $metodoPago,
        int $cuotas,
        string $metodoEnvio,
        string $direccion,
        ?string $observaciones,
        float $subtotal,
        float $costoEnvio,
        float $total,
        array $productos
    ): int {

        $conexion = (new Conexion())->getConexion();

        try {

            $conexion->beginTransaction();

            $query = "INSERT INTO pedidos
                (
                    id_usuario,
                    fecha_pedido,
                    estado,
                    metodo_pago,
                    metodo_envio,
                    direccion,
                    observaciones,
                    subtotal,
                    costo_envio,
                    total,
                    cuotas
                )
            VALUES
            (
                ?,
                NOW(),
                'Pendiente',
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )";

            $stmt = $conexion->prepare($query);

            $stmt->execute([
                $idUsuario,
                $metodoPago,
                $metodoEnvio,
                $direccion,
                $observaciones,
                $subtotal,
                $costoEnvio,
                $total,
                $cuotas

            ]);

            $idPedido = (int) $conexion->lastInsertId();

            foreach ($productos as $producto) {

                DetallePedido::crear(
                    $conexion,
                    $idPedido,
                    $producto
                );

            }

            $conexion->commit();

            return $idPedido;

        } catch (Throwable $e) {

            $conexion->rollBack();

            throw $e;

        }

    }

    public static function buscarPorId(int $idPedido): ?Pedido
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT *
                FROM pedidos
                WHERE id_pedido = ?";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$idPedido]);

        $pedido = $stmt->fetch();

        return $pedido ?: null;
    }

    public static function buscarPorUsuario(int $idUsuario): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT *
                FROM pedidos
                WHERE id_usuario = ?
                ORDER BY fecha_pedido DESC";

        $stmt = $conexion->prepare($query);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            self::class
        );

        $stmt->execute([$idUsuario]);

        return $stmt->fetchAll();
    }

    public static function obtenerPedidoCompleto(int $idPedido): ?array
    {
        $pedido = self::buscarPorId($idPedido);

        if (!$pedido) {
            return null;
        }

        $usuario = Usuario::buscarPorId(
            $pedido->getIdUsuario()
        );

        $productos = DetallePedido::obtenerPorPedido(
            $pedido->getId()
        );

        return [
            'pedido'    => $pedido,
            'usuario'   => $usuario,
            'productos' => $productos
        ];
    }

    public static function listarTodos(): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "
            SELECT
                p.*,
                u.nombre AS cliente
            FROM pedidos p
            INNER JOIN usuarios u
                ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_pedido DESC
        ";

        $stmt = $conexion->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizarEstado(int $idPedido, string $estado): bool
    {
        $conexion = (new Conexion())->getConexion();

        $query = "UPDATE pedidos
                SET estado = ?
                WHERE id_pedido = ?";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([$estado, $idPedido]);
    }
}