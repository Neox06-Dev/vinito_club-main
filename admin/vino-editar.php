<?php

require_once 'includes/auth.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';

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
    $vino->setMaridaje($_POST['maridaje']);
    $vino->setDestacado((int)$_POST['destacado']);
    $vino->setRegion($_POST['region']);

    $vino->editar();

    header('Location: vinos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar vino</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="container py-5">

    <h1 class="mb-4">Editar vino</h1>

    <?php if ($error === 'anio_cosecha'): ?>
        <div class="alert alert-danger">
            Debes completar el año de cosecha antes de guardar.
        </div>
    <?php elseif ($error === 'temperatura_servicio'): ?>
        <div class="alert alert-danger">
            Debes completar la temperatura de servicio antes de guardar.
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input
                type="text"
                name="nombre"
                class="form-control"
                value="<?= htmlspecialchars($vino->getNombre()) ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea
                name="descripcion"
                class="form-control"
                rows="4"
                required><?= htmlspecialchars($vino->getDescripcion()) ?></textarea>
        </div>

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="form-label">Precio</label>
                <input
                    type="number"
                    step="0.01"
                    name="precio"
                    class="form-control"
                    value="<?= $vino->getPrecio() ?>"
                    required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Stock</label>
                <input
                    type="number"
                    name="stock"
                    class="form-control"
                    value="<?= $vino->getStock() ?>"
                    min="0"
                    required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Volumen (ml)</label>
                <input
                    type="number"
                    name="volumen_ml"
                    class="form-control"
                    min="0"
                    value="<?= $vino->getVolumenMl() ?>"
                    required>
            </div>

        </div>

        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input
                type="text"
                name="imagen"
                class="form-control"
                value="<?= htmlspecialchars($vino->getImagen()) ?>"
                required
                placeholder="URL de la imagen">
        </div>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Bodega</label>
                <input
                    type="text"
                    name="bodega"
                    class="form-control"
                    value="<?= htmlspecialchars($vino->getBodega()) ?>"
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Región</label>
                <input
                    type="text"
                    name="region"
                    class="form-control"
                    value="<?= htmlspecialchars($vino->getRegion()) ?>"
                    required>
            </div>

        </div>

        <div class="mb-3">
            <label class="form-label">Año cosecha</label>
            <input
                type="date"
                name="anio_cosecha"
                class="form-control"
                value="<?= htmlspecialchars($vino->getAnioCosecha()) ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Temperatura servicio</label>
            <input
                type="number"
                name="temperatura_servicio"
                class="form-control"
                min="0"
                value="<?= htmlspecialchars((string)$vino->getTemperaturaServicio()) ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoría</label>

            <select name="categoria_id" class="form-select" required>

                <?php foreach ($categorias as $categoria): ?>

                    <option
                        value="<?= $categoria->getId() ?>"
                        <?= $categoria->getId() == $vino->getCategoriaId() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria->getNombre()) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="mb-3">
            <label class="form-label">Maridaje</label>
            <textarea
                name="maridaje"
                class="form-control"
                rows="3"
                required><?= htmlspecialchars($vino->getMaridaje()) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Destacado</label>

            <select name="destacado" class="form-select" required>
                <option value="1" <?= $vino->getDestacado() ? 'selected' : '' ?>>
                    Sí
                </option>
                <option value="0" <?= !$vino->getDestacado() ? 'selected' : '' ?>>
                    No
                </option>
            </select>

        </div>

        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>

        <a href="vinos.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</body>

</html>