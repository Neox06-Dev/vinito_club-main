<?php

require_once __DIR__ . '/Vino.php';

class Carrito
{
    /**
     * Inicializa el carrito en la sesión.
     */
    private static function inicializar(): void
    {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    /**
     * Obtiene un vino por su ID.
     */
    private static function obtenerVino(int $idVino): ?Vino
    {
        return Vino::vino_por_id($idVino);
    }

    /**
     * Agrega un vino al carrito.
     */
    public static function agregar(int $idVino): bool
    {
        self::inicializar();

        $vino = self::obtenerVino($idVino);

        if (!$vino) {
            return false;
        }

        if (isset($_SESSION['carrito'][$idVino])) {
            $_SESSION['carrito'][$idVino]++;
        } else {
            $_SESSION['carrito'][$idVino] = 1;
        }

        return true;
    }

    /**
     * Devuelve todos los productos del carrito.
     */
    public static function obtenerProductos(): array
    {
        self::inicializar();

        $productos = [];

        foreach ($_SESSION['carrito'] as $idVino => $cantidad) {

            $vino = self::obtenerVino($idVino);

            if (!$vino) {
                continue;
            }

            $productos[] = [
                'vino'      => $vino,
                'cantidad'  => $cantidad,
                'subtotal'  => $vino->getPrecio() * $cantidad
            ];
        }

        return $productos;
    }

    /**
     * Devuelve la cantidad total de productos del carrito.
     */
    public static function obtenerCantidadProductos(): int
    {
        self::inicializar();

        return array_sum($_SESSION['carrito']);
    }

    /**
     * Devuelve el subtotal del carrito.
     */
    public static function obtenerSubtotal(): float
    {
        $subtotal = 0;

        foreach (self::obtenerProductos() as $item) {
            $subtotal += $item['subtotal'];
        }

        return $subtotal;
    }

    /**
     * Aumenta una unidad de un producto.
     */
    public static function sumar(int $idVino): bool
    {
        self::inicializar();

        if (!self::obtenerVino($idVino)) {
            return false;
        }

        if (!isset($_SESSION['carrito'][$idVino])) {
            return false;
        }

        $_SESSION['carrito'][$idVino]++;

        return true;
    }

    /**
     * Disminuye una unidad de un producto.
     */
    public static function restar(int $idVino): bool
    {
        self::inicializar();

        if (!self::obtenerVino($idVino)) {
            return false;
        }

        if (!isset($_SESSION['carrito'][$idVino])) {
            return false;
        }

        $_SESSION['carrito'][$idVino]--;

        if ($_SESSION['carrito'][$idVino] <= 0) {
            unset($_SESSION['carrito'][$idVino]);
        }

        return true;
    }

    /**
     * Elimina un producto del carrito.
     */
    public static function eliminar(int $idVino): bool
    {
        self::inicializar();

        if (!self::obtenerVino($idVino)) {
            return false;
        }

        if (!isset($_SESSION['carrito'][$idVino])) {
            return false;
        }

        unset($_SESSION['carrito'][$idVino]);

        return true;
    }

    /**
     * Vacía completamente el carrito.
     */
    public static function vaciar(): void
    {
        self::inicializar();

        $_SESSION['carrito'] = [];
    }
}