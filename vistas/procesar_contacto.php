<?php
    // Validar y sanitizar datos del formulario
    $nombre   = isset($_POST['nombre'])   ? htmlspecialchars(trim($_POST['nombre']))   : '';
    $apellido = isset($_POST['apellido']) ? htmlspecialchars(trim($_POST['apellido'])) : '';
    $email    = isset($_POST['email'])    ? htmlspecialchars(trim($_POST['email']))    : '';
    $telefono = isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : 'No proporcionado';
    $motivo   = isset($_POST['motivo'])   ? htmlspecialchars(trim($_POST['motivo']))   : '';
    $mensaje  = isset($_POST['mensaje'])  ? htmlspecialchars(trim($_POST['mensaje']))  : '';
    $suscribir = isset($_POST['suscribir']) ? 'Sí' : 'No';

    date_default_timezone_set('America/Argentina/Buenos_Aires');

    $motivoLabels = [
        'consulta'   => 'Consulta sobre un vino',
        'club'       => 'Quiero unirme al club',
        'mayorista'  => 'Consulta mayorista',
        'regalo'     => 'Quiero armar un regalo',
        'otro'       => 'Otro',
    ];
    $motivoLabel = $motivoLabels[$motivo] ?? $motivo;

    $errores = [];
    if (empty($nombre))   $errores[] = 'El nombre es obligatorio.';
    if (empty($apellido)) $errores[] = 'El apellido es obligatorio.';
    if (empty($email) || !filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errores[] = 'El email no es válido.';
    if (empty($motivo))   $errores[] = 'Seleccioná un motivo de contacto.';
    if (empty($mensaje))  $errores[] = 'El mensaje no puede estar vacío.';
    ?>

    <section class="procesar-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">

                    <?php if (!empty($errores)): ?>
                        <!-- ERROR -->
                        <div class="procesar-card procesar-error text-center">
                            <i class="bi bi-x-circle procesar-icon icon-error"></i>
                            <h2 class="procesar-title">Hubo un problema</h2>
                            <p class="procesar-sub">Por favor corregí los siguientes errores y volvé a intentarlo:</p>
                            <ul class="procesar-errores-list text-start">
                                <?php foreach ($errores as $e): ?>
                                    <li><?= $e ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="index.php?seccion=contacto" class="btn btn-hero-primary mt-4">
                                <i class="bi bi-arrow-left"></i> &nbsp; Volver al formulario
                            </a>
                        </div>

                    <?php else: ?>
                        <!-- ÉXITO -->
                        <div class="procesar-card text-center">
                            <i class="bi bi-check-circle procesar-icon icon-ok"></i>
                            <h2 class="procesar-title">¡Mensaje enviado!</h2>
                            <p class="procesar-sub">Gracias por escribirnos, <?= $nombre ?>. Te respondemos en menos de 24 horas.</p>
                        </div>

                        <!-- RESUMEN -->
                        <div class="procesar-resumen">
                            <h4 class="resumen-titulo">Resumen de tu mensaje</h4>
                            <table class="table resumen-table">
                                <tbody>
                                    <tr>
                                        <th>Nombre completo</th>
                                        <td><?= $nombre . ' ' . $apellido ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= $email ?></td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono</th>
                                        <td><?= $telefono ?></td>
                                    </tr>
                                    <tr>
                                        <th>Motivo</th>
                                        <td><?= $motivoLabel ?></td>
                                    </tr>
                                    <tr>
                                        <th>Mensaje</th>
                                        <td><?= nl2br($mensaje) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Suscripción al newsletter</th>
                                        <td><?= $suscribir ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha y hora</th>
                                        <td><?= date('d/m/Y H:i') ?> hs</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4 d-flex gap-3 justify-content-center flex-wrap">
                            <a href="index.php?seccion=inicio" class="btn btn-hero-primary flex-fill justify-content-center">
                                <i class="bi bi-house"></i> &nbsp; Volver al inicio
                            </a>
                            <a href="index.php?seccion=tienda" class="btn btn-hero-outline flex-fill justify-content-center">
                                Ver vinos <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>