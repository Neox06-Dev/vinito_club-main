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
    header('Location: ../../index.php?seccion=seguridad');
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

$actual = $_POST['password_actual'] ?? '';
$nueva = $_POST['password_nueva'] ?? '';
$confirmar = $_POST['password_confirmar'] ?? '';

// Validaciones
if ($actual === '' || $nueva === '' || $confirmar === '') {
    header('Location: ../../index.php?seccion=seguridad&error=campos');
    exit;
}

if (strlen($nueva) < 6) {
    header('Location: ../../index.php?seccion=seguridad&error=longitud');
    exit;
}

if ($nueva !== $confirmar) {
    header('Location: ../../index.php?seccion=seguridad&error=coincide');
    exit;
}

$usuario = Usuario::buscarPorId($id_usuario);

if (!$usuario || !$usuario->verificarPassword($actual)) {
    header('Location: ../../index.php?seccion=seguridad&error=actual');
    exit;
}

if ($nueva === $actual) {
    header('Location: ../../index.php?seccion=seguridad&error=igual');
    exit;
}

if (!Usuario::actualizarPassword($id_usuario, $nueva)) {
    header('Location: ../../index.php?seccion=seguridad&error=actualizar');
    exit;
}

header('Location: ../../index.php?seccion=seguridad&success=password');
exit;
