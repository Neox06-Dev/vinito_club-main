<?php

session_start();

session_destroy();

header('Location: index.php?sec=login&success=logout');
exit;