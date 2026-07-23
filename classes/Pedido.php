<?php
require_once 'Conexion.php';
require_once 'Usuario.php';
require_once 'DetallePedido.php';
require_once 'Vino.php';

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

                if (!Vino::descontarStock(
                    $conexion,
                    $producto['vino']->getIdVino(),
                    $producto['cantidad']
                )) {
                    throw new Exception(
                        'Stock insuficiente para completar la compra.'
                    );
                }

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

    // Trae la dirección del pedido más reciente del usuario que haya sido
    // enviado a domicilio (se excluye "retiro en tienda", que no es una
    // dirección propia del cliente). Devuelve null si nunca cargó una.
 
    public static function buscarUltimaDireccion(int $idUsuario): ?string
    {
        $conexion = (new Conexion())->getConexion();
 
        $query = "SELECT direccion
                FROM pedidos
                WHERE id_usuario = ?
                    AND metodo_envio = 'domicilio'
                ORDER BY fecha_pedido DESC
                LIMIT 1";
 
        $stmt = $conexion->prepare($query);
 
        $stmt->execute([$idUsuario]);
 
        $direccion = $stmt->fetchColumn();
 
        return $direccion !== false ? $direccion : null;
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

    // Trae los N pedidos más recientes (con nombre de cliente) para el dashboard

    public static function ultimos(int $limite = 4): array
    {
        $conexion = (new Conexion())->getConexion();

        // $limite ya está tipado como int por PHP (no viene de un input externo
        // sin validar), así que se puede interpolar directo con seguridad.
        // Se evita bindValue() para LIMIT porque algunas configuraciones de
        // PDO/MySQL lo manejan mal quedándose en silencio con ERRMODE_SILENT.
        $limite = max(1, $limite);

        $query = "
            SELECT
                p.*,
                u.nombre AS cliente
            FROM pedidos p
            INNER JOIN usuarios u
                ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_pedido DESC
            LIMIT {$limite}
        ";

        $stmt = $conexion->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Estadísticas de ventas para el dashboard: facturación, cantidad de
    // pedidos, ticket promedio, pendientes y ventas por día de la última semana.
    // Los pedidos cancelados no se contabilizan como venta real.

    public static function estadisticasVentas(): array
    {
        $conexion = (new Conexion())->getConexion();

        // Todo el resumen (facturado, cantidad, promedio y pendientes) sale
        // de una sola consulta con agregación condicional, para no dejar
        // varios cursores abiertos a la vez sobre la misma conexión.
        $query = "
            SELECT
                COALESCE(SUM(CASE WHEN estado <> 'Cancelado' THEN total END), 0) AS facturado,
                COUNT(CASE WHEN estado <> 'Cancelado' THEN 1 END) AS cantidad_pedidos,
                COALESCE(AVG(CASE WHEN estado <> 'Cancelado' THEN total END), 0) AS ticket_promedio,
                COUNT(CASE WHEN estado = 'Pendiente' THEN 1 END) AS pendientes
            FROM pedidos
        ";

        $stmt = $conexion->query($query);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Ventas por día (últimos 7 días, incluyendo hoy)
        $queryVentasDia = "
            SELECT
                DATE(fecha_pedido) AS dia,
                SUM(total) AS total
            FROM pedidos
            WHERE estado <> 'Cancelado'
                AND fecha_pedido >= (CURDATE() - INTERVAL 6 DAY)
            GROUP BY DATE(fecha_pedido)
        ";

        $stmtVentasDia = $conexion->query($queryVentasDia);
        $filas = $stmtVentasDia->fetchAll(PDO::FETCH_KEY_PAIR);
        $stmtVentasDia->closeCursor();

        // Se completan los días sin ventas con 0 para que el gráfico
        // muestre siempre los últimos 7 días de forma continua.
        $ventasPorDia = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));
            $ventasPorDia[$fecha] = (float) ($filas[$fecha] ?? 0);
        }

        return [
            'facturado'        => (float) $resumen['facturado'],
            'cantidad_pedidos' => (int) $resumen['cantidad_pedidos'],
            'ticket_promedio'  => (float) $resumen['ticket_promedio'],
            'pendientes'       => (int) $resumen['pendientes'],
            'ventas_por_dia'   => $ventasPorDia,
        ];
    }
}