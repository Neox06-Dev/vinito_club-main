<?php

require_once 'includes/auth.php';

?>

<h1>Panel Administrador</h1>

<p>Bienvenido <?= $_SESSION['nombre'] ?></p>

<hr>

<ul>
    <li>
        <a href="vinos.php">
            Administrar vinos
        </a>
    </li>

    <li>
        <a href="logout.php">
            Cerrar sesión
        </a>
    </li>
</ul>