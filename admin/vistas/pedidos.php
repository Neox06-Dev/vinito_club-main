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
                <p class="section-label"> — GESTIÓN DE PEDIDOS — </p>
                <h1 class="section-title">Administrar <em>pedidos</em></h1>
            </div>
        </header>

        <section class="table-panel">
            <div class="table-responsive">
                <table class="table-vinos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (empty($pedidos)): ?>

                        <tr>
                            <td colspan="6" class="vinos-empty">No hay pedidos cargados todavía.</td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($pedidos as $i => $pedido): ?>
                            <tr style="animation-delay: <?= ($i * 0.05) + 0.15 ?>s">

                                <td>
                                    #<?= str_pad($pedido['id_pedido'], 6, '0', STR_PAD_LEFT); ?>
                                </td>

                                <td class="vino-nombre">
                                    <?= htmlspecialchars($pedido['cliente']); ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($pedido['fecha_pedido'])
                                    ); ?>
                                </td>

                                <td>

                                    <?php
                                        $estadoClave = strtolower(trim($pedido['estado']));
                                        $variante = $estadoVariantes[$estadoClave] ?? strtolower(EstadoPedido::PENDIENTE);
                                        $icono = $estadoIconos[$estadoClave] ?? 'bi-hourglass-split';
                                    ?>

                                    <span class="pedido-status pedido-status--<?= $variante ?>">
                                        <i class="bi <?= $icono ?>"></i>
                                        <?= htmlspecialchars($pedido['estado']); ?>
                                    </span>

                                </td>

                                <td>
                                    $
                                    <?= number_format($pedido['total'], 0, ',', '.'); ?>
                                </td>

                                <td class="text-end">
                                <a href="index.php?sec=pedido-ver&id=<?= $pedido['id_pedido']; ?>" class="btn-accion btn-editar">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
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