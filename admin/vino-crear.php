<?php

require_once 'includes/auth.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';

$categorias = Categoria::todas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $vino = new Vino();

    $vino->setNombre($_POST['nombre']);
    $vino->setDescripcion($_POST['descripcion']);
    $vino->setPrecio((float)$_POST['precio']);
    $vino->setStock((int)$_POST['stock']);
    $vino->setImagen($_POST['imagen']);
    $vino->setAnioCosecha($_POST['anio_cosecha']);
    $vino->setVolumenMl((int)$_POST['volumen_ml']);
    $vino->setTemperaturaServicio($_POST['temperatura_servicio']);
    $vino->setBodega($_POST['bodega']);
    $vino->setCategoriaId((int)$_POST['categoria_id']);
    $vino->setMaridaje($_POST['maridaje']);
    $vino->setDestacado((int)$_POST['destacado']);
    $vino->setRegion($_POST['region']);

    $vino->crear();

    header('Location: vinos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear vino</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="container py-5">

<h1 class="mb-4">Nuevo vino</h1>

<form method="POST">

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="4"></textarea>
    </div>

    <div class="row">

        <div class="col-md-4 mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Volumen (ml)</label>
            <input type="number" name="volumen_ml" class="form-control">
        </div>

    </div>

    <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="text" name="imagen" class="form-control">
    </div>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">Bodega</label>
            <input type="text" name="bodega" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Región</label>
            <input type="text" name="region" class="form-control">
        </div>

    </div>

    <div class="mb-3">
        <label class="form-label">Año cosecha</label>
        <input type="date" name="anio_cosecha" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Temperatura servicio</label>
        <input type="text" name="temperatura_servicio" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Categoría</label>

        <select name="categoria_id" class="form-select">

            <?php foreach($categorias as $categoria): ?>

                <option value="<?= $categoria->getId() ?>">
                    <?= htmlspecialchars($categoria->getNombre()) ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">
        <label class="form-label">Maridaje</label>
        <textarea name="maridaje" class="form-control" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Destacado</label>

        <select name="destacado" class="form-select">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>

    </div>

    <button type="submit" class="btn btn-success">
        Crear vino
    </button>

    <a href="vinos.php" class="btn btn-secondary">
        Cancelar
    </a>

</form>

</body>
</html>