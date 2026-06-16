<?php
require_once 'includes/functions.php';

$seccion = $_GET['sec'] ?? 'dashboard';

if ($seccion !== 'login') {
    require_once 'includes/auth.php';
}

require_once 'includes/header-admin.php';

switch ($seccion) {

    case 'login':

        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        require_once 'vistas/login.php';

        break;

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

            $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
            $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');

            if ($anioCosecha === '') {
                header('Location: index.php?sec=vino-crear&error=anio_cosecha');
                exit;
            }

            if ($temperaturaServicio === '') {
                header('Location: index.php?sec=vino-crear&error=temperatura_servicio');
                exit;
            }

            $vino = new Vino();

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $vino->setImagen($_POST['imagen']);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegion($_POST['region']);
            $vino->setVarietalId((int)$_POST['varietal_id']);

            $vino->crear();

            header('Location: index.php?sec=vinos');
            exit;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
            $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');

            if ($anioCosecha === '') {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=anio_cosecha');
                exit;
            }

            if ($temperaturaServicio === '') {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=temperatura_servicio');
                exit;
            }

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $vino->setImagen($_POST['imagen']);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setVarietalId((int)$_POST['varietal_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegion($_POST['region']);

            $vino->editar();

            header('Location: index.php?sec=vinos');
            exit;
        }

        require_once 'vistas/vino-editar.php';

        break;

    default:
        header('Location: index.php?sec=dashboard');
        exit;
}

require_once 'includes/footer-admin.php';