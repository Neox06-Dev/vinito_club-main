<?php

session_start();

// Eliminar únicamente la sesión del cliente
unset($_SESSION['id_usuario']);
unset($_SESSION['nombre']);
unset($_SESSION['email']);
unset($_SESSION['rol']);

header('Location: ../../index.php');

exit;