<?php
$todos = Vino::catalogo_completo();

// Obtener filtros como arrays
$filtro_categoria = isset($_GET['categoria']) ? explode(',', $_GET['categoria']) : [];
$filtro_region    = isset($_GET['region'])    ? explode(',', $_GET['region'])    : [];
$filtro_varietal  = isset($_GET['varietal'])  ? explode(',', $_GET['varietal'])  : [];
$filtro_precio    = isset($_GET['precio'])    ? explode(',', $_GET['precio'])    : [];
$ordenar          = isset($_GET['ordenar'])   ? $_GET['ordenar']   : 'destacados';

// Limpiar arrays de valores vacíos o "todos"
$filtro_categoria = array_filter($filtro_categoria, fn($v) => $v !== '' && $v !== 'todos');
$filtro_region    = array_filter($filtro_region,    fn($v) => $v !== '' && $v !== 'todos');
$filtro_varietal  = array_filter($filtro_varietal,  fn($v) => $v !== '' && $v !== 'todos');
$filtro_precio    = array_filter($filtro_precio,    fn($v) => $v !== '' && $v !== 'todos');

// Aplicar filtros
$productos = array_filter($todos, function ($p) use ($filtro_categoria, $filtro_region, $filtro_varietal, $filtro_precio) {

    // Categoría (OR entre seleccionadas)
    if (!empty($filtro_categoria)) {
        $catProd = strtolower($p->getCategoria()->getNombre());
        if (!in_array($catProd, array_map('strtolower', $filtro_categoria))) {
            return false;
        }
    }

    // Región (OR entre seleccionadas)
    if (!empty($filtro_region)) {
        if (!in_array($p->getRegionNombre(), $filtro_region)) {
            return false;
        }
    }

    // Varietal (OR entre seleccionadas)
    if (!empty($filtro_varietal)) {
        $coincide = false;
        foreach ($p->getVarietales() as $varietal) {
            if (in_array($varietal->getNombre(), $filtro_varietal)) {
                $coincide = true;
                break;
            }
        }
        if (!$coincide) {
            return false;
        }
    }

    // Precio (OR entre rangos seleccionados)
    if (!empty($filtro_precio)) {
        $cumplePrecio = false;
        foreach ($filtro_precio as $fp) {
            if ($fp === 'hasta15' && $p->getPrecio() <= 15000) $cumplePrecio = true;
            if ($fp === '15a25' && $p->getPrecio() > 15000 && $p->getPrecio() <= 25000) $cumplePrecio = true;
            if ($fp === 'mas25' && $p->getPrecio() > 25000) $cumplePrecio = true;
            if ($cumplePrecio) break;
        }
        if (!$cumplePrecio) return false;
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
$regiones  = array_unique(array_map(fn($p) => $p->getRegionNombre(), $todos));
$varietales = [];
foreach ($todos as $vino) {
    foreach ($vino->getVarietales() as $varietal) {
        $varietales[] = $varietal->getNombre();
    }
}
$varietales = array_unique($varietales);
sort($regiones);
sort($varietales);

/**
 * Construir URL de filtro para selección múltiple
 * Si el valor ya existe en el parámetro, lo quita (toggle).
 * Si no existe, lo agrega.
 * Si se pasa 'todos', se limpia ese parámetro.
 */
function filtroUrlMultiple(string $param, string $valor, array $seleccionados): string
{
    $parametros = $_GET;
    unset($parametros['seccion']);

    if ($valor === 'todos') {
        unset($parametros[$param]);
    } else {
        if (in_array($valor, $seleccionados)) {
            // Quitar valor (Toggle OFF)
            $nuevaSeleccion = array_diff($seleccionados, [$valor]);
        } else {
            // Agregar valor (Toggle ON)
            $nuevaSeleccion = array_merge($seleccionados, [$valor]);
        }

        if (empty($nuevaSeleccion)) {
            unset($parametros[$param]);
        } else {
            $parametros[$param] = implode(',', $nuevaSeleccion);
        }
    }

    return 'index.php?seccion=tienda&' . http_build_query($parametros);
}
?>

<section class="tienda-section" id="tienda">
    <div class="container-fluid px-4">

        <div class="tienda-header mb-4 d-flex justify-content-between align-items-end flex-wrap gap-3">
            <div>
                <div class="section-label">— Tienda —</div>
                <h2 class="section-title">Nuestros <em>vinos</em></h2>
                <p class="tienda-sub"><?= count($productos) ?> de <?= count($todos) ?> etiquetas</p>
            </div>
            <button class="account-edit-btn justify-content-center d-lg-none" id="btnFiltrosMobile">
                <i class="bi bi-funnel"></i>
                Filtros
            </button>
            
        </div>

        <div class="row">

            <!-- ── PANEL DE FILTROS ── -->
            <aside class="col-lg-2 tienda-sidebar">
                
                <div class="filtros-sidebar-overlay" id="filtrosOverlay"></div>

                <div class="filtros-panel" id="filtrosPanel">
                    
                    <div class="filtros-mobile-header d-lg-none">
                        <h5>Filtros</h5>
                        <button type="button" class="btn-close-filtros" id="btnCloseFiltros">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- CATEGORÍAS -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">CATEGORÍA</h6>
                        <div class="filtro-pills">
                            <?php $categorias = Categoria::todas(); ?>
                            <a href="<?= filtroUrlMultiple('categoria', 'todos', $filtro_categoria) ?>"
                                class="pill-btn <?= empty($filtro_categoria) ? 'active' : '' ?>">
                                Todas
                            </a>
                            <?php foreach ($categorias as $categoria): 
                                $slug = strtolower($categoria->getNombre());
                                $activo = in_array($slug, array_map('strtolower', $filtro_categoria));
                            ?>
                                <a href="<?= filtroUrlMultiple('categoria', $slug, $filtro_categoria) ?>"
                                    class="pill-btn <?= $activo ? 'active' : '' ?>">
                                    <?= htmlspecialchars($categoria->getNombre()) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- REGIÓN -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">REGIÓN</h6>
                        <div class="filtro-pills">
                            <a href="<?= filtroUrlMultiple('region', 'todos', $filtro_region) ?>"
                                class="pill-btn <?= empty($filtro_region) ? 'active' : '' ?>">Todas</a>
                            <?php foreach ($regiones as $r): 
                                $activo = in_array($r, $filtro_region);
                            ?>
                                <a href="<?= filtroUrlMultiple('region', $r, $filtro_region) ?>"
                                    class="pill-btn <?= $activo ? 'active' : '' ?>">
                                    <?= htmlspecialchars($r) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- VARIETAL -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">VARIETAL</h6>
                        <div class="filtro-pills">
                            <a href="<?= filtroUrlMultiple('varietal', 'todos', $filtro_varietal) ?>"
                                class="pill-btn <?= empty($filtro_varietal) ? 'active' : '' ?>">Todas</a>
                            <?php foreach ($varietales as $v): 
                                $activo = in_array($v, $filtro_varietal);
                            ?>
                                <a href="<?= filtroUrlMultiple('varietal', $v, $filtro_varietal) ?>"
                                    class="pill-btn <?= $activo ? 'active' : '' ?>">
                                    <?= htmlspecialchars($v) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- PRECIO -->
                    <div class="filtro-grupo">
                        <h6 class="filtro-titulo">PRECIO</h6>
                        <div class="filtro-pills">
                            <a href="<?= filtroUrlMultiple('precio', 'todos', $filtro_precio) ?>"
                                class="pill-btn <?= empty($filtro_precio) ? 'active' : '' ?>">Todos</a>
                            <?php
                            $precios = [
                                'hasta15' => 'Hasta $15.000',
                                '15a25'   => '$15.000 — $25.000',
                                'mas25'   => 'Más de $25.000',
                            ];
                            foreach ($precios as $val => $label):
                                $activo = in_array($val, $filtro_precio);
                            ?>
                                <a href="<?= filtroUrlMultiple('precio', $val, $filtro_precio) ?>" 
                                    class="pill-btn <?= $activo ? 'active' : '' ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <a href="index.php?seccion=tienda" class="btn-limpiar">
                        <i class="bi bi-x-circle"></i> Limpiar filtros
                    </a>

                </div>
            </aside>

            <!-- ── GRID DE PRODUCTOS ── -->
            <div class="col-lg-10">

                <div class="tienda-toolbar d-flex justify-content-between align-items-center mb-4">
                    <span class="toolbar-count"><?= count($productos) ?> etiquetas</span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="toolbar-label">ORDENAR</label>
                        <select class="form-select toolbar-select" onchange="window.location.href=this.value">
                            <?php
                            $opciones = [
                                'destacados'  => 'Destacados',
                                'precio-asc'  => 'Precio: menor a mayor',
                                'precio-desc' => 'Precio: mayor a menor',
                                'nombre-az'   => 'Nombre: A–Z',
                                'anio-desc'   => 'Más reciente',
                            ];
                            foreach ($opciones as $val => $label):
                                // Mantener filtros actuales al cambiar orden
                                $paramsOrden = $_GET;
                                $paramsOrden['ordenar'] = $val;
                                unset($paramsOrden['seccion']);
                                $url = 'index.php?seccion=tienda&' . http_build_query($paramsOrden);
                                $sel = $ordenar === $val ? 'selected' : '';
                            ?>
                                <option value="<?= htmlspecialchars($url) ?>" <?= $sel ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if (empty($productos)): ?>
                    <div class="no-results text-center py-5">
                        <i class="bi bi-funnel" style="font-size:2.5rem; color: var(--color-gold);"></i>
                        <p class="mt-3">No se encontraron vinos con esos filtros.</p>
                        <a href="index.php?seccion=tienda" class="btn btn-hero-primary mt-2">Limpiar filtros</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4" id="productosGrid">
                        <?php foreach ($productos as $p): ?>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <article class="product-card">
                                    <a href="index.php?seccion=detalle&id=<?= $p->getIdVino() ?>" class="product-card-link">
                                        <div class="product-card-img-wrap">
                                            <img src="<?= htmlspecialchars($p->getImagenSrc()) ?>" alt="<?= htmlspecialchars($p->getNombre()) ?>" class="product-card-img">
                                            <?php
                                            $categoria = $p->getCategoria();
                                            $slugCategoria = strtolower($categoria->getNombre());
                                            if ($slugCategoria === 'rosé') $slugCategoria = 'rose';
                                            ?>
                                            <span class="product-badge badge-<?= $slugCategoria ?>">
                                                <?= htmlspecialchars($categoria->getNombre()) ?>
                                            </span>
                                            <?php if (!$p->estaEnStock()): ?>
                                                <div class="product-sin-stock">Sin stock</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-card-body">
                                            <p class="product-meta"><?= htmlspecialchars($p->getRegionNombre()) ?> · <?= $p->getAnio() ?></p>
                                            <h3 class="product-name"><?= htmlspecialchars($p->getNombre()) ?></h3>
                                            <?php $varietales = $p->getVarietales(); ?>
                                            <p class="product-varietal">
                                                <?= !empty($varietales) ? htmlspecialchars($varietales[0]->getNombre()) : 'Sin varietal'; ?>
                                            </p>
                                            <div class="product-card-footer">
                                                <span class="product-price"><?= $p->getPrecioFormateado() ?></span>
                                                <span class="product-agregar js-agregar-carrito" role="button" tabindex="0" data-id="<?= $p->getIdVino() ?>" aria-label="Agregar al carrito">
                                                    <i class="bi bi-bag-plus"></i>
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
