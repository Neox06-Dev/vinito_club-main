<?php
require_once __DIR__ . '/../classes/Usuario.php';

session_start();

if (
    !isset($_SESSION['id_usuario']) &&
    isset($_COOKIE['recordar_usuario'])
) {

    $usuario = Usuario::buscarPorId(
        (int) $_COOKIE['recordar_usuario']
    );

    if ($usuario) {
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nombre'] = $usuario->getNombre();
        $_SESSION['rol'] = $usuario->getRol();
    }
}