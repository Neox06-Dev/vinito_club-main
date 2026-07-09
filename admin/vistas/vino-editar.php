<?php

/** @var Vino $vino */
/** @var Categoria[] $categorias */
/** @var Varietal[] $varietales */
/** @var int|null $varietalActualId */
/** @var string $error */
?>

<div class="admin-layout">

    <button type="button" class="admin-toggle" id="adminToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="adminSidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="admin-overlay" id="adminOverlay"></div>

    <aside class="admin-sidebar" id="adminSidebar">

        <div class="navbar-brand d-flex justify-content-start">
            <a href="index.php?sec=dashboard"><img src="../assets/img/logo.png" alt="Vinito Club" id="logoImg"></a>
        </div>

        <nav class="admin-nav">

            <h6>GENERAL</h6>

            <a href="index.php?sec=dashboard">Dashboard</a>
            <a href="index.php?sec=vinos">Vinos</a>
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
        <?php elseif ($error === 'region_id'): ?>
            <div class="alert-vinito alert-vinito--error">
                <i class="bi bi-exclamation-triangle"></i>
                Debes seleccionar una región válida antes de guardar.
            </div>
        <?php endif; ?>

        <section class="form-panel">

            <form method="POST" enctype="multipart/form-data">

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
                            <label class="form-label-custom">Imagen del vino</label>
                            <p class="mb-2 hero-sub"> Imagen actual: <strong><?= htmlspecialchars($vino->getImagen()) ?></strong></p>
                            <input type="file" name="imagen" class="form-control-custom" accept=".jpg,.jpeg,.png,.webp">
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
                            <?php $regionSeleccionadaId = $vino->getRegion()?->getId(); ?>
                            <select name="region_id" class="form-control-custom">
                                <option value="">Seleccionar región</option>
                                <?php foreach (Region::todas() as $region): ?>
                                    <option
                                        value="<?= $region->getId() ?>"
                                        <?= $regionSeleccionadaId === $region->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($region->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                                required>

                                <?php foreach ($varietales as $varietal): ?>

                                    <option
                                        value="<?= $varietal->getId() ?>"
                                        <?= $varietal->getId() == $varietalActualId ? 'selected' : '' ?>>
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

                    <a href="index.php?sec=vinos" class="btn-hero-outline">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>
</div>