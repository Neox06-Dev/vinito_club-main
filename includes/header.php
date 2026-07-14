<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/Carrito.php';

$seccion_actual = $_GET['seccion'] ?? 'inicio';
$contador_carrito = Carrito::obtenerCantidadProductos();

$usuarioLogueado = isset($_SESSION['id_usuario']);
$nombreUsuario = $_SESSION['nombre'] ?? '';
$textoAcceso = $usuarioLogueado ? $nombreUsuario : 'Acceder';

$mostrarHeader = !in_array($seccion_actual, [
    'login',
    'registro'
]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinito Club — Vinos con alma</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php if ($mostrarHeader): ?>

        <!-- Barra de anuncios -->
        <div class="announcement-bar d-flex flex-column flex-md-row justify-content-md-around align-items-center gap-2">
            <p>Envíos a todo el país &nbsp;·&nbsp; En compras mayores a $25.000 ARS, envíos <strong>gratis</strong></p>

            <div class="d-inline-flex align-items-center gap-2">
                <p class="m-0">Seguinos</p>
                <a href="https://www.instagram.com" target="_blank" class="social-icon-link">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="https://www.facebook.com" target="_blank"
                    class="social-icon-link">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://www.tiktok.com" target="_blank" class="social-icon-link">
                    <i class="bi bi-tiktok"></i>
                </a>
            </div>
        </div>

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg vinito-navbar sticky-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="index.php?seccion=inicio">
                    <img src="assets/img/logo.png" alt="Vinito Club Logo">
                </a>
                <div class="d-flex align-items-center gap-2 order-lg-3 ms-auto me-3">
                    <div class="navbar-icons cart-icon">
                        <a href="index.php?seccion=carrito" class="nav-icon-link cart-icon-link" title="Carrito">
                            <i class="bi bi-bag"></i>
                            <span
                                class="cart-count-badge <?= $contador_carrito > 0 ? '' : 'is-empty' ?>"
                                id="cartCount"
                                aria-label="Productos en el carrito">
                                <?= $contador_carrito ?>
                            </span>
                        </a>
                    </div>
                    <div class="dropdown navbar-icons account-dropdown">

                        <a
                            href="#"
                            class="nav-icon-link d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="bi bi-person"></i>

                            <span class="account-text d-none d-lg-inline">
                                <?= htmlspecialchars($textoAcceso) ?>
                            </span>

                            <i class="bi bi-chevron-down small"></i>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow">

                            <?php if (!$usuarioLogueado): ?>

                                <li>
                                    <a class="dropdown-item" href="index.php?seccion=login">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Iniciar sesión
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="index.php?seccion=registro">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Crear cuenta
                                    </a>
                                </li>

                            <?php else: ?>

                                <li>
                                    <a class="dropdown-item" href="index.php?seccion=mi-cuenta">
                                        <i class="bi bi-person-circle me-2"></i>
                                        Mi cuenta
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="index.php?seccion=mis-pedidos">
                                        <i class="bi bi-receipt me-2"></i>
                                        Mis pedidos
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item text-danger" href="acciones/auth/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Cerrar sesión
                                    </a>
                                </li>

                            <?php endif; ?>

                        </ul>

                    </div>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse order-lg-2 flex-lg-fill" id="navMenu">
                    <ul class="navbar-nav mx-auto gap-2">
                        <li class="nav-item">
                            <a class="nav-link <?= $seccion_actual === 'inicio' ? 'active' : '' ?>" href="index.php?seccion=inicio">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $seccion_actual === 'tienda' ? 'active' : '' ?>" href="index.php?seccion=tienda">Tienda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $seccion_actual === 'datos' ? 'active' : '' ?>" href="index.php?seccion=datos">Datos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $seccion_actual === 'contacto' ? 'active' : '' ?>" href="index.php?seccion=contacto">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>