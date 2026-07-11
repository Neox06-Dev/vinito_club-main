<?php

session_start();

header('Content-Type: application/json');

require_once '../../classes/Carrito.php';

$idVino = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$idVino) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de producto inválido.'
    ]);
    exit;
}

$success = Carrito::sumar($idVino);

echo json_encode([
    'success'  => $success,
    'cantidad' => Carrito::obtenerCantidadProductos(),
    'subtotal' => Carrito::obtenerSubtotal()
]);