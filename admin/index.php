<?php
require_once 'includes/auth.php';
require_once 'includes/header-admin.php';

require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';
require_once '../classes/Varietal.php';

$totalVinos = count(Vino::catalogo_completo());
$totalCategorias = count(Categoria::todas());
$totalVarietales = count(Varietal::todos());

$ultimosVinos = Vino::ultimos();
require_once 'vistas/dashboard.php';
?>