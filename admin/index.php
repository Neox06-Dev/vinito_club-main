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
        $totalVarietales = count(Varietal::todas());

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
        require_once '../classes/Region.php';

        $categorias = Categoria::todas();
        $regiones = Region::todas();
        $varietales = Varietal::todas();

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

            $regionId = (int)($_POST['region_id'] ?? 0);

            if ($regionId <= 0 || Region::porId($regionId) === null) {
                header('Location: index.php?sec=vino-crear&error=region_id');
                exit;
            }

            $vino = new Vino();

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $nombreImagen = null;
            if (!empty($_FILES['imagen']['name'])) {

                $nombreImagen = uniqid() . '-' . basename($_FILES['imagen']['name']);

                move_uploaded_file(
                    $_FILES['imagen']['tmp_name'],
                    '../assets/img/productos/' . $nombreImagen
                );
            }
            $vino->setImagen($nombreImagen);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegionId($regionId);
            $vino->setVarietalId((int)$_POST['varietal_id']);

            if (!$vino->crear()) {
                header('Location: index.php?sec=vino-crear&error=region_id');
                exit;
            }

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
        require_once '../classes/Region.php';

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
        $regiones = Region::todas();
        $varietales = Varietal::todas();

        $varietalesActuales = $vino->getVarietales();

        $varietalActualId = !empty($varietalesActuales)
            ? $varietalesActuales[0]->getId()
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

            $regionId = (int)($_POST['region_id'] ?? 0);

            if ($regionId <= 0 || Region::porId($regionId) === null) {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=region_id');
                exit;
            }

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $nombreImagen = $vino->getImagen();
            if (!empty($_FILES['imagen']['name'])) {

                $imagenVieja = '../assets/img/productos/' . $vino->getImagen();

                if (
                    file_exists($imagenVieja)
                    && is_file($imagenVieja)
                ) {
                    unlink($imagenVieja);
                }

                $nombreImagen = uniqid() . '-' . basename($_FILES['imagen']['name']);

                move_uploaded_file(
                    $_FILES['imagen']['tmp_name'],
                    '../assets/img/productos/' . $nombreImagen
                );
            }

            $vino->setImagen($nombreImagen);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setVarietalId((int)$_POST['varietal_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegionId($regionId);

            if (!$vino->editar()) {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=region_id');
                exit;
            }

            header('Location: index.php?sec=vinos');
            exit;
        }

        require_once 'vistas/vino-editar.php';

        break;

    case 'categorias':

        require_once '../classes/Categoria.php';
        
        $categorias = Categoria::todas();

        require_once 'vistas/categorias.php';

        break;
    
    case 'categoria-crear':

        require_once '../classes/Categoria.php';

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria = new Categoria();

            $categoria->setNombre($_POST['nombre']);

            if ($categoria->crear()) {

                header('Location: index.php?sec=categorias');
                exit;
            }

            $error = $categoria->getError();
        }

        require_once 'vistas/categoria-crear.php';

        break;

    case 'categoria-editar':

        require_once '../classes/Categoria.php';

        $id = (int)($_GET['id'] ?? 0);

        $categoria = Categoria::porId($id);

        if (!$categoria) {
            header('Location: index.php?sec=categorias');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria->setNombre($_POST['nombre']);

            if ($categoria->editar()) {

                header('Location: index.php?sec=categorias');
                exit;
            }

            $error = $categoria->getError();
        }

        require_once 'vistas/categoria-editar.php';

        break;  
    
    case 'categoria-eliminar':

        require_once '../classes/Categoria.php';

        $id = (int)($_GET['id'] ?? 0);

        $categoria = Categoria::porId($id);

        if (!$categoria->eliminar()) {

            header(
                'Location: index.php?sec=categorias&error=' .
                urlencode($categoria->getError())
            );

            exit;
        }

        header('Location: index.php?sec=categorias');
        exit;

    default:
        header('Location: index.php?sec=dashboard');
        exit;
}

require_once 'includes/footer-admin.php';
