<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/Carrito.php';

$seccion_actual = $_GET['seccion'] ?? 'inicio';
$contador_carrito = Carrito::obtenerCantidadProductos();
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
                <div class="navbar-icons admin-icon">
                    <a href="admin/login.php" class="nav-icon-link" title="Panel de Administración">
                        <i class="bi bi-person"></i>
                    </a>
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