<?php

require_once 'includes/auth.php';
require_once 'includes/header.php';
require_once 'classes/Vino.php';

// Secciones válidas
$secciones_validas = ['inicio', 'tienda', 'contacto', 'datos', 'procesar_contacto', 'detalle', 'carrito', 'login', 'registro', 'mi-cuenta', 'editar-perfil', 'seguridad', 'checkout', 'pedido-confirmado', 'mis-pedidos', 'detalle-pedidos', '404'];
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';

if (!in_array($seccion, $secciones_validas)) {
    $seccion = '404';
}

if ($seccion === 'checkout') {

    if (!isset($_SESSION['id_usuario'])) {
        header('Location: index.php?seccion=login&error=checkout');
        exit;
    }

    require_once 'classes/Carrito.php';

    if (Carrito::obtenerCantidadProductos() === 0) {
        header('Location: index.php?seccion=carrito&error=carrito_vacio');
        exit;
    }
}

if ($seccion === 'pedido-confirmado') {

    if (!isset($_GET['id']) || (int)$_GET['id'] <= 0) {
        header('Location: index.php');
        exit;
    }

}

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

        case 'checkout':
            require_once 'vistas/checkout.php';
            break;

        case 'pedido-confirmado':
            require_once 'vistas/pedido-confirmado.php';
            break;

        case 'mis-pedidos':
            require_once 'vistas/mis-pedidos.php';
            break;
        
        case 'detalle-pedidos':
            require_once 'vistas/detalle-pedidos.php';
            break;
    }
    ?>
</main>

<?php require_once 'includes/footer.php'; ?>