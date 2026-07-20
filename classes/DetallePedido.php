<?php

class DetallePedido
{
    private int $id_detalle;
    private int $id_pedido;
    private int $id_vino;
    private int $cantidad;
    private float $precio_unitario;
    private float $subtotal;

    // Getters

    public function getId(): int
    {
        return $this->id_detalle;
    }

    public function getIdPedido(): int
    {
        return $this->id_pedido;
    }

    public function getIdVino(): int
    {
        return $this->id_vino;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function getPrecioUnitario(): float
    {
        return $this->precio_unitario;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    // Métodos
    public static function crear(
        PDO $conexion,
        int $idPedido,
        array $producto
    ): bool {

        $vino = $producto['vino'];

        $query = "INSERT INTO detalle_pedidos
            (
                id_pedido,
                id_vino,
                cantidad,
                precio_unitario,
                subtotal
            )
            VALUES
            (
                ?, ?, ?, ?, ?
            )";

        $stmt = $conexion->prepare($query);

        return $stmt->execute([
            $idPedido,
            $vino->getIdVino(),
            $producto['cantidad'],
            $vino->getPrecio(),
            $producto['subtotal']
        ]);
    }

    public static function obtenerPorPedido(int $idPedido): array
    {
        $conexion = (new Conexion())->getConexion();

        $query = "SELECT dp.*, v.nombre, v.bodega, v.imagen
                FROM detalle_pedidos dp
                INNER JOIN vinos v
                        ON dp.id_vino = v.id_vino
                WHERE dp.id_pedido = ?";

        $stmt = $conexion->prepare($query);

        $stmt->execute([$idPedido]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}