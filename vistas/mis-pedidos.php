<?php

require_once 'classes/Pedido.php';
require_once 'classes/DetallePedido.php';

$pedidos = Pedido::buscarPorUsuario(
    $_SESSION['id_usuario']
);

// Estados de pedido -> variante visual del badge
$estadoVariantes = [
    'pendiente'   => 'pendiente',
    'preparando'  => 'preparando',
    'enviado'     => 'enviado',
    'entregado'   => 'entregado',
    'cancelado'   => 'cancelado',
];

$estadoIconos = [
    'pendiente'   => 'bi-hourglass-split',
    'preparando'  => 'bi-box-seam',
    'enviado'     => 'bi-truck',
    'entregado'   => 'bi-check-circle',
    'cancelado'   => 'bi-x-circle',
];

// date() no traduce nombres de mes según locale, así que se arma a mano
$mesesAbreviados = [
    1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr',
    5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago',
    9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic',
];

function formatearFechaPedido(string $fecha, array $meses): string
{
    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[(int) date('n', $timestamp)];
    $anio = date('Y', $timestamp);

    return "{$dia} {$mes} {$anio}";
}

?>

<section class="account-page py-5">

    <div class="container">

        <!-- Header -->
        <header class="mb-5 account-page-header">
            <p class="section-label">— HISTORIAL —</p>

            <h1 class="section-title">
                Mis <em>pedidos</em>
            </h1>

            <p class="account-subtitle">
                Revisá el estado y el detalle de todas tus compras en Vinito Club.
            </p>
        </header>

        <?php if (empty($pedidos)): ?>

            <div class="account-module">
                <div class="account-empty-state py-4">

                    <div class="account-empty-icon">
                        <i class="bi bi-bag-x"></i>
                    </div>

                    <h3 class="account-empty-title">Aún no realizaste ningún pedido</h3>

                    <p class="account-empty-text mb-4">
                        Cuando compres un vino, vas a poder seguir su estado
                        y ver el detalle de la compra desde acá.
                    </p>

                    <a href="index.php?seccion=tienda" class="btn-hero-primary">
                        <i class="bi bi-shop"></i>
                        Ir a la tienda
                    </a>

                </div>
            </div>

        <?php else: ?>

            <div class="pedidos-lista">

                <?php foreach ($pedidos as $pedido):

                    $items = DetallePedido::obtenerPorPedido($pedido->getId());
                    $primerItem = $items[0] ?? null;
                    $cantidadItems = count($items);

                    $estadoClave = strtolower(trim($pedido->getEstado()));
                    $variante = $estadoVariantes[$estadoClave] ?? 'pendiente';
                    $icono = $estadoIconos[$estadoClave] ?? 'bi-hourglass-split';

                ?>

                <article class="pedido-card">

                    <div class="pedido-thumb">
                        <?php if ($primerItem): ?>
                        <img
                            src="assets/img/productos/<?= htmlspecialchars($primerItem['imagen']) ?>"
                            alt="<?= htmlspecialchars($primerItem['nombre']) ?>"
                            onerror="this.closest('.pedido-thumb').classList.add('pedido-thumb--fallback')">
                        <i class="bi bi-bag pedido-thumb-fallback-icon"></i>
                        <?php else: ?>
                        <i class="bi bi-bag"></i>
                        <?php endif; ?>

                        <?php if ($cantidadItems > 1): ?>
                        <span class="pedido-thumb-badge">+<?= $cantidadItems - 1 ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="pedido-info">

                        <p class="pedido-meta">
                            <span class="pedido-numero">#<?= str_pad((string) $pedido->getId(), 6, '0', STR_PAD_LEFT) ?></span>
                            <span class="pedido-meta-dot">·</span>
                            <span><?= formatearFechaPedido($pedido->getFechaPedido(), $mesesAbreviados) ?></span>
                        </p>

                        <h3 class="pedido-titulo">
                            <?= $primerItem ? htmlspecialchars($primerItem['nombre']) : 'Pedido #' . $pedido->getId() ?>
                        </h3>

                        <?php if ($cantidadItems > 1): ?>
                        <p class="pedido-extra">
                            + <?= $cantidadItems - 1 ?> producto<?= $cantidadItems - 1 === 1 ? '' : 's' ?> más
                        </p>
                        <?php endif; ?>

                        <span class="pedido-status mt-2 pedido-status--<?= $variante ?>">
                            <i class="bi <?= $icono ?>"></i>
                            <?= htmlspecialchars($pedido->getEstado()) ?>
                        </span>

                    </div>

                    <div class="pedido-footer">

                        <strong class="pedido-price">
                            $ <?= number_format($pedido->getTotal(), 0, ',', '.') ?>
                        </strong>

                        <a href="index.php?seccion=pedido-confirmado&id=<?= $pedido->getId() ?>" class="pedido-btn-detalle">
                            <i class="bi bi-eye"></i>
                            Detalles
                        </a>

                    </div>

                </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
