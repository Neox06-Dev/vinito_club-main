<?php

session_start();

require_once '../classes/Conexion.php';
require_once '../classes/Usuario.php';

$email = $_POST['email'];
$password = $_POST['password'];

$usuario = Usuario::buscarPorEmail($email);

if (
    $usuario &&
    $usuario->verificarPassword($password)
) {

    $_SESSION['id_usuario'] = $usuario->getId();
    $_SESSION['nombre'] = $usuario->getNombre();
    $_SESSION['rol'] = $usuario->getRol();

    header('Location: index.php');
    exit;
}

header('Location: login.php');