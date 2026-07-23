<?php

session_start();

require_once '../../classes/Conexion.php';
require_once '../../classes/Usuario.php';

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../index.php?seccion=login');
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?seccion=editar-direccion');
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

// Obtener y sanitizar datos
$calle        = trim($_POST['calle'] ?? '');
$ciudad       = trim($_POST['ciudad'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$referencia   = trim($_POST['referencia'] ?? '');

// Validaciones
if ($calle === '' || $ciudad === '' || $codigo_postal === '') {
    header('Location: ../../index.php?seccion=editar-direccion&error=campos');
    exit;
}

if (strlen($calle) < 3) {
    header('Location: ../../index.php?seccion=editar-direccion&error=calle');
    exit;
}

if (strlen($ciudad) < 2) {
    header('Location: ../../index.php?seccion=editar-direccion&error=ciudad');
    exit;
}

// Armar la dirección formateada (mismo formato que el checkout)
$direccion = trim(
    $calle . ', ' . $ciudad . ' (CP ' . $codigo_postal . ')' .
    ($referencia !== '' ? ' - ' . $referencia : '')
);

// Actualizar en la base de datos
if (!Usuario::actualizarDireccion($id_usuario, $direccion)) {
    header('Location: ../../index.php?seccion=editar-direccion&error=actualizar');
    exit;
}

// Redirección con éxito
header('Location: ../../index.php?seccion=mi-cuenta&success=direccion');
exit;
