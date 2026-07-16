<?php

session_start();

// Secciones válidas
$secciones_validas = ['inicio', 'tienda', 'contacto', 'datos', 'procesar_contacto', 'detalle', 'carrito', 'login', 'registro', 'mi-cuenta', 'editar-perfil', 'seguridad'];
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';


if (!in_array($seccion, $secciones_validas)) {
    $seccion = '404';
}

require_once 'includes/header.php';
require_once 'classes/Vino.php';

?>

<main id="main-content">
    <?php
    switch ($seccion) {
        case 'inicio':
            require 'vistas/inicio.php';
            break;
        case 'tienda':
            require 'vistas/tienda.php';
            break;
        case 'contacto':
            require 'vistas/contacto.php';
            break;
        case 'datos':
            require 'vistas/datos.php';
            break;
        case 'procesar_contacto':
            require 'vistas/procesar_contacto.php';
            break;
        case 'detalle':
            require 'vistas/detalle.php';
            break;
        case '404':
            require 'vistas/404.php';
            break;

        case 'carrito':
            require_once 'classes/Carrito.php';

            $productos = Carrito::obtenerProductos();
            $cantidadProductos = Carrito::obtenerCantidadProductos();
            $subtotal = Carrito::obtenerSubtotal();

            require_once 'vistas/carrito.php';
            break;

        case 'login':
            require_once 'vistas/login.php';
            break;

        case 'registro':
            require_once 'vistas/registro.php';
            break;
        
        case 'mi-cuenta':
            require_once 'vistas/mi-cuenta.php';
            break;

        case 'editar-perfil':
            require_once 'vistas/editar-perfil.php';
            break;

        case 'seguridad':
            require_once 'vistas/seguridad.php';
            break;
    }
    ?>
</main>

<?php require_once 'includes/footer.php'; ?>