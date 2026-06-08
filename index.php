<?php
// Secciones válidas
$secciones_validas = ['inicio', 'tienda', 'contacto', 'datos', 'procesar_contacto', 'detalle'];
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
    }
    ?>
</main>

<?php require_once 'includes/footer.php'; ?>