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
?>

<div class="admin-layout">

    <button type="button" class="admin-toggle" id="adminToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="adminSidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="admin-overlay" id="adminOverlay"></div>

    <aside class="admin-sidebar" id="adminSidebar">

        <div class="navbar-brand d-flex justify-content-start">
            <a href="index.php"><img src="../assets/img/logo.png" alt="Vinito Club" id="logoImg"></a>
        </div>

        <nav class="admin-nav">

            <h6>GENERAL</h6>

            <a href="index.php">Dashboard</a>
            <a href="vinos.php">Vinos</a>
            <h6>SISTEMA</h6>

            <a href="logout.php">Cerrar sesión</a>
        </nav>

    </aside>

    <main class="admin-content">

        <header class="admin-header vinos-header">
            <div>
                <p class="section-label"> — EDITANDO <?= htmlspecialchars(mb_strtoupper($vino->getNombre())) ?> — </p>
                <h1 class="section-title">Editar <em>vino</em></h1>
            </div>
        </header>

        <?php if ($error === 'anio_cosecha'): ?>
            <div class="alert-vinito alert-vinito--error">
                <i class="bi bi-exclamation-triangle"></i>
                Debes completar el año de cosecha antes de guardar.
            </div>
        <?php elseif ($error === 'temperatura_servicio'): ?>
            <div class="alert-vinito alert-vinito--error">
                <i class="bi bi-exclamation-triangle"></i>
                Debes completar la temperatura de servicio antes de guardar.
            </div>
        <?php endif; ?>

        <section class="form-panel">

            <form method="POST">

                <!-- ── INFORMACIÓN GENERAL ─────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-info-circle"></i> Información general</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">Nombre</label>
                            <input
                                type="text"
                                name="nombre"
                                class="form-control-custom"
                                value="<?= htmlspecialchars($vino->getNombre()) ?>"
                                required>
                        </div>

                        <div class="form-group full">
                            <label class="form-label-custom">Descripción</label>
                            <textarea
                                name="descripcion"
                                class="form-control-custom"
                                rows="4"
                                required><?= htmlspecialchars($vino->getDescripcion()) ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- ── PRECIO Y STOCK ───────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-cash-coin"></i> Precio y stock</h3>

                    <div class="form-grid form-grid-3">

                        <div class="form-group">
                            <label class="form-label-custom">Precio</label>
                            <input
                                type="number"
                                step="0.01"
                                name="precio"
                                class="form-control-custom"
                                value="<?= $vino->getPrecio() ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Stock</label>
                            <input
                                type="number"
                                name="stock"
                                class="form-control-custom"
                                value="<?= $vino->getStock() ?>"
                                min="0"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Volumen (ml)</label>
                            <input
                                type="number"
                                name="volumen_ml"
                                class="form-control-custom"
                                min="0"
                                value="<?= $vino->getVolumenMl() ?>"
                                required>
                        </div>

                    </div>
                </div>

                <!-- ── IMAGEN ───────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-image"></i> Imagen</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">URL de la imagen</label>
                            <input
                                type="text"
                                name="imagen"
                                class="form-control-custom"
                                value="<?= htmlspecialchars($vino->getImagen()) ?>"
                                placeholder="https://..."
                                required>
                        </div>

                    </div>
                </div>

                <!-- ── ORIGEN ───────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-geo-alt"></i> Origen</h3>

                    <div class="form-grid form-grid-2">

                        <div class="form-group">
                            <label class="form-label-custom">Bodega</label>
                            <input
                                type="text"
                                name="bodega"
                                class="form-control-custom"
                                value="<?= htmlspecialchars($vino->getBodega()) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Región</label>
                            <input
                                type="text"
                                name="region"
                                class="form-control-custom"
                                value="<?= htmlspecialchars($vino->getRegion()) ?>"
                                required>
                        </div>

                    </div>
                </div>

                <!-- ── DETALLES TÉCNICOS ────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-thermometer-half"></i> Detalles técnicos</h3>

                    <div class="form-grid form-grid-2">

                        <div class="form-group">
                            <label class="form-label-custom">Año cosecha</label>
                            <input
                                type="date"
                                name="anio_cosecha"
                                class="form-control-custom <?= $error === 'anio_cosecha' ? 'is-invalid-custom' : '' ?>"
                                value="<?= htmlspecialchars($vino->getAnioCosecha()) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Temperatura servicio (°C)</label>
                            <input
                                type="number"
                                name="temperatura_servicio"
                                class="form-control-custom <?= $error === 'temperatura_servicio' ? 'is-invalid-custom' : '' ?>"
                                min="0"
                                value="<?= htmlspecialchars((string)$vino->getTemperaturaServicio()) ?>"
                                required>
                        </div>

                    </div>
                </div>

                <!-- ── CLASIFICACIÓN ────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-tags"></i> Clasificación</h3>

                    <div class="form-grid form-grid-2">

                        <div class="form-group">
                            <label class="form-label-custom">Categoría</label>

                            <select name="categoria_id" class="form-control-custom" required>

                                <?php foreach ($categorias as $categoria): ?>

                                    <option
                                        value="<?= $categoria->getId() ?>"
                                        <?= $categoria->getId() == $vino->getCategoriaId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->getNombre()) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>
                        
                        <div class="form-group">

                            <label class="form-label-custom">
                                Varietal
                            </label>

                            <select
                                name="varietal_id"
                                class="form-control-custom"
                                required
                            >

                                <?php foreach ($varietales as $varietal): ?>

                                    <option
                                        value="<?= $varietal->getIdVarietal() ?>"
                                        <?= $varietal->getIdVarietal() == $varietalActualId ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($varietal->getNombre()) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Destacado</label>

                            <select name="destacado" class="form-control-custom" required>
                                <option value="1" <?= $vino->getDestacado() ? 'selected' : '' ?>>
                                    Sí
                                </option>
                                <option value="0" <?= !$vino->getDestacado() ? 'selected' : '' ?>>
                                    No
                                </option>
                            </select>

                        </div>

                    </div>
                </div>

                <!-- ── MARIDAJE ─────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-egg-fried"></i> Maridaje</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">Sugerencias de maridaje</label>
                            <textarea
                                name="maridaje"
                                class="form-control-custom"
                                rows="3"
                                required><?= htmlspecialchars($vino->getMaridaje()) ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- ── ACCIONES ─────────────────────────────────── -->
                <div class="form-actions">

                    <button type="submit" class="btn-hero-primary">
                        Guardar cambios &nbsp;<i class="bi bi-check2"></i>
                    </button>

                    <a href="vinos.php" class="btn-hero-outline">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>
</div>

<?php require_once 'includes/footer-admin.php'; ?>
</div>
</html>