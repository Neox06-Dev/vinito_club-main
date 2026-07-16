<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario'])
    || $_SESSION['rol'] !== 'admin'
) {
    header('Location: index.php?sec=login&error=sesion');
    exit;
}