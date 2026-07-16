<?php

session_start();

require_once '../../classes/Conexion.php';
require_once '../../classes/Usuario.php';

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../index.php?seccion=login');
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

Usuario::eliminar($id_usuario);

// Cerrar la sesión del cliente (misma lógica que logout.php)
unset($_SESSION['id_usuario']);
unset($_SESSION['nombre']);
unset($_SESSION['email']);
unset($_SESSION['rol']);

header('Location: ../../index.php?seccion=login&success=cuenta-eliminada');
exit;
