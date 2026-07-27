<?php

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
                <h1 class="section-title">Agregar <em>categoría</em></h1>
            </div>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert-vinito alert-vinito--error">
                <i class="bi bi-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="alert-vinito alert-vinito--error js-alert-hidden" id="jsAlert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span id="jsAlertMsg"></span>
        </div>

        <section class="form-panel">

            <form method="POST" id="categoriaCrearForm">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-info-circle"></i> Información general</h3>

                    <div class="form-grid">

                        <div class="form-group full">
                            <label class="form-label-custom" for="nombre">Nombre</label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control-custom"
                                required
                                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                            <p class="invalid-msg" id="nombreError" role="alert">
                                Ingresá un nombre válido.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="form-actions">

                    <button type="submit" class="btn-hero-primary">
                        Guardar &nbsp;<i class="bi bi-check2"></i>
                    </button>

                    <a href="index.php?sec=categorias" class="btn-hero-outline">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>
</div>