<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?seccion=login');
    exit;
}

if (!isset($_SESSION['ultimo_pedido'])) {
    header('Location: index.php?seccion=inicio');
    exit;
}

$pedido = $_SESSION['ultimo_pedido'];
unset($_SESSION['ultimo_pedido']);

$metodoPagoLabels = [
    'tarjeta'        => 'Tarjeta de crédito o débito',
    'transferencia'  => 'Transferencia bancaria',
    'efectivo'       => 'Efectivo al retirar',
];

$tipoEntregaLabels = [
    'domicilio' => 'Envío a domicilio',
    'retiro'    => 'Retiro en tienda',
];

?>

<section class="procesar-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <!-- ÉXITO -->
                <div class="procesar-card text-center">
                    <i class="bi bi-check-circle procesar-icon icon-ok"></i>
                    <h2 class="procesar-title">¡Gracias por tu compra, <?= htmlspecialchars($pedido['nombre']); ?>!</h2>
                    <p class="procesar-sub">
                        Tu pedido <strong class="checkout-pedido-numero"><?= htmlspecialchars($pedido['numero']); ?></strong>
                        fue confirmado. Te enviamos los detalles a
                        <strong><?= htmlspecialchars($pedido['email']); ?></strong>.
                    </p>
                </div>

                <!-- RESUMEN -->
                <div class="procesar-resumen">
                    <h4 class="resumen-titulo">Resumen de tu pedido</h4>

                    <table class="table resumen-table">
                        <tbody>
                            <tr>
                                <th>Número de pedido</th>
                                <td><?= htmlspecialchars($pedido['numero']); ?></td>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <td><?= htmlspecialchars($pedido['fecha']); ?> hs</td>
                            </tr>
                            <tr>
                                <th>Entrega</th>
                                <td>
                                    <?= htmlspecialchars($tipoEntregaLabels[$pedido['tipo_entrega']] ?? $pedido['tipo_entrega']); ?>
                                    <br>
                                    <span class="checkout-resumen-direccion"><?= htmlspecialchars($pedido['direccion']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Método de pago</th>
                                <td>
                                    <?= htmlspecialchars($metodoPagoLabels[$pedido['metodo_pago']] ?? $pedido['metodo_pago']); ?>
                                    <?php if ($pedido['metodo_pago'] === 'tarjeta' && ($pedido['cuotas'] ?? 1) > 1): ?>
                                        <br>
                                        <span class="checkout-resumen-direccion">
                                            <?= (int) $pedido['cuotas']; ?> cuotas de
                                            $ <?= number_format($pedido['total'] / $pedido['cuotas'], 0, ',', '.'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (!empty($pedido['observaciones'])): ?>
                            <tr>
                                <th>Observaciones</th>
                                <td><?= nl2br(htmlspecialchars($pedido['observaciones'])); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Productos</th>
                                <td>
                                    <?php foreach ($pedido['productos'] as $item): ?>
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
                                <td>$ <?= number_format($pedido['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <th>Envío</th>
                                <td>
                                    <?= $pedido['costo_envio'] > 0
                                        ? '$ ' . number_format($pedido['costo_envio'], 0, ',', '.')
                                        : 'Gratis'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td class="checkout-confirmado-total">
                                    $ <?= number_format($pedido['total'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4 d-flex gap-3 justify-content-center flex-wrap">
                    <a href="index.php?seccion=inicio" class="btn btn-hero-primary">
                        <i class="bi bi-house"></i> &nbsp; Volver al inicio
                    </a>
                    <a href="index.php?seccion=tienda" class="btn btn-hero-outline">
                        Seguir comprando <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
