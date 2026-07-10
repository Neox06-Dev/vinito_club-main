<?php

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$producto = Vino::vino_por_id($id);

if (!$producto) {
    header('Location: index.php?seccion=tienda');
    exit;
}
// Si no existe el producto, redirigir a tienda
if (!$producto) {
    header('Location: index.php?seccion=tienda');
    exit;
}

// Productos relacionados (misma categoría, distintos id, máx 3)
$relacionados = array_filter(Vino::catalogo_completo(), fn($p) => $p->getCategoriaId() === $producto->getCategoriaId() && $p->getIdVino() !== $producto->getIdVino());
$relacionados = array_slice(array_values($relacionados), 0, 3);

$categoriaClase = strtolower($producto->getCategoriaLabel());
$categoriaClase = match ($categoriaClase) {
    'tinto' => 'tinto',
    'blanco' => 'blanco',
    'rosé' => 'rose',
    'espumante' => 'espumante',
    'dulce' => 'dulce',
    default => 'especial'
};
?>

<!-- DETALLE -->
<section class="detalle-section">
    <div class="container">
        <div class="row g-5 align-items-start">

            <!-- IMAGEN -->
            <div class="col-lg-5">
                <div class="detalle-img-wrap">
                    <span class="product-badge badge-<?= $categoriaClase ?> detalle-badge">
                        <?= $producto->getCategoriaLabel() ?>
                    </span>
                    <img src="<?= htmlspecialchars($producto->getImagenSrc()) ?>"
                        alt="<?= htmlspecialchars($producto->getNombre()) ?>"
                        class="detalle-img">
                </div>
            </div>

            <!-- INFO -->
            <div class="col-lg-7">
                <p class="detalle-meta">
                    <strong><?= htmlspecialchars($producto->getBodega()) ?></strong>
                    &nbsp;·&nbsp; <?= htmlspecialchars($producto->getRegionNombre()) ?>
                    &nbsp;·&nbsp; Cosecha <?= $producto->getAnio() ?>
                </p>

                <h1 class="detalle-title">
                    <?= htmlspecialchars($producto->getNombre()) ?>
                </h1>
                <?php $varietales = $producto->getVarietales(); ?>

                <p class="detalle-varietal">
                    <?= !empty($varietales)
                        ? htmlspecialchars($varietales[0]->getNombre())
                        : 'Sin varietal'; ?>
                </p>

                <p class="detalle-precio"><?= $producto->getPrecioFormateado() ?></p>

                <?php if ($producto->estaEnStock()): ?>
                    <p class="detalle-stock stock-ok">
                        <i class="bi bi-check-circle-fill"></i> En stock (<?= $producto->getStock() ?> unidades)
                    </p>
                <?php else: ?>
                    <p class="detalle-stock stock-no">
                        <i class="bi bi-x-circle-fill"></i> Sin stock
                    </p>
                <?php endif; ?>

                <p class="detalle-desc"><?= htmlspecialchars($producto->getDescripcion()) ?></p>

                <!-- SPECS -->
                <div class="detalle-specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Bodega</span>
                        <span class="spec-val"><?= htmlspecialchars($producto->getBodega()) ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Varietal</span>
                        <span class="spec-val">
                            <?= !empty($varietales)
                                ? htmlspecialchars($varietales[0]->getNombre())
                                : 'Sin varietal'; ?>
                        </span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Región</span>
                        <span class="spec-val"><?= htmlspecialchars($producto->getRegionNombre()) ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Año de cosecha</span>
                        <span class="spec-val"><?= date('d/m/Y', strtotime($producto->getAnioCosecha())) ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Volumen</span>
                        <span class="spec-val"><?= $producto->getVolumenMl() ?> ml</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Temperatura de servicio</span>
                        <span class="spec-val"><?= $producto->getTemperaturaServicio() ?>°C</span>
                    </div>
                    <div class="spec-item spec-full">
                        <span class="spec-label">Maridaje</span>
                        <span class="spec-val"><?= htmlspecialchars($producto->getMaridaje()) ?></span>
                    </div>
                </div>

                <!-- ACCIONES -->
                <div class="detalle-acciones d-flex gap-3 flex-wrap">
                    <?php if ($producto->estaEnStock()): ?>
                        <button class="btn btn-hero-primary btn-detalle-comprar js-agregar-carrito"
                            type="button"
                            data-id="<?= $producto->getIdVino() ?>">
                            <i class="bi bi-bag-plus"></i> &nbsp; Agregar al carrito
                        </button>
                    <?php else: ?>
                        <button class="btn btn-hero-primary" disabled>Sin stock</button>
                    <?php endif; ?>
                    <a href="index.php?seccion=tienda" class="btn btn-hero-outline">
                        <i class="bi bi-arrow-left"></i> &nbsp; Volver a la tienda
                    </a>
                </div>
            </div>
        </div>

        <!-- RELACIONADOS -->
        <?php if (!empty($relacionados)): ?>
            <div class="relacionados-section mt-5 pt-4">
                <h3 class="subsection-title mb-4">También te puede interesar</h3>
                <div class="row g-4">
                    <?php foreach ($relacionados as $r): ?>
                        <div class="col-lg-4 col-md-6">
                            <article class="product-card">
                                <a href="index.php?seccion=detalle&id=<?= $r->getIdVino() ?>" class="product-card-link">
                                    <div class="product-card-img-wrap">
                                        <img src="<?= htmlspecialchars($r->getImagenSrc()) ?>"
                                            alt="<?= htmlspecialchars($r->getNombre()) ?>"
                                            class="product-card-img">

                                        <?php
                                        $categoria = $r->getCategoria();

                                        $claseBadge = match ($categoria->getNombre()) {
                                            'Tinto'      => 'tinto',
                                            'Blanco'     => 'blanco',
                                            'Rosé'       => 'rose',
                                            'Espumante'  => 'espumante',
                                            'Dulce'      => 'dulce',
                                            default      => 'especial'
                                        };
                                        ?>

                                        <span class="product-badge badge-<?= $claseBadge ?>">
                                            <?= htmlspecialchars($categoria->getNombre()) ?>
                                        </span>
                                    </div>
                                    <div class="product-card-body">
                                        <p class="product-meta"><?= htmlspecialchars($r->getRegionNombre()) ?> · <?= $r->getAnio() ?></p>
                                        <h3 class="product-name"><?= htmlspecialchars($r->getNombre()) ?></h3>
                                        <p class="product-varietal"><?= htmlspecialchars($r->getVarietales()[0]->getNombre()) ?></p>
                                        <div class="product-card-footer">
                                            <span class="product-price"><?= $r->getPrecioFormateado() ?></span>
                                            <span
                                                class="product-agregar js-agregar-carrito"
                                                role="button"
                                                tabindex="0"
                                                data-id="<?= $r->getIdVino() ?>"
                                                aria-label="Agregar al carrito">
                                                <i class="bi bi-bag-plus"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>