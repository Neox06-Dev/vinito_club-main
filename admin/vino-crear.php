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

    $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
    $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');

    if ($anioCosecha === '') {
        header('Location: vino-crear.php?error=anio_cosecha');
        exit;
    }

    if ($temperaturaServicio === '') {
        header('Location: vino-crear.php?error=temperatura_servicio');
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