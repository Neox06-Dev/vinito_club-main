<?php

require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/header-admin.php';

$seccion = $_GET['sec'] ?? 'dashboard';

if (!in_array($seccion, admin_secciones_validas())) {
    $seccion = 'dashboard';
}

switch ($seccion) {

    case 'dashboard':

        require_once '../classes/Vino.php';
        require_once '../classes/Categoria.php';
        require_once '../classes/Varietal.php';

        $totalVinos = count(Vino::catalogo_completo());
        $totalCategorias = count(Categoria::todas());
        $totalVarietales = count(Varietal::todos());

        $ultimosVinos = Vino::ultimos();

        require_once 'vistas/dashboard.php';

        break;

    case 'vinos':

    require_once '../classes/Conexion.php';
    require_once '../classes/Vino.php';

    $vinos = Vino::catalogo_completo();

    require_once 'vistas/vinos.php';

    break;

    case 'vino-crear':

    require_once '../classes/Conexion.php';
    require_once '../classes/Vino.php';
    require_once '../classes/Categoria.php';
    require_once '../classes/Varietal.php';

    $categorias = Categoria::todas();
    $varietales = Varietal::todos();

    $error = $_GET['error'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // POR AHORA NO MOVEMOS EL POST
        // lo dejamos en vino-crear.php

    }

    require_once 'vistas/vino-crear.php';

    break;

    case 'vino-editar':

    require_once '../classes/Conexion.php';
    require_once '../classes/Vino.php';
    require_once '../classes/Categoria.php';
    require_once '../classes/Varietal.php';

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: index.php?sec=vinos');
        exit;
    }

    $vino = Vino::vino_por_id((int)$id);

    if (!$vino) {
        header('Location: index.php?sec=vinos');
        exit;
    }

    $categorias = Categoria::todas();
    $varietales = Varietal::todos();

    $varietalesActuales = $vino->getVarietales();

    $varietalActualId = !empty($varietalesActuales)
        ? $varietalesActuales[0]->getIdVarietal()
        : null;

    $error = $_GET['error'] ?? '';

    require_once 'vistas/vino-editar.php';

    break;

    default:
        require_once 'vistas/dashboard.php';
}

require_once 'includes/footer-admin.php';