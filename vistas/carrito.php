<?php
require_once 'classes/Carrito.php';

$productos = Carrito::obtenerProductos();
$cantidadProductos = Carrito::obtenerCantidadProductos();
$subtotal = Carrito::obtenerSubtotal();

if (!isset($productos)) {
    $productos = [];
}

$envioGratisDesde = 25000;
$costoEnvioBase = 3500;

$costoEnvio = ($subtotal > 0 && $subtotal < $envioGratisDesde) ? $costoEnvioBase : 0;
$total = $subtotal + $costoEnvio;

?>

<section class="carrito-page py-5">
    <div class="container">
        <!-- Header -->
        <header class="carrito-header d-flex justify-content-between align-items-center mb-5">

            <div>
                <p class="section-label">— TU COMPRA —</p>
                <h1 class="section-title">Mi <em>carrito</em></h1>
            </div>

            <a href="index.php?seccion=tienda" class="btn-hero-outline">
                <i class="bi bi-arrow-left"></i>
                Seguir comprando
            </a>

        </header>

        <div class="row g-4">

            <!-- Productos -->
            <div class="col-lg-8" id="carritoColumnaProductos">

                <?php if (empty($productos)): ?>

                    <div class="carrito-empty text-center">

                        <i class="bi bi-bag-x"></i>
                        <h2>Tu carrito está vacío</h2>
                        <p>Descubrí etiquetas únicas y empezá a armar tu selección.</p>

                        <a href="index.php?seccion=tienda" class="btn-hero-primary">
                            Explorar catálogo
                        </a>

                    </div>

                <?php else: ?>

                    <?php foreach ($productos as $item):

                        $vino = $item['vino'];
                        $cantidad = $item['cantidad'];
                        $subtotalProducto = $item['subtotal'];

                    ?>

                    <article class="carrito-item mb-3" data-id="<?= $vino->getIdVino() ?>" data-precio="<?= $vino->getPrecio() ?>" data-cantidad="<?= $cantidad ?>">

                        <div class="carrito-item-img">

                            <img
                                src="<?= htmlspecialchars($vino->getImagenSrc()) ?>"
                                alt="<?= htmlspecialchars($vino->getNombre()) ?>">

                        </div>

                        <div class="carrito-item-content">

                            <div class="carrito-item-info">

                                <h2 class="carrito-item-title">
                                    <?= htmlspecialchars($vino->getNombre()) ?>
                                </h2>

                                <p class="carrito-item-bodega">
                                    <?= htmlspecialchars($vino->getBodega()) ?>
                                </p>

                                <p class="carrito-item-meta">

                                    <?= htmlspecialchars($vino->getRegionNombre()) ?>

                                    ·

                                    <?php
                                    $varietales = $vino->getVarietales();

                                    echo !empty($varietales)
                                        ? htmlspecialchars($varietales[0]->getNombre())
                                        : 'Sin varietal';
                                    ?>

                                </p>

                            </div>

                            <div class="carrito-item-footer">

                                <div class="carrito-item-precio">

                                    <small>Precio</small>

                                    <strong class="product-price">
                                        <?= $vino->getPrecioFormateado() ?>
                                    </strong>

                                </div>

                                <div class="carrito-item-cantidad">

                                    <button
                                        class="qty-btn qty-minus"
                                        type="button"
                                        data-id="<?= $vino->getIdVino() ?>">

                                        <i class="bi bi-dash"></i>

                                    </button>

                                    <span class="qty-value js-qty-value">

                                        <?= $cantidad ?>

                                    </span>

                                    <button
                                        class="qty-btn qty-plus"
                                        type="button"
                                        data-id="<?= $vino->getIdVino() ?>">

                                        <i class="bi bi-plus"></i>

                                    </button>

                                </div>

                                <div class="carrito-item-subtotal">

                                    <small>Subtotal</small>

                                    <strong class="subtotal-item product-price js-subtotal-item">

                                        $

                                        <?= number_format($subtotalProducto, 0, ',', '.') ?>

                                    </strong>

                                </div>

                            </div>

                        </div>

                        <button
                            class="carrito-item-remove"
                            type="button"
                            data-id="<?= $vino->getIdVino() ?>"
                            aria-label="Eliminar vino">

                            <i class="bi bi-x-lg"></i>
                        </button>

                    </article>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <!-- Resumen -->

            <aside class="col-lg-4">

                <div class="carrito-resumen">

                    <h2>Resumen</h2>

                    <div class="carrito-summary-row">

                        <span>Productos:</span>

                        <span id="resumenCantidad"><?= $cantidadProductos . ' ' . ($cantidadProductos === 1 ? 'vino' : 'vinos') ?></span>

                    </div>

                    <div class="carrito-summary-row">

                        <span>Subtotal:</span>

                        <span id="resumenSubtotal">
                            $
                            <?= number_format($subtotal, 0, ',', '.') ?>
                        </span>

                    </div>

                    <div class="carrito-summary-row">

                        <span>Envío:</span>

                        <span id="resumenEnvio"><?= $costoEnvio > 0 ? '$ ' . number_format($costoEnvio, 0, ',', '.') : 'Gratis' ?></span>

                    </div>

                    <?php if ($costoEnvio > 0): ?>
                    <p class="carrito-envio-nota" id="resumenEnvioNota">
                        Te faltan $ <?= number_format($envioGratisDesde - $subtotal, 0, ',', '.') ?> para envío gratis
                    </p>
                    <?php else: ?>
                    <p class="carrito-envio-nota carrito-envio-nota--gratis" id="resumenEnvioNota" <?= empty($productos) ? 'style="display:none;"' : '' ?>>
                        <i class="bi bi-check-circle-fill"></i> Envío gratis
                    </p>
                    <?php endif; ?>

                    <hr>

                    <div class="carrito-summary-total product-price">
                        <span>Total:</span>
                        <strong id="resumenTotal">$ <?= number_format($total, 0, ',', '.') ?></strong>
                    </div>

                    <button
                        class="btn-hero-outline w-100 mt-3 align-items-center justify-content-center"
                        id="vaciarCarrito">
                        <i class="bi bi-trash3"></i>
                        Vaciar carrito
                    </button>

                    <button class="btn-hero-primary w-100 mt-4 align-items-center justify-content-center" id="finalizarCompra">
                        Finalizar compra
                    </button>

                </div>

            </aside>

        </div>

    </div>

</section>