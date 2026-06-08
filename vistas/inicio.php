<?php
$productos = Vino::catalogo_completo();
$destacados = array_filter($productos, fn($p) => $p->getDestacado());
?>

<!-- HERO -->
<section class="hero-section" id="inicio">
    <picture class="hero-media" aria-hidden="true">
        <source srcset="assets/img/hero-bg.png" media="(min-width: 992px)">
        <source srcset="assets/img/hero-bg-movil.png" media="(max-width: 991px)">
        <img src="assets/img/hero-bg.png" alt="Hero imagen" class="hero-media-img">
    </picture>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center min-vh-section">
            <div class="col-lg-6">
                <p class="hero-eyebrow">Bodegas boutique · Selección curada</p>
                <h1 class="hero-title">Descubrí vinos<br><em>sin complicarte</em></h1>
                <p class="hero-sub">Elegimos los mejores vinos para vos.<br>Vos solo tenés que descorchar y disfrutar.</p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="index.php?seccion=tienda" class="btn btn-hero-primary">
                        Explorar vinos &nbsp;<i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="index.php?seccion=contacto" class="btn btn-hero-outline">Unite al club</a>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-hint">
        <span>Scrolleá</span>
        <i class="bi bi-chevron-down"></i>
    </div>
</section>

<!-- VIDEO -->
<section class="video-section">
    <div class="container">
        <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/embed/aL2dIxwLJoA?autoplay=1&mute=1"
                title="Presentación de Vinito Club"
                allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
    </div>
</section>

<!-- NOSOTROS -->
<section class="nosotros-section" id="nosotros">
    <div class="container">
        <div class="section-label text-center">— Nosotros —</div>
        <h2 class="section-title text-center">Un club nacido del<br><em>amor por el vino</em></h2>
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <p>Vinito Club nace de la pasión por las bodegas boutique y los vinos con alma. Cada etiqueta que llega a tu copa es elegida personalmente por nuestros sommeliers, en visitas a viñedos donde el oficio aún se hereda.</p>
                <p>Más que una tienda, somos una comunidad de amantes del vino que comparten catas, experiencias únicas y descubrimientos exclusivos cada mes.</p>
            </div>
        </div>
        <div class="row g-4 mt-4 stats-row">
            <div class="col-4 text-center">
                <div class="stat-number">22</div>
                <div class="stat-label">Años de experiencia</div>
            </div>
            <div class="col-4 text-center border-x-gold">
                <div class="stat-number">+80</div>
                <div class="stat-label">Bodegas Boutique</div>
            </div>
            <div class="col-4 text-center">
                <div class="stat-number">5k</div>
                <div class="stat-label">Socios activos</div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="index.php?seccion=datos" class="btn btn-hero-primary mt-3">
                Conocenos un poco más &nbsp;<i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- BENEFICIOS -->
<section class="beneficios-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="beneficio-item">
                    <i class="bi bi-truck beneficio-icon"></i>
                    <h6 class="beneficio-title">Envío a todo el país</h6>
                    <p class="beneficio-text">Gratis en compras mayores a $25.000</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="beneficio-item">
                    <i class="bi bi-award beneficio-icon"></i>
                    <h6 class="beneficio-title">Selección curada</h6>
                    <p class="beneficio-text">Cada vino elegido por nuestros sommeliers</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="beneficio-item">
                    <i class="bi bi-shield-check beneficio-icon"></i>
                    <h6 class="beneficio-title">Compra segura</h6>
                    <p class="beneficio-text">Pago 100% seguro y protegido</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="beneficio-item">
                    <i class="bi bi-arrow-counterclockwise beneficio-icon"></i>
                    <h6 class="beneficio-title">Devolución fácil</h6>
                    <p class="beneficio-text">30 días para cambios sin preguntas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DESTACADOS -->
<section class="destacados-section">
    <div class="container">
        <div class="section-label text-center">— Selección —</div>
        <h2 class="section-title text-center">Vinos <em>destacados</em></h2>
        <div class="row g-4 mt-2">
            <?php foreach ($destacados as $p): ?>
                <div class="col-lg-4 col-md-6">
                    <article class="product-card">
                        <a href="index.php?seccion=detalle&id=<?= $p->getIdVino() ?>" class="product-card-link">
                            <div class="product-card-img-wrap">
                                <img src="<?= htmlspecialchars($p->getImagenSrc()) ?>"
                                    alt="<?= htmlspecialchars($p->getNombre()) ?>"
                                    class="product-card-img">

                                <?php $categoria = $p->getCategoria(); ?>

                                <span class="product-badge <?= $categoria->getClaseCss() ?>">
                                    <?= htmlspecialchars($categoria->getNombre()) ?>
                                </span>
                                
                            </div>
                            <div class="product-card-body">
                                <p class="product-meta"><?= htmlspecialchars($p->getRegion()) ?> · <?= $p->getAnio() ?></p>
                                <h3 class="product-name"><?= htmlspecialchars($p->getNombre()) ?></h3>
                                <?php $varietales = $p->getVarietales();?>

                                <p class="product-varietal">
                                    <?= !empty($varietales)
                                        ? htmlspecialchars($varietales[0]->getNombre())
                                        : 'Sin varietal'; ?>
                                </p>
                                <div class="product-card-footer">
                                    <span class="product-price"><?= $p->getPrecioFormateado() ?></span>
                                    <span class="product-agregar">AGREGAR <i class="bi bi-plus"></i></span>
                                </div>
                            </div>
                        </a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="index.php?seccion=tienda" class="btn btn-hero-primary">
                Ver toda la tienda &nbsp;<i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="testimonios-section">
    <div class="container mb-5">
        <div class="section-label text-center mt-5">— Testimonios —</div>
        <h2 class="section-title text-center">Lo que dicen nuestros <em>socios</em></h2>

        <!-- CARRUSEL BOOTSTRAP -->
        <div id="testimoniosCarousel" class="carousel slide mt-4" data-bs-ride="carousel" data-bs-interval="5000">

            <!-- Indicadores -->
            <div class="carousel-indicators testimonio-indicators">
                <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Grupo 1"></button>
                <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="1" aria-label="Grupo 2"></button>
                <button type="button" data-bs-target="#testimoniosCarousel" data-bs-slide-to="2" aria-label="Grupo 3"></button>
            </div>

            <div class="carousel-inner">

                <!-- SLIDE 1 -->
                <div class="carousel-item active">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Vinito Club transformó mis cenas. Cada vino es un descubrimiento y el servicio es impecable."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">María López</p>
                                        <p class="testimonio-ubicacion">Buenos Aires</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"La selección de vinos es increíble. Siempre encuentro algo nuevo para probar y el envío es súper rápido."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Juan Pérez</p>
                                        <p class="testimonio-ubicacion">Córdoba</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                </div>
                                <p class="testimonio-text">"Vinito Club es mi secreto mejor guardado. La calidad de los vinos y la atención al cliente son excepcionales."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Laura Gómez</p>
                                        <p class="testimonio-ubicacion">Rosario</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 2 -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Recibo mi caja mensual con mucha anticipación. Cada etiqueta viene acompañada de una ficha descriptiva que me ayuda a entender y disfrutar más el vino."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Santiago Ríos</p>
                                        <p class="testimonio-ubicacion">Mendoza</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Compré una caja como regalo para mi marido y quedó enamorado. Ahora somos socios del club y no lo cambiaríamos por nada."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Valeria Martínez</p>
                                        <p class="testimonio-ubicacion">Mar del Plata</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Descubrí varietales que nunca hubiera elegido solo. El equipo de Vinito tiene un gusto impecable y la relación precio-calidad es inmejorable."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Federico Castro</p>
                                        <p class="testimonio-ubicacion">Tucumán</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 3 -->
                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                </div>
                                <p class="testimonio-text">"El Malbec de Valle de Uco que me mandaron el mes pasado fue una revelación. Jamás lo hubiera encontrado en una vinoteca convencional."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Analía Gutiérrez</p>
                                        <p class="testimonio-ubicacion">Salta</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Uso Vinito para mis cenas de negocios y siempre causa muy buena impresión. Mis invitados siempre preguntan de dónde saco esos vinos tan especiales."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Martín Barros</p>
                                        <p class="testimonio-ubicacion">Buenos Aires</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonio-card">
                                <div class="testimonio-stars mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <p class="testimonio-text">"Llevo dos años siendo socia y el club me sorprende mes a mes. El espumante de San Rafael que llegó en diciembre fue simplemente perfecto para las fiestas."</p>
                                <div class="testimonio-author d-flex align-items-center gap-3 mt-3">
                                    <div>
                                        <p class="testimonio-nombre">Cecilia Flores</p>
                                        <p class="testimonio-ubicacion">Neuquén</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Controles -->
            <button class="carousel-control-prev testimonio-control" type="button" data-bs-target="#testimoniosCarousel" data-bs-slide="prev">
                <i class="bi bi-chevron-left testimonio-control-icon"></i>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next testimonio-control" type="button" data-bs-target="#testimoniosCarousel" data-bs-slide="next">
                <i class="bi bi-chevron-right testimonio-control-icon"></i>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>
</section>

<section class="contacto-section-inicio" id="contacto">
    <div class="container">
        <div class="section-label text-center mt-5">— Contacto —</div>
        <h2 class="section-title text-center">¿Querés ser parte del club?</h2>
        <p class="nosotros-text text-center">Si tenés preguntas, querés sugerir un vino o simplemente saludar, escribinos. Nos encanta conectar con nuestra comunidad.</p>
        <div class="text-center mt-4">
            <a href="index.php?seccion=contacto" class="btn btn-hero-primary">
                Contactanos &nbsp;<i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>