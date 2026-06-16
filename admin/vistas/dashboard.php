
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

        <header class="admin-header">
            <p class="section-label"> — PANEL DE CONTROL — </p>
            <h1 class="section-title">Bienvenido al club <em><?= htmlspecialchars($_SESSION['nombre']) ?></em></h1>
        </header>

        <section class="stats-grid">

            <article class="stat-card">
                <h2><?= $totalVinos ?></h2>
                <span>Vinos cargados</span>
            </article>

            <article class="stat-card">
                <h2><?= $totalCategorias ?></h2>
                <span>Categorías</span>
            </article>

            <article class="stat-card">
                <h2><?= $totalVarietales ?></h2>
                <span>Varietales</span>
            </article>

        </section>

        <section class="quick-actions">
            <h3>Gestión rápida</h3>
            <div class="d-flex gap-3 flex-wrap mt-4">
                <a href="index.php?sec=vinos" class=" btn-hero-primary">
                    Ver catálogo &nbsp;<i class="bi bi-arrow-right"></i>
                </a>
                <a href="index.php?sec=vino-crear" class=" btn-hero-outline">
                    Agregar vino &nbsp;<i class="bi bi-plus"></i>
                </a>
            </div>
        </section>

        <section class="latest-wines">
            <h3>Últimos vinos agregados</h3>
            <ul>

                <?php foreach($ultimosVinos as $vino): ?>
                    <li>

                        <?= htmlspecialchars(
                            $vino->getNombre()
                        ) ?>

                    </li>
                <?php endforeach; ?>

            </ul>
        </section>
    </main>
</div>

<?php require_once 'includes/footer-admin.php'; ?>
</div>
</html>