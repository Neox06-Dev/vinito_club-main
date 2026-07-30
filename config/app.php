<?php

/*
    Constantes generales de la app: estados de pedido, métodos de pago
    y métodos de envío. Se centralizan acá para no repetir los mismos
    strings sueltos en varios archivos.
*/

// Estados posibles de un pedido (coincide con el ENUM de la tabla `pedidos`)
class EstadoPedido
{
    const PENDIENTE  = 'Pendiente';
    const PREPARANDO = 'Preparando';
    const ENVIADO    = 'Enviado';
    const ENTREGADO  = 'Entregado';
    const CANCELADO  = 'Cancelado';

    // Todos los estados válidos, en el orden en que se muestran en el admin
    const TODOS = [
        self::PENDIENTE,
        self::PREPARANDO,
        self::ENVIADO,
        self::ENTREGADO,
        self::CANCELADO,
    ];
}

// Métodos de pago disponibles al hacer checkout
class MetodoPago
{
    const TARJETA   = 'tarjeta';
    const EFECTIVO  = 'efectivo';
}

// Métodos de envío disponibles al hacer checkout
class MetodoEnvio
{
    const DOMICILIO = 'domicilio';
    const RETIRO    = 'retiro';
}
