<?php
require_once 'includes/auth.php';
require_once 'includes/header-admin.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';

$error = $_GET['error'] ?? '';

$vinos = Vino::catalogo_completo();

require_once 'vistas/vinos.php';

require_once 'includes/footer-admin.php';
?>