<?php
require_once 'includes/auth.php';

require_once '../classes/Conexion.php';
require_once '../classes/Vino.php';

$vinos = Vino::catalogo_completo();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar vinos</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administrar vinos</h1>
        <a href="vino-crear.php" class="btn btn-success">+ Nuevo vino</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Bodega</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach($vinos as $vino): ?>

            <tr>

                <td><?= $vino->getIdVino() ?></td>

                <td><?= htmlspecialchars($vino->getNombre()) ?></td>

                <td><?= htmlspecialchars($vino->getBodega()) ?></td>

                <td><?= htmlspecialchars($vino->getCategoriaLabel()) ?></td>

                <td><?= $vino->getPrecioFormateado() ?></td>

                <td><?= $vino->getStock() ?></td>

                <td>

                    <a
                        href="vino-editar.php?id=<?= $vino->getIdVino() ?>"
                        class="btn btn-warning btn-sm"
                    >
                        Editar
                    </a>

                    <a
                        href="vino-eliminar.php?id=<?= $vino->getIdVino() ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('¿Eliminar este vino?')"
                    >
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <a
        href="index.php"
        class="btn btn-secondary"
    >
        Volver
    </a>

</body>
</html>