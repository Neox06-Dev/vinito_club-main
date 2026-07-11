<?php

session_start();

header('Content-Type: application/json');

require_once '../../classes/Carrito.php';

Carrito::vaciar();

echo json_encode([
    'success'  => true,
    'cantidad' => 0,
    'subtotal' => 0
]);