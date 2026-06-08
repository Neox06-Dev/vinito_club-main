<?php
$todos = Vino::catalogo_completo();

$filtro_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';
$filtro_region    = isset($_GET['region'])    ? $_GET['region']    : 'todos';
$filtro_varietal  = isset($_GET['varietal'])  ? $_GET['varietal']  : 'todos';
$filtro_precio    = isset($_GET['precio'])    ? $_GET['precio']    : 'todos';
$ordenar          = isset($_GET['ordenar'])   ? $_GET['ordenar']   : 'destacados';

// Aplicar filtros
$productos = array_filter($todos, function ($p) use ($filtro_categoria, $filtro_region, $filtro_varietal, $filtro_precio) {

    // Categoría
    if ($filtro_categoria !== 'todos') {
        $categoria = strtolower($p->getCategoria()->getNombre());

        if ($categoria !== strtolower($filtro_categoria)) {
            return false;
        }
    }

    // Región
    if (
        $filtro_region !== 'todos'
        && $p->getRegion() !== $filtro_region
    ) {
        return false;
    }

    if ($filtro_varietal !== 'todos') {

        $coincide = false;

        foreach ($p->getVarietales() as $varietal) {

            if ($varietal->getNombre() === $filtro_varietal) {
                $coincide = true;
                break;
            }
        }

        if (!$coincide) {
            return false;
        }
    }
    // Precio
    if (
        $filtro_precio === 'hasta15'
        && $p->getPrecio() > 15000
    ) {
        return false;
    }

    if (
        $filtro_precio === '15a25'
        && ($p->getPrecio() <= 15000 || $p->getPrecio() > 25000)
    ) {
        return false;
    }

    if (
        $filtro_precio === 'mas25'
        && $p->getPrecio() <= 25000
    ) {
        return false;
    }

    return true;
});

// Ordenar
$productos = array_values($productos);
usort($productos, function ($a, $b) use ($ordenar) {
    switch ($ordenar) {
        case 'precio-asc':
            return $a->getPrecio() <=> $b->getPrecio();

        case 'precio-desc':
            return $b->getPrecio() <=> $a->getPrecio();

        case 'nombre-az':
            return strcmp($a->getNombre(), $b->getNombre());

        case 'anio-desc':
            return strcmp($b->getAnio(), $a->getAnio());
        case 'destacados':
        default:
            return $b->getDestacado() <=> $a->getDestacado();
    }
});

// Valores únicos para los filtros (siempre sobre el total)
$regiones  = array_unique(array_map(fn($p) => $p->getRegion(),   $todos));
$varietales = [];

foreach ($todos as $vino) {
    foreach ($vino->getVarietales() as $varietal) {
        $varietales[] = $varietal->getNombre();
    }
}

$varietales = array_unique($varietales);

sort($regiones);
sort($varietales);

// Construir URL de filtro manteniendo los demás parámetros activos
function filtroUrl(string $param, string $valor): string
{
    $parametros = $_GET;
    $parametros[$param] = $valor;
    unset($parametros['seccion']);
    return 'index.php?seccion=tienda&' . http_build_query($parametros);
}
?>

<section class="tienda-section" id="tienda">
    <div class="container-fluid px-4">

        <div class="tienda-header mb-4">
            <div class="section-label">— Tienda —</div>
            <h2 class="section-title">Nuestros <em>vinos</em></h2>
            <p class="tienda-sub"><?= count($productos) ?> de <?= count($todos) ?> etiquetas</p>
        </div>

        <div class="row">

            <!-- ── PANEL DE FILTROS (PHP GET) ── -->
            <aside class="col-lg-2 col-md-3 tienda-sidebar">
                <div class="filtros-panel">

                    <!-- TIPO DE VINO -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">TIPO DE VINO</h6>
                        <div class="filtro-pills">
                            <?php
                            $categorias = [
                                'todos' => 'Todos',
                                'tinto' => 'Tinto',
                                'blanco' => 'Blanco',
                                'rosé' => 'Rosé',
                                'espumante' => 'Espumante',
                                'dulce' => 'Dulce'
                            ];
                            foreach ($categorias as $val => $label):
                                $activo = $filtro_categoria === $val ? 'active' : '';
                            ?>
                                <a href="<?= filtroUrl('categoria', $val) ?>" class="pill-btn <?= $activo ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ORIGEN -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">ORIGEN</h6>
                        <div class="filtro-pills">
                            <a href="<?= filtroUrl('region', 'todos') ?>"
                                class="pill-btn <?= $filtro_region === 'todos' ? 'active' : '' ?>">Todas</a>
                            <?php foreach ($regiones as $r): ?>
                                <a href="<?= filtroUrl('region', $r) ?>"
                                    class="pill-btn <?= $filtro_region === $r ? 'active' : '' ?>">
                                    <?= htmlspecialchars($r) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- VARIEDAD -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">VARIEDAD</h6>
                        <div class="filtro-pills">
                            <a href="<?= filtroUrl('varietal', 'todos') ?>"
                                class="pill-btn <?= $filtro_varietal === 'todos' ? 'active' : '' ?>">Todas</a>
                            <?php foreach ($varietales as $v): ?>
                                <a href="<?= filtroUrl('varietal', $v) ?>"
                                    class="pill-btn <?= $filtro_varietal === $v ? 'active' : '' ?>">
                                    <?= htmlspecialchars($v) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- PRECIO -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">PRECIO</h6>
                        <div class="filtro-pills">
                            <?php
                            $precios = [
                                'todos'   => 'Todos',
                                'hasta15' => 'Hasta $15.000',
                                '15a25'   => '$15.000 — $25.000',
                                'mas25'   => 'Más de $25.000',
                            ];
                            foreach ($precios as $val => $label):
                                $activo = $filtro_precio === $val ? 'active' : '';
                            ?>
                                <a href="<?= filtroUrl('precio', $val) ?>" class="pill-btn <?= $activo ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- LIMPIAR -->
                    <a href="index.php?seccion=tienda" class="btn-limpiar">
                        <i class="bi bi-x-circle"></i> Limpiar filtros
                    </a>

                </div>
            </aside>

            <!-- ── GRID DE PRODUCTOS ── -->
            <div class="col-lg-10 col-md-9">

                <!-- Toolbar ordenar -->
                <div class="tienda-toolbar d-flex justify-content-between align-items-center mb-4">
                    <span class="toolbar-count"><?= count($productos) ?> etiquetas</span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="toolbar-label">ORDENAR</label>
                        <select class="form-select toolbar-select"
                            onchange="window.location.href=this.value">
                            <?php
                            $opciones = [
                                'destacados'  => 'Destacados',
                                'precio-asc'  => 'Precio: menor a mayor',
                                'precio-desc' => 'Precio: mayor a menor',
                                'nombre-az'   => 'Nombre: A–Z',
                                'anio-desc'   => 'Más reciente',
                            ];
                            foreach ($opciones as $val => $label):
                                $url = filtroUrl('ordenar', $val);
                                $sel = $ordenar === $val ? 'selected' : '';
                            ?>
                                <option value="<?= htmlspecialchars($url) ?>" <?= $sel ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Grid -->
                <?php if (empty($productos)): ?>
                    <div class="no-results text-center py-5">
                        <i class="bi bi-funnel" style="font-size:2.5rem; color: var(--color-gold);"></i>
                        <p class="mt-3">No se encontraron vinos con esos filtros.</p>
                        <a href="index.php?seccion=tienda" class="btn btn-outline-gold mt-2">Limpiar filtros</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4" id="productosGrid">
                        <?php foreach ($productos as $p): ?>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <article class="product-card">
                                    <a href="index.php?seccion=detalle&id=<?= $p->getIdVino() ?>" class="product-card-link">
                                        <div class="product-card-img-wrap">

                                            <img src="<?= htmlspecialchars($p->getImagenSrc()) ?>"
                                                alt="<?= htmlspecialchars($p->getNombre()) ?>"
                                                class="product-card-img">

                                            <?php
                                            $categoria = $p->getCategoria();
                                            $slugCategoria = strtolower($categoria->getNombre());

                                            if ($slugCategoria === 'rosé') {
                                                $slugCategoria = 'rose';
                                            }
                                            ?>

                                            <span class="product-badge badge-<?= $slugCategoria ?>">
                                                <?= htmlspecialchars($categoria->getNombre()) ?>
                                            </span>

                                            <?php if (!$p->estaEnStock()): ?>
                                                <div class="product-sin-stock">Sin stock</div>
                                            <?php endif; ?>

                                        </div>

                                        <div class="product-card-body">

                                            <p class="product-meta">
                                                <?= htmlspecialchars($p->getRegion()) ?> · <?= $p->getAnio() ?>
                                            </p>

                                            <h3 class="product-name">
                                                <?= htmlspecialchars($p->getNombre()) ?>
                                            </h3>

                                            <?php $varietales = $p->getVarietales(); ?>

                                            <p class="product-varietal">
                                                <?= !empty($varietales)
                                                    ? htmlspecialchars($varietales[0]->getNombre())
                                                    : 'Sin varietal'; ?>
                                            </p>



                                            <div class="product-card-footer">
                                                <span class="product-price">
                                                    <?= $p->getPrecioFormateado() ?>
                                                </span>

                                                <span class="product-agregar">
                                                    AGREGAR <i class="bi bi-plus"></i>
                                                </span>
                                            </div>

                                        </div>

                                    </a>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>