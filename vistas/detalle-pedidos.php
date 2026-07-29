<?php

require_once 'classes/Pedido.php';
require_once 'classes/DetallePedido.php';
require_once 'classes/Usuario.php';
require_once 'config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idPedido = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($idPedido <= 0) {
    header('Location: index.php?seccion=inicio');
    exit;
}

$datos = Pedido::obtenerPedidoCompleto($idPedido);

if ($datos === null) {
    header('Location: index.php?seccion=inicio');
    exit;
}

$pedido = $datos['pedido'];
$usuario = $datos['usuario'];
$productos = $datos['productos'];

$metodoPagoLabels = [
    MetodoPago::TARJETA    => 'Tarjeta de crédito o débito',
    'transferencia'        => 'Transferencia bancaria',
    MetodoPago::EFECTIVO   => 'Efectivo al retirar',
];

$tipoEntregaLabels = [
    MetodoEnvio::DOMICILIO => 'Envío a domicilio',
    MetodoEnvio::RETIRO    => 'Retiro en tienda',
];

if ($pedido->getIdUsuario() !== $_SESSION['id_usuario']) {
    header('Location: index.php?seccion=inicio');
    exit;
}

?>

<section class="procesar-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <!-- RESUMEN -->
                <div class="procesar-resumen">
                    <h4 class="resumen-titulo">Resumen de tu pedido</h4>

                    <table class="table resumen-table">
                        <tbody>
                            <tr>
                                <th>Número de pedido</th>
                                <td>#<?= str_pad($pedido->getId(), 6, '0', STR_PAD_LEFT); ?></td>
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
                                    <?php if ($pedido->getMetodoPago() === MetodoPago::TARJETA): ?>
                                        <br>
                                    <tr>
                                        <th>Cuotas</th>
                                        <td><?= $pedido->getCuotas(); ?> cuota<?= $pedido->getCuotas() > 1 ? 's' : ''; ?></td>
                                    </tr>

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
                                        <a href="index.php?seccion=detalle&id=<?= $item['id_vino']; ?>" class="text-decoration-none text-white-50">
                                            <?= htmlspecialchars($item['nombre']); ?>
                                        </a>
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

                <div class="text-center mt-4 d-flex gap-3 justify-content-center flex-wrap">
                    <a href="index.php?seccion=mis-pedidos" class="btn btn-hero-primary d-flex flex-fill justify-content-center">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <a href="index.php?seccion=tienda" class="btn btn-hero-outline flex-fill d-flex justify-content-center">
                        <i class="bi bi-shop"></i> Ir a la tienda
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
