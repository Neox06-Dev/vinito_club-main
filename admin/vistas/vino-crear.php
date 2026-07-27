<?php

/** @var Categoria[] $categorias */
/** @var Region[] $regiones */
/** @var Varietal[] $varietales */
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
            <a href="index.php?sec=categorias">Categorías</a>
            <a href="index.php?sec=regiones">Regiones</a>
            <a href="index.php?sec=varietales">Varietales</a>
            <a href="index.php?sec=pedidos">Pedidos</a>
            
            <h6>SISTEMA</h6>

            <a href="logout.php">Cerrar sesión</a>
        </nav>

    </aside>

    <main class="admin-content">

        <header class="admin-header vinos-header">
            <div>
                <p class="section-label"> — NUEVO INGRESO AL CATÁLOGO — </p>
                <h1 class="section-title">Agregar <em>vino</em></h1>
            </div>
        </header>

        <!-- Alertas PHP -->
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

        <!-- Alerta JS (Oculta por defecto) -->
        <div class="alert-vinito alert-vinito--error" id="jsAlertVino" style="display: none;">
            <i class="bi bi-exclamation-triangle"></i>
            <span id="jsAlertVinoMsg"></span>
        </div>

        <section class="form-panel">

            <form method="POST" enctype="multipart/form-data" id="vinoForm" novalidate>

                <!-- ── INFORMACIÓN GENERAL ─────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-info-circle"></i> Información general</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control-custom" required>
                            <p class="invalid-msg" id="nombreError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Debes ingresar el nombre del vino.</span>
                            </p>
                        </div>

                        <div class="form-group full">
                            <label class="form-label-custom">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control-custom" rows="4" required></textarea>
                            <p class="invalid-msg" id="descripcionError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>La descripción es obligatoria.</span>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ── PRECIO Y STOCK ───────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-cash-coin"></i> Precio y stock</h3>

                    <div class="form-grid form-grid-3">

                        <div class="form-group">
                            <label class="form-label-custom">Precio</label>
                            <input type="number" step="0.01" name="precio" id="precio" class="form-control-custom" required>
                            <p class="invalid-msg" id="precioError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Ingresa un precio válido.</span>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control-custom" min="0" required>
                            <p class="invalid-msg" id="stockError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Ingresa el stock disponible.</span>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Volumen (ml)</label>
                            <input type="number" name="volumen_ml" id="volumen_ml" min="0" class="form-control-custom" required>
                            <p class="invalid-msg" id="volumenError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Ingresa el volumen en ml.</span>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ── IMAGEN ───────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-image"></i> Imagen</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">Imagen del vino</label>
                            <input type="file" name="imagen" id="imagen" class="form-control-custom" accept=".jpg,.jpeg,.png,.webp" required>
                            <p class="invalid-msg" id="imagenError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Debes seleccionar una imagen.</span>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ── ORIGEN ───────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-geo-alt"></i> Origen</h3>

                    <div class="form-grid form-grid-2">

                        <div class="form-group">
                            <label class="form-label-custom">Bodega</label>
                            <input type="text" name="bodega" id="bodega" class="form-control-custom" required>
                            <p class="invalid-msg" id="bodegaError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>La bodega es obligatoria.</span>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Región</label>
                            <select name="region_id" id="region_id" class="form-control-custom" required>
                                <option value="">Seleccionar región</option>
                                <?php foreach ($regiones as $region): ?>
                                    <option value="<?= $region->getId() ?>">
                                        <?= htmlspecialchars($region->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="invalid-msg" id="regionError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Debes seleccionar una región.</span>
                            </p>
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
                                id="anio_cosecha"
                                class="form-control-custom <?= $error === 'anio_cosecha' ? 'is-invalid-custom' : '' ?>"
                                required>
                            <p class="invalid-msg" id="anioError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Selecciona el año de cosecha.</span>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Temperatura servicio (°C)</label>
                            <input
                                type="number"
                                name="temperatura_servicio"
                                id="temperatura_servicio"
                                class="form-control-custom <?= $error === 'temperatura_servicio' ? 'is-invalid-custom' : '' ?>"
                                min="0"
                                required>
                            <p class="invalid-msg" id="tempError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Ingresa la temperatura.</span>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ── CLASIFICACIÓN ────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-tags"></i> Clasificación</h3>

                    <div class="form-grid form-grid-2">

                        <div class="form-group">
                            <label class="form-label-custom">Categoría</label>

                            <select name="categoria_id" id="categoria_id" class="form-control-custom" required>

                                <?php foreach ($categorias as $categoria): ?>

                                    <option value="<?= $categoria->getId() ?>">
                                        <?= htmlspecialchars($categoria->getNombre()) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Destacado</label>

                            <select name="destacado" id="destacado" class="form-control-custom">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>

                        </div>


                        <div class="form-group">

                            <label for="varietal_id" class="form-label-custom">
                                Varietal
                            </label>

                            <select
                                name="varietal_id"
                                id="varietal_id"
                                class="form-control-custom"
                                required>

                                <option value="">
                                    Seleccionar varietal
                                </option>

                                <?php foreach ($varietales as $varietal): ?>

                                    <option value="<?= $varietal->getId() ?>">
                                        <?= htmlspecialchars($varietal->getNombre()) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                            <p class="invalid-msg" id="varietalError">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Selecciona un varietal.</span>
                            </p>

                        </div>
                    </div>
                </div>

                <!-- ── MARIDAJE ─────────────────────────────────── -->
                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-egg-fried"></i> Maridaje</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom">Sugerencias de maridaje</label>
                            <textarea name="maridaje" id="maridaje" class="form-control-custom" rows="3"></textarea>
                        </div>

                    </div>
                </div>

                <!-- ── ACCIONES ─────────────────────────────────── -->
                <div class="form-actions">

                    <button type="submit" class="btn-hero-primary" id="submitBtnVino">
                        <span id="btnTextVino">Crear vino &nbsp;<i class="bi bi-check2" id="btnIconVino"></i></span>
                        <span class="spinner-border spinner-border-sm" id="btnSpinnerVino" style="display: none;"></span>
                    </button>

                    <a href="index.php?sec=vinos" class="btn-hero-outline">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>
</div>
