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
    header('Location: ../../index.php?seccion=editar-perfil');
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

// Obtener datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$telefono = trim($_POST['telefono'] ?? '');

// Validaciones
if ($nombre === '' || $email === '') {
    header('Location: ../../index.php?seccion=editar-perfil&error=campos');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../index.php?seccion=editar-perfil&error=email');
    exit;
}

if (Usuario::emailExisteParaOtro($email, $id_usuario)) {
    header('Location: ../../index.php?seccion=editar-perfil&error=existe');
    exit;
}

// Actualizar
if (!Usuario::actualizarPerfil($id_usuario, $nombre, $email, $telefono)) {
    header('Location: ../../index.php?seccion=editar-perfil&error=actualizar');
    exit;
}

// Sincronizar datos de la sesión
$_SESSION['nombre'] = $nombre;
$_SESSION['email'] = $email;

// Redirección
header('Location: ../../index.php?seccion=mi-cuenta&success=perfil');
exit;
