<?php

require_once __DIR__ . '/Vino.php';

class Carrito
{
    /*Inicializa el carrito en la sesión.*/
    private static function inicializar(): void
    {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    /* Agrega un vino al carrito.*/
    public static function agregar(int $idVino): bool
    {
        self::inicializar();

        // Validar que exista el vino
        $vino = Vino::vino_por_id($idVino);

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

    public static function contarProductos(): int
    {
        self::inicializar();

        return array_sum($_SESSION['carrito']);
    }
}
