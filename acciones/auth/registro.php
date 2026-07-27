<?php

session_start();

require_once '../../classes/Conexion.php';
require_once '../../classes/Usuario.php';

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?seccion=registro');
    exit;
}

// Obtener datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

// Validaciones
if (
    $nombre === '' ||
    $email === '' ||
    $password === '' ||
    $password2 === ''
) {
    header('Location: ../../index.php?seccion=registro&error=campos');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../index.php?seccion=registro&error=email');
    exit;
}

if ($password !== $password2) {
    header('Location: ../../index.php?seccion=registro&error=password');
    exit;
}

if (Usuario::emailExiste($email)) {
    header('Location: ../../index.php?seccion=registro&error=existe');
    exit;
}

// Registrar
if (!Usuario::registrar($nombre, $email, $telefono, $password)) {
    header('Location: ../../index.php?seccion=registro&error=registro');
    exit;
}

// Obtener el usuario recién creado
$usuario = Usuario::buscarPorEmail($email);

// Login automático
$_SESSION['id_usuario'] = $usuario->getId();
$_SESSION['nombre'] = $usuario->getNombre();
$_SESSION['email'] = $usuario->getEmail();
$_SESSION['rol'] = $usuario->getRol();

// Solo crear la cookie si el usuario marcó "Recordar mi sesión"
if (isset($_POST['remember']) && $_POST['remember'] === '1') {
    setcookie(
        'recordar_usuario',
        (string) $usuario->getId(),
        time() + (60 * 60 * 24 * 30),
        '/'
    );
}

// Redirección
header('Location: ../../index.php?success=registro');
exit;