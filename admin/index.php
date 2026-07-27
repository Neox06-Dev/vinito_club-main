<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'includes/functions.php';
require_once '../classes/Usuario.php';

// Si no hay sesión activa pero existe la cookie "recordar_admin", reloguear automáticamente
if (
    !isset($_SESSION['id_usuario']) &&
    isset($_COOKIE['recordar_admin'])
) {

    $usuario = Usuario::buscarPorId(
        (int) $_COOKIE['recordar_admin']
    );

    if ($usuario && $usuario->getRol() === 'admin') {
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nombre'] = $usuario->getNombre();
        $_SESSION['rol'] = $usuario->getRol();
    }
}

$adminLogueado = isset($_SESSION['id_usuario']) && $_SESSION['rol'] === 'admin';

// Si no especifican sección: raíz del admin -> login directo (si no hay sesión) o dashboard.
$seccion = $_GET['sec'] ?? ($adminLogueado ? 'dashboard' : 'login');

if ($seccion !== 'login') {
    require_once 'includes/auth.php';
}

require_once 'includes/header-admin.php';

switch ($seccion) {

    case 'login':

        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        require_once 'vistas/login.php';

        break;

    case 'dashboard':

        require_once '../classes/Vino.php';
        require_once '../classes/Categoria.php';
        require_once '../classes/Varietal.php';
        require_once '../classes/Pedido.php';

        $totalVinos = count(Vino::catalogo_completo());
        $totalCategorias = count(Categoria::todas());
        $totalVarietales = count(Varietal::todas());

        $ultimosVinos = Vino::ultimos();

        $errorVentas = null;

        try {
            $estadisticasVentas = Pedido::estadisticasVentas();
            $ultimosPedidos = Pedido::ultimos(4);
        } catch (Throwable $e) {
            $errorVentas = $e->getMessage();
            $estadisticasVentas = [
                'facturado'        => 0,
                'cantidad_pedidos' => 0,
                'ticket_promedio'  => 0,
                'pendientes'       => 0,
                'ventas_por_dia'   => [],
            ];
            $ultimosPedidos = [];
        }

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

        require_once 'vistas/dashboard.php';

        break;

    case 'vinos':

        require_once '../classes/Conexion.php';
        require_once '../classes/Vino.php';

        $vinos = Vino::catalogo_completo();

        require_once 'vistas/vinos.php';

        break;

    case 'vino-crear':

        require_once '../classes/Conexion.php';
        require_once '../classes/Vino.php';
        require_once '../classes/Categoria.php';
        require_once '../classes/Varietal.php';
        require_once '../classes/Region.php';

        $categorias = Categoria::todas();
        $regiones = Region::todas();
        $varietales = Varietal::todas();

        $error = $_GET['error'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
            $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');

            if ($anioCosecha === '') {
                header('Location: index.php?sec=vino-crear&error=anio_cosecha');
                exit;
            }

            if ($temperaturaServicio === '') {
                header('Location: index.php?sec=vino-crear&error=temperatura_servicio');
                exit;
            }

            $regionId = (int)($_POST['region_id'] ?? 0);

            if ($regionId <= 0 || Region::porId($regionId) === null) {
                header('Location: index.php?sec=vino-crear&error=region_id');
                exit;
            }

            $vino = new Vino();

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $nombreImagen = null;
            if (!empty($_FILES['imagen']['name'])) {

                $nombreImagen = uniqid() . '-' . basename($_FILES['imagen']['name']);

                move_uploaded_file(
                    $_FILES['imagen']['tmp_name'],
                    '../assets/img/productos/' . $nombreImagen
                );
            }
            $vino->setImagen($nombreImagen);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegionId($regionId);
            $vino->setVarietalId((int)$_POST['varietal_id']);

            if (!$vino->crear()) {
                header('Location: index.php?sec=vino-crear&error=region_id');
                exit;
            }

            header('Location: index.php?sec=vinos');
            exit;
        }

        require_once 'vistas/vino-crear.php';

        break;

    case 'vino-editar':

        require_once '../classes/Conexion.php';
        require_once '../classes/Vino.php';
        require_once '../classes/Categoria.php';
        require_once '../classes/Varietal.php';
        require_once '../classes/Region.php';

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?sec=vinos');
            exit;
        }

        $vino = Vino::vino_por_id((int)$id);

        if (!$vino) {
            header('Location: index.php?sec=vinos');
            exit;
        }

        $categorias = Categoria::todas();
        $regiones = Region::todas();
        $varietales = Varietal::todas();

        $varietalesActuales = $vino->getVarietales();

        $varietalActualId = !empty($varietalesActuales)
            ? $varietalesActuales[0]->getId()
            : null;

        $error = $_GET['error'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $anioCosecha = trim($_POST['anio_cosecha'] ?? '');
            $temperaturaServicio = trim($_POST['temperatura_servicio'] ?? '');

            if ($anioCosecha === '') {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=anio_cosecha');
                exit;
            }

            if ($temperaturaServicio === '') {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=temperatura_servicio');
                exit;
            }

            $regionId = (int)($_POST['region_id'] ?? 0);

            if ($regionId <= 0 || Region::porId($regionId) === null) {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=region_id');
                exit;
            }

            $vino->setNombre($_POST['nombre']);
            $vino->setDescripcion($_POST['descripcion']);
            $vino->setPrecio((float)$_POST['precio']);
            $vino->setStock((int)$_POST['stock']);
            $nombreImagen = $vino->getImagen();
            if (!empty($_FILES['imagen']['name'])) {

                $imagenVieja = '../assets/img/productos/' . $vino->getImagen();

                if (
                    file_exists($imagenVieja)
                    && is_file($imagenVieja)
                ) {
                    unlink($imagenVieja);
                }

                $nombreImagen = uniqid() . '-' . basename($_FILES['imagen']['name']);

                move_uploaded_file(
                    $_FILES['imagen']['tmp_name'],
                    '../assets/img/productos/' . $nombreImagen
                );
            }

            $vino->setImagen($nombreImagen);
            $vino->setAnioCosecha($anioCosecha);
            $vino->setVolumenMl((int)$_POST['volumen_ml']);
            $vino->setTemperaturaServicio((int)$temperaturaServicio);
            $vino->setBodega($_POST['bodega']);
            $vino->setCategoriaId((int)$_POST['categoria_id']);
            $vino->setVarietalId((int)$_POST['varietal_id']);
            $vino->setMaridaje($_POST['maridaje']);
            $vino->setDestacado((int)$_POST['destacado']);
            $vino->setRegionId($regionId);

            if (!$vino->editar()) {
                header('Location: index.php?sec=vino-editar&id=' . (int)$id . '&error=region_id');
                exit;
            }

            header('Location: index.php?sec=vinos');
            exit;
        }

        require_once 'vistas/vino-editar.php';

        break;

    case 'categorias':

        require_once '../classes/Categoria.php';

        $error = $_GET['error'] ?? '';

        $categorias = Categoria::todas();

        require_once 'vistas/categorias.php';

        break;

    case 'categoria-crear':

        require_once '../classes/Categoria.php';

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria = new Categoria();

            $categoria->setNombre($_POST['nombre']);

            if ($categoria->crear()) {

                header('Location: index.php?sec=categorias');
                exit;
            }

            $error = $categoria->getError();
        }

        require_once 'vistas/categoria-crear.php';

        break;

    case 'categoria-editar':

        require_once '../classes/Categoria.php';

        $id = (int)($_GET['id'] ?? 0);

        $categoria = Categoria::porId($id);

        if (!$categoria) {
            header('Location: index.php?sec=categorias');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria->setNombre($_POST['nombre']);

            if ($categoria->editar()) {

                header('Location: index.php?sec=categorias');
                exit;
            }

            $error = $categoria->getError();
        }

        require_once 'vistas/categoria-editar.php';

        break;

    case 'categoria-eliminar':

        require_once '../classes/Categoria.php';

        $id = (int)($_GET['id'] ?? 0);

        $categoria = Categoria::porId($id);

        if (!$categoria) {
            header('Location: index.php?sec=categorias');
            exit;
        }

        if (!$categoria->eliminar()) {

            header(
                'Location: index.php?sec=categorias&error=' .
                    urlencode($categoria->getError())
            );

            exit;
        }

        header('Location: index.php?sec=categorias');
        exit;

    case 'regiones':

        require_once '../classes/Region.php';

        $error = $_GET['error'] ?? '';

        $regiones = Region::todas();

        require_once 'vistas/regiones.php';

        break;

    case 'region-crear':

        require_once '../classes/Region.php';

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $region = new Region();

            $region->setNombre($_POST['nombre']);

            if ($region->crear()) {

                header('Location: index.php?sec=regiones');
                exit;
            }

            $error = $region->getError();
        }

        require_once 'vistas/region-crear.php';

        break;

    case 'region-editar':

        require_once '../classes/Region.php';

        $id = (int)($_GET['id'] ?? 0);

        $region = Region::porId($id);

        if (!$region) {
            header('Location: index.php?sec=regiones');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $region->setNombre($_POST['nombre']);

            if ($region->editar()) {

                header('Location: index.php?sec=regiones');
                exit;
            }

            $error = $region->getError();
        }

        require_once 'vistas/region-editar.php';

        break;

    case 'region-eliminar':
        require_once '../classes/Region.php';

        $id = (int)($_GET['id'] ?? 0);

        $region = Region::porId($id);

        if (!$region) {
            header('Location: index.php?sec=regiones');
            exit;
        }

        if (!$region->eliminar()) {

            header(
                'Location: index.php?sec=regiones&error=' .
                    urlencode($region->getError())
            );

            exit;
        }

        header('Location: index.php?sec=regiones');
        exit;

    case 'varietales':
        require_once '../classes/Varietal.php';

        $error = $_GET['error'] ?? '';

        $varietales = Varietal::todas();

        require_once 'vistas/varietales.php';

        break;
    
    case 'varietal-crear':
        require_once '../classes/Varietal.php';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $varietal = new Varietal();

            $varietal->setNombre($_POST['nombre']);

            if ($varietal->crear()) {

                header('Location: index.php?sec=varietales');
                exit;
            }

            $error = $varietal->getError();
        }

        require_once 'vistas/varietal-crear.php';

        break;

    case 'varietal-editar':
        require_once '../classes/Varietal.php';

        $id = (int)($_GET['id'] ?? 0);

        $varietal = Varietal::porId($id);

        if (!$varietal) {
            header('Location: index.php?sec=varietales');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $varietal->setNombre($_POST['nombre']);

            if ($varietal->editar()) {

                header('Location: index.php?sec=varietales');
                exit;
            }

            $error = $varietal->getError();
        }

        require_once 'vistas/varietal-editar.php';

        break;

    case 'varietal-eliminar':
        require_once '../classes/Varietal.php';

        $id = (int)($_GET['id'] ?? 0);

        $varietal = Varietal::porId($id);

        if (!$varietal) {
            header('Location: index.php?sec=varietales');
            exit;
        }

        if (!$varietal->eliminar()) {

            header(
                'Location: index.php?sec=varietales&error=' .
                    urlencode($varietal->getError())
            );

            exit;
        }

        header('Location: index.php?sec=varietales');
        exit;

    case 'pedidos':

        require_once '../classes/Pedido.php';

        $pedidos = Pedido::listarTodos();

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

        require_once 'vistas/pedidos.php';

        break;

    case 'pedido-ver':

        require_once '../classes/Pedido.php';
        require_once '../classes/DetallePedido.php';
        require_once '../classes/Usuario.php';

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: index.php?sec=pedidos');
            exit;
        }

        $estadosDisponibles = ['Pendiente', 'Preparando', 'Enviado', 'Entregado', 'Cancelado'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nuevoEstado = trim($_POST['estado'] ?? '');

            if (in_array($nuevoEstado, $estadosDisponibles, true)) {
                Pedido::actualizarEstado($id, $nuevoEstado);
            }

            header('Location: index.php?sec=pedido-ver&id=' . $id . '&success=1');
            exit;
        }

        $datos = Pedido::obtenerPedidoCompleto($id);

        if ($datos === null) {
            header('Location: index.php?sec=pedidos');
            exit;
        }

        $pedido = $datos['pedido'];
        $usuario = $datos['usuario'];
        $productos = $datos['productos'];

        $success = $_GET['success'] ?? '';

        require_once 'vistas/pedido-ver.php';

        break;

    default:
        header('Location: index.php?sec=dashboard');
        exit;
}

require_once 'includes/footer-admin.php';
