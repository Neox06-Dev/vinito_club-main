<?php

session_start();

require_once '../../classes/Conexion.php';
require_once '../../classes/Usuario.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?seccion=login');
    exit;
}

// Obtener y limpiar datos
$password = $_POST['password'] ?? '';
$login = trim($_POST['login'] ?? '');

// Validar campos vacíos
if ($login === '' || $password === '') {
    header('Location: ../../index.php?seccion=login&error=campos');
    exit;
}

// Buscar usuario (por email o por nombre)
$usuario = Usuario::buscarPorLogin($login);

// Verificar usuario y contraseña
if (!$usuario || !$usuario->verificarPassword($password)) {
    header('Location: ../../index.php?seccion=login&error=credenciales');
    exit;
}

// Verificar que sea un cliente
if ($usuario->getRol() !== 'cliente') {
    header('Location: ../../index.php?seccion=login&error=rol');
    exit;
}

// Crear sesión
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

// Redireccionar
header('Location: ../../index.php');
exit;