<?php
require_once 'includes/auth.php';
require_once 'includes/header-admin.php';

require_once '../classes/Vino.php';
require_once '../classes/Categoria.php';
require_once '../classes/Varietal.php';

$totalVinos = count(Vino::catalogo_completo());
$totalCategorias = count(Categoria::todas());
$totalVarietales = count(Varietal::todos());

$ultimosVinos = Vino::ultimos();

?>

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div class="navbar-brand d-flex justify-content-start">
            <a href="index.php"><img src="../assets/img/logo.png" alt="Vinito Club" id="logoImg"></a>
        </div>

        <nav class="admin-nav">

            <h6>GENERAL</h6>

            <a href="index.php">
                Dashboard
            </a>

            <a href="vinos.php">
                Vinos
            </a>

            <h6>SISTEMA</h6>

            <a href="logout.php">
                Cerrar sesión
            </a>

        </nav>

    </aside>

    <main class="admin-content">

        <header class="admin-header">

            <p class="section-label">
                PANEL DE CONTROL
            </p>

            <h1 class="section-title">
                Bienvenido al <em>club</em>
            </h1>

            <p>
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </p>

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
                    <a href="vinos.php" class=" btn-hero-primary">
                        Ver catálogo &nbsp;<i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="vino-crear.php" class=" btn-hero-outline">
                        Agregar vino &nbsp;<i class="bi bi-plus"></i>
                    </a>
                </div>

        </section>

        <section class="latest-wines">

            <h3>
                Últimos vinos agregados
            </h3>

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