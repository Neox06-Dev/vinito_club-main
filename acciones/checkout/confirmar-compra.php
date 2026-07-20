<?php

session_start();

require_once '../../classes/Conexion.php';
require_once '../../classes/Usuario.php';
require_once '../../classes/Carrito.php';
require_once '../../classes/Pedido.php';

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../index.php?seccion=login&error=checkout');
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?seccion=checkout');
    exit;
}

$productos = Carrito::obtenerProductos();

if (empty($productos)) {
    header('Location: ../../index.php?seccion=carrito');
    exit;
}

// Obtener y sanitizar datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$tipoEntrega = $_POST['tipo_entrega'] ?? 'domicilio';
$metodoPago = $_POST['metodo_pago'] ?? 'tarjeta';
$observaciones = trim($_POST['observaciones'] ?? '');

$cuotas = (int) ($_POST['cuotas'] ?? 1);
if ($metodoPago !== 'tarjeta' || $cuotas < 1 || $cuotas > 6) {
    $cuotas = 1;
}

$calle = trim($_POST['calle'] ?? '');
$ciudad = trim($_POST['ciudad'] ?? '');
$codigoPostal = trim($_POST['codigo_postal'] ?? '');
$referencia = trim($_POST['referencia'] ?? '');

$subtotal = (float) ($_POST['subtotal'] ?? 0);
$costoEnvio = (float) ($_POST['costo_envio'] ?? 0);
$total = (float) ($_POST['total'] ?? 0);

// Validaciones básicas
if ($nombre === '' || $email === '' || $telefono === '') {
    header('Location: ../../index.php?seccion=checkout&error=campos');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../index.php?seccion=checkout&error=campos');
    exit;
}

// Si es envío a domicilio, la dirección es obligatoria
if ($tipoEntrega === 'domicilio' && ($calle === '' || $ciudad === '' || $codigoPostal === '')) {
    header('Location: ../../index.php?seccion=checkout&error=direccion');
    exit;
}

$direccion = $tipoEntrega === 'domicilio'
    ? trim($calle . ', ' . $ciudad . ' (CP ' . $codigoPostal . ')' . ($referencia !== '' ? ' - ' . $referencia : ''))
    : 'Retiro en tienda — Av. del Libertador 1234, CABA';

Pedido::crear(
    $_SESSION['id_usuario'],
    $metodoPago,
    $cuotas,
    $tipoEntrega,
    $direccion,
    $observaciones,
    $subtotal,
    $costoEnvio,
    $total,
    $productos
);

$numeroPedido = 'VC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

$_SESSION['ultimo_pedido'] = [
    'numero'        => $numeroPedido,
    'fecha'         => date('d/m/Y H:i'),
    'nombre'        => $nombre,
    'email'         => $email,
    'telefono'      => $telefono,
    'tipo_entrega'  => $tipoEntrega,
    'direccion'     => $direccion,
    'metodo_pago'   => $metodoPago,
    'cuotas'        => $cuotas,
    'observaciones' => $observaciones,
    'productos'     => array_map(function ($item) {
        return [
            'nombre'   => $item['vino']->getNombre(),
            'bodega'   => $item['vino']->getBodega(),
            'cantidad' => $item['cantidad'],
            'subtotal' => $item['subtotal'],
        ];
    }, $productos),
    'subtotal'      => $subtotal,
    'costo_envio'   => $costoEnvio,
    'total'         => $total,
];

$idPedido = Pedido::crear(
    $_SESSION['id_usuario'],
    $metodoPago,
    $cuotas,
    $tipoEntrega,
    $direccion,
    $observaciones,
    $subtotal,
    $costoEnvio,
    $total,
    $productos
);

Carrito::vaciar();

header('Location: ../../index.php?seccion=pedido-confirmado&id=' . $idPedido);
exit;