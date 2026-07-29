<?php

/** @var Pedido $pedido */
/** @var Usuario $usuario */
/** @var array $productos */
/** @var array $estadosDisponibles */
/** @var string $success */

$metodoPagoLabels = [
    MetodoPago::TARJETA    => 'Tarjeta de crédito o débito',
    'transferencia'        => 'Transferencia bancaria',
    MetodoPago::EFECTIVO   => 'Efectivo al retirar',
];

$tipoEntregaLabels = [
    MetodoEnvio::DOMICILIO => 'Envío a domicilio',
    MetodoEnvio::RETIRO    => 'Retiro en tienda',
];

$estadoVariantes = [
    strtolower(EstadoPedido::PENDIENTE)  => 'pendiente',
    strtolower(EstadoPedido::PREPARANDO) => 'preparando',
    strtolower(EstadoPedido::ENVIADO)    => 'enviado',
    strtolower(EstadoPedido::ENTREGADO)  => 'entregado',
    strtolower(EstadoPedido::CANCELADO)  => 'cancelado',
];

$estadoActualClave = strtolower(trim($pedido->getEstado()));
$varianteActual = $estadoVariantes[$estadoActualClave] ?? strtolower(EstadoPedido::PENDIENTE);
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
            <a href="index.php?sec=pedidos" class="active">Pedidos</a>

            <h6>SISTEMA</h6>

            <a href="logout.php">Cerrar sesión</a>
        </nav>

    </aside>

    <main class="admin-content">

        <header class="admin-header vinos-header">
            <div>
                <p class="section-label"> — GESTIÓN DE PEDIDOS — </p>
                <h1 class="section-title">
                    Pedido <em>#<?= str_pad((string) $pedido->getId(), 6, '0', STR_PAD_LEFT); ?></em>
                </h1>
            </div>
        </header>

        <?php if ($success === '1'): ?>
        <div class="alert-vinito alert-vinito--success">
            <i class="bi bi-check-circle-fill"></i>
            El estado del pedido se actualizó correctamente.
        </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Detalle del pedido -->
            <div class="col-lg-8">

                <section class="form-panel">

                    <div class="form-section">

                        <h3 class="form-section-title"><i class="bi bi-info-circle"></i> Datos del pedido</h3>

                        <table class="table resumen-table">
                            <tbody>
                                <tr>
                                    <th>Cliente</th>
                                    <td>
                                        <?= htmlspecialchars($usuario->getNombre()); ?>
                                        <br>
                                        <span class="checkout-resumen-direccion"><?= htmlspecialchars($usuario->getEmail()); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha</th>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido->getFechaPedido())); ?> hs</td>
                                </tr>
                                <tr>
                                    <th>Entrega</th>
                                    <td>
                                        <?= htmlspecialchars($tipoEntregaLabels[$pedido->getMetodoEnvio()] ?? $pedido->getMetodoEnvio()); ?>
                                        <br>
                                        <span class="checkout-resumen-direccion"><?= htmlspecialchars($pedido->getDireccion()); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Método de pago</th>
                                    <td>
                                        <?= htmlspecialchars($metodoPagoLabels[$pedido->getMetodoPago()] ?? $pedido->getMetodoPago()); ?>
                                        <?php if ($pedido->getMetodoPago() === MetodoPago::TARJETA && $pedido->getCuotas() > 1): ?>
                                            <br>
                                            <span class="checkout-resumen-direccion">
                                                <?= $pedido->getCuotas(); ?> cuotas
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($pedido->getObservaciones())): ?>
                                <tr>
                                    <th>Observaciones</th>
                                    <td><?= nl2br(htmlspecialchars($pedido->getObservaciones())); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Productos</th>
                                    <td>
                                        <?php foreach ($productos as $item): ?>
                                        <div class="checkout-confirmado-item">
                                            <?= htmlspecialchars($item['cantidad']); ?>x
                                            <?= htmlspecialchars($item['nombre']); ?>
                                            <span class="checkout-confirmado-item-precio">
                                                $ <?= number_format($item['subtotal'], 0, ',', '.'); ?>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Subtotal</th>
                                    <td>$ <?= number_format($pedido->getSubtotal(), 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <th>Envío</th>
                                    <td>
                                        <?= $pedido->getCostoEnvio() > 0
                                            ? '$ ' . number_format($pedido->getCostoEnvio(), 0, ',', '.')
                                            : 'Gratis'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="checkout-confirmado-total">
                                        $ <?= number_format($pedido->getTotal(), 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </section>

            </div>

            <!-- Estado del pedido -->
            <div class="col-lg-4">

                <section class="form-panel">

                    <div class="form-section">

                        <h3 class="form-section-title"><i class="bi bi-arrow-repeat"></i> Estado del pedido</h3>

                        <p class="mb-3">
                            <span class="pedido-status  pedido-status--<?= $varianteActual ?>">
                                <?= htmlspecialchars($pedido->getEstado()); ?>
                            </span>
                        </p>

                        <form method="POST" action="index.php?sec=pedido-ver&id=<?= $pedido->getId(); ?>">

                            <div class="form-group full mb-3">
                                <label class="form-label-custom" for="estado">Actualizar estado</label>
                                <select id="estado" name="estado" class="form-control-custom">
                                    <?php foreach ($estadosDisponibles as $estadoOpcion): ?>
                                    <option
                                        value="<?= htmlspecialchars($estadoOpcion); ?>"
                                        <?= strcasecmp($estadoOpcion, $pedido->getEstado()) === 0 ? 'selected' : ''; ?>
                                    >
                                        <?= htmlspecialchars($estadoOpcion); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn-hero-primary w-100 text-center justify-content-center">
                                Guardar estado &nbsp;<i class="bi bi-check2"></i>
                            </button>

                        </form>

                    </div>

                </section>

            </div>

        </div>

    </main>
</div>
