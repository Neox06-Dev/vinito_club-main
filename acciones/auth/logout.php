<?php

session_start();

// Eliminar únicamente la sesión del cliente
unset($_SESSION['id_usuario']);
unset($_SESSION['nombre']);
unset($_SESSION['email']);
unset($_SESSION['rol']);

// Eliminar la cookie de recordatorio
setcookie(
    'recordar_usuario',
    '',
    time() - 3600,
    '/'
);

header('Location: ../../index.php');

exit;