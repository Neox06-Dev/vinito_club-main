<?php
require_once 'includes/auth.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: vinos.php');
    exit;
}

$vino = Vino::vino_por_id((int)$id);

if (!$vino) {
    header('Location: vinos.php');
    exit;
}

$vino->eliminar();

header('Location: vinos.php');
exit;