<?php
/** @var int $totalVinos */
/** @var int $totalCategorias */
/** @var int $totalVarietales */
/** @var array $ultimosVinos */
/** @var array $estadisticasVentas */
/** @var array $ultimosPedidos */
/** @var array $estadoVariantes */
/** @var array $estadoIconos */
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

        <section class="admin-section-heading">
            <h2>Estadísticas de ventas</h2>
        </section>

        <?php if (!empty($errorVentas)): ?>
        <div class="admin-alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>No se pudieron cargar las estadísticas de ventas.</strong>
                <p><?= htmlspecialchars($errorVentas) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <section class="stats-grid stats-grid--ventas">

            <article class="stat-card stat-card--venta">
                <h2>$ <?= number_format($estadisticasVentas['facturado'], 0, ',', '.') ?></h2>
                <span>Facturado total</span>
            </article>

            <article class="stat-card stat-card--venta">
                <h2><?= $estadisticasVentas['cantidad_pedidos'] ?></h2>
                <span>Pedidos realizados</span>
            </article>

            <article class="stat-card stat-card--venta">
                <h2>$ <?= number_format($estadisticasVentas['ticket_promedio'], 0, ',', '.') ?></h2>
                <span>Promedio de ventas</span>
            </article>

            <article class="stat-card stat-card--venta stat-card--alerta">
                <h2><?= $estadisticasVentas['pendientes'] ?></h2>
                <span>Pedidos pendientes</span>
            </article>

        </section>

        <div class="row g-4 mb-4">

            <!-- Gráfico de ventas -->
            <div class="col-lg-7">
                <section class="ventas-chart-card">
                    <h3>Ventas de los últimos 7 días</h3>
                    <div class="ventas-chart-wrap">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </section>
            </div>

            <!-- Últimos pedidos -->
            <div class="col-lg-5">
                <section class="ultimos-pedidos-card">

                    <div class="ultimos-pedidos-header">
                        <h3>Últimos pedidos</h3>
                        <a href="index.php?sec=pedidos">Ver todos <i class="bi bi-arrow-right"></i></a>
                    </div>

                    <?php if (empty($ultimosPedidos)): ?>

                        <p class="ultimos-pedidos-vacio">Todavía no se registraron pedidos.</p>

                    <?php else: ?>

                        <ul class="ultimos-pedidos-lista">

                            <?php foreach ($ultimosPedidos as $i => $pedido):

                                $estadoClave = strtolower(trim($pedido['estado']));
                                $variante = $estadoVariantes[$estadoClave] ?? strtolower(EstadoPedido::PENDIENTE);
                                $icono = $estadoIconos[$estadoClave] ?? 'bi-hourglass-split';

                            ?>

                            <li style="animation-delay: <?= ($i * 0.07) + 0.1 ?>s">
                                <a href="index.php?sec=pedido-ver&id=<?= $pedido['id_pedido']; ?>" class="ultimo-pedido-item">

                                    <div class="ultimo-pedido-info">
                                        <span class="ultimo-pedido-numero">
                                            #<?= str_pad($pedido['id_pedido'], 6, '0', STR_PAD_LEFT); ?>
                                        </span>
                                        <span class="ultimo-pedido-cliente">
                                            <?= htmlspecialchars($pedido['cliente']); ?>
                                        </span>
                                    </div>

                                    <span class="pedido-status pedido-status--<?= $variante ?>">
                                        <i class="bi <?= $icono ?>"></i>
                                        <?= htmlspecialchars($pedido['estado']); ?>
                                    </span>

                                    <strong class="ultimo-pedido-total">
                                        $ <?= number_format($pedido['total'], 0, ',', '.'); ?>
                                    </strong>

                                </a>
                            </li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>

                </section>
            </div>

        </div>

        <section class="quick-actions">
            <h3>Gestión rápida</h3>
            <div class="d-flex gap-3 flex-wrap mt-4">
                <a href="index.php?sec=vinos" class=" btn-hero-primary">
                    Ver catálogo &nbsp;<i class="bi bi-arrow-right"></i>
                </a>
                <a href="index.php?sec=vino-crear" class=" btn-hero-outline">
                    Agregar vino &nbsp;<i class="bi bi-plus"></i>
                </a>
                <a href="index.php?sec=pedidos" class=" btn-hero-outline">
                    Ver pedidos &nbsp;<i class="bi bi-bag"></i>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // Puente de datos PHP -> JS. La lógica del gráfico vive en js/main.js
    // (bloque "DASHBOARD ADMIN"), esto solo le pasa los datos reales.
    window.ventasPorDiaData = <?= json_encode($estadisticasVentas['ventas_por_dia']) ?>;
</script>

<?php require_once 'includes/footer-admin.php'; ?>
</div>
</html>