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

    // Solo crear la cookie si el admin marcó "Recordar mi sesión"
    if (isset($_POST['remember']) && $_POST['remember'] === '1') {
        setcookie(
            'recordar_admin',
            (string) $usuario->getId(),
            time() + (60 * 60 * 24 * 30),
            '/'
        );
    }

    header('Location: index.php?sec=dashboard');
    exit;
}

header('Location: index.php?sec=login&error=credenciales');
exit;