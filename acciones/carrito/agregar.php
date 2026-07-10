<?php

session_start();

require_once __DIR__ . '/../../classes/Carrito.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Producto inválido.'
    ]);
    exit;
}

$agregado = Carrito::agregar($id);

if (!$agregado) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró el producto.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Producto agregado al carrito.',
    'count'   => Carrito::contarProductos(),
]);

exit;
