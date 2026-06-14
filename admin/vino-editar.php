<?php

require_once 'includes/auth.php';
require_once 'includes/header-admin.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';
require_once '../classes/Varietal.php';

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
        header('Location: vino-editar.php?id=' . (int)$id . '&error=anio_cosecha');
        exit;
    }

    if ($temperaturaServicio === '') {
        header('Location: vino-editar.php?id=' . (int)$id . '&error=temperatura_servicio');
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

    header('Location: vinos.php');
    exit;
}
require_once 'vistas/vino-editar.php';

require_once 'includes/footer-admin.php';
?>