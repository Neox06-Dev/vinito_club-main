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
                <p class="section-label"> — GESTIÓN DE CATÁLOGO — </p>
                <h1 class="section-title">Administrar <em>Regiones</em></h1>
            </div>

            <a href="index.php?sec=region-crear" class="btn-hero-primary">
                Nueva región &nbsp;<i class="bi bi-plus"></i>
            </a>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert-vinito alert-vinito--error">
                <i class="bi bi-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <section class="table-panel">
            <div class="table-responsive">
                <table class="table-vinos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (empty($regiones)): ?>

                        <tr>
                            <td colspan="7" class="vinos-empty">No hay regiones cargadas todavía.</td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($regiones as $i => $region): ?>

                            <tr>

                                <td data-label="ID" class="vino-id">#<?= $region->getId() ?></td>

                                <td data-label="Nombre" class="vino-nombre"><?= htmlspecialchars($region->getNombre()) ?></td>

                                <td data-label="Acciones" class="text-end">
                                    <div class="acciones-grupo">

                                        <a
                                            href="index.php?sec=region-editar&id=<?= $region->getId() ?>"
                                            class="btn-accion btn-editar"
                                            title="Editar"
                                        >
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>

                                        <button
                                            type="button"
                                            class="btn-accion btn-eliminar"
                                            data-href="index.php?sec=region-eliminar&id=<?= $region->getId() ?>"
                                            data-nombre="<?= htmlspecialchars($region->getNombre()) ?>"
                                            title="Eliminar"
                                        >
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>
            </div>
        </section>
    </main>
</div>

<!-- ── MODAL DE CONFIRMACIÓN: ELIMINAR VINO ───────────────────── -->
<div class="vinito-overlay" id="deleteOverlay"></div>
<div class="vinito-modal" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="vinito-modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <h3 id="deleteModalTitle">¿Eliminar región?</h3>
    <p>Estás a punto de eliminar <strong id="deleteModalNombre"></strong> del catálogo. Esta acción no se puede deshacer.</p>
    <div class="vinito-modal-actions">
        <button type="button" class="btn-hero-outline" id="deleteCancelar">Cancelar</button>
        <a href="#" id="deleteConfirmar" class="btn-confirm-eliminar">Sí, eliminar</a>
    </div>
</div>
