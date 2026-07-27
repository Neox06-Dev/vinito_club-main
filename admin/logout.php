<?php

session_start();

session_destroy();

// Eliminar la cookie de recordatorio del admin
setcookie(
    'recordar_admin',
    '',
    time() - 3600,
    '/'
);

header('Location: index.php?sec=login&success=logout');
exit;