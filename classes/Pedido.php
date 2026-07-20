<?php

class Pedido
{
    private int $id_pedido;
    private int $id_usuario;
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

    // Métodos
    // public static function crear()
    // {

    // }

    // public static function buscarPorId(int $id): ?Pedido
    // {

    // }

    // public static function buscarPorUsuario(int $idUsuario): array
    // {

    // }

    // public static function actualizarEstado(int $idPedido, string $estado): bool
    // {

    // }
}