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
    // public static function crear()
    // {

    // }

    // public static function buscarPorPedido(int $idPedido): array
    // {

    // }
}