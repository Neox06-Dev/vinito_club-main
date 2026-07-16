<?php

session_start();

require_once '../classes/Conexion.php';
require_once '../classes/Usuario.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if ($login === '' || $password === '') {
    header('Location: index.php?sec=login&error=campos');
    exit;
}

$usuario = Usuario::buscarPorLogin($login);

if (
    $usuario &&
    $usuario->verificarPassword($password)
) {

    if ($usuario->getRol() !== 'admin') {
        header('Location: index.php?sec=login&error=rol');
        exit;
    }

    $_SESSION['id_usuario'] = $usuario->getId();
    $_SESSION['nombre'] = $usuario->getNombre();
    $_SESSION['rol'] = $usuario->getRol();

    header('Location: index.php?sec=dashboard');
    exit;
}

header('Location: index.php?sec=login&error=credenciales');
exit;