<?php

require_once 'includes/auth.php';
require_once 'includes/header-admin.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';
require_once '../classes/Varietal.php';

$categorias = Categoria::todas();
$varietales = Varietal::todas();
$regiones = Region::todas();
$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $volumenMl = trim($_POST['volumen_ml'] ?? '');
    $bodega = trim($_POST['bodega'] ?? '');
    $regionId = trim($_POST['region_id'] ?? '');
    $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
    $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');
    $categoriaId = trim($_POST['categoria_id'] ?? '');
    $varietalId = trim($_POST['varietal_id'] ?? '');
    $imagenSubida = $_FILES['imagen']['name'] ?? '';

    if ($nombre === '') {
        header('Location: vino-crear.php?error=nombre');
        exit;
    }

    if ($descripcion === '') {
        header('Location: vino-crear.php?error=descripcion');
        exit;
    }

    if ($precio === '') {
        header('Location: vino-crear.php?error=precio');
        exit;
    }

    if ($stock === '') {
        header('Location: vino-crear.php?error=stock');
        exit;
    }

    if ($volumenMl === '') {
        header('Location: vino-crear.php?error=volumen_ml');
        exit;
    }

    if ($imagenSubida === '') {
        header('Location: vino-crear.php?error=imagen');
        exit;
    }

    if ($bodega === '') {
        header('Location: vino-crear.php?error=bodega');
        exit;
    }

    if ($regionId === '') {
        header('Location: vino-crear.php?error=region_id');
        exit;
    }

    if ($anioCosecha === '') {
        header('Location: vino-crear.php?error=anio_cosecha');
        exit;
    }

    if ($temperaturaServicio === '') {
        header('Location: vino-crear.php?error=temperatura_servicio');
        exit;
    }

    if ($categoriaId === '') {
        header('Location: vino-crear.php?error=categoria_id');
        exit;
    }

    if ($varietalId === '') {
        header('Location: vino-crear.php?error=varietal_id');
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
    $vino->setRegionId((int)$_POST['region_id']);
    $vino->setVarietalId((int)$_POST['varietal_id']);
    $vino->crear();

    header('Location: vinos.php');
    exit;
}
?>