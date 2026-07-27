<?php

require_once 'classes/Usuario.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?seccion=login');
    exit;
}

$usuario = Usuario::buscarPorId($_SESSION['id_usuario']);

$error = $_GET['error'] ?? '';

// Parsear la dirección guardada en campos separados para precargar el formulario
$direccionGuardada = $usuario->getDireccion() ?? '';
$callePreload        = '';
$ciudadPreload       = '';
$cpPreload           = '';
$referenciaPreload   = '';

if ($direccionGuardada !== '') {
    // Formato esperado: "Calle 123, Ciudad (CP 12345) - Referencia"
    // Separar referencia
    if (str_contains($direccionGuardada, ' - ')) {
        [$parteDir, $referenciaPreload] = explode(' - ', $direccionGuardada, 2);
    } else {
        $parteDir = $direccionGuardada;
    }

    // Separar CP
    if (preg_match('/^(.*?)\s*\(CP\s*([^)]+)\)/', $parteDir, $m)) {
        $cpPreload  = trim($m[2]);
        $sinCP      = trim($m[1]);
    } else {
        $sinCP = $parteDir;
    }

    // Separar calle y ciudad
    if (str_contains($sinCP, ', ')) {
        $partes      = explode(', ', $sinCP, 2);
        $callePreload  = trim($partes[0]);
        $ciudadPreload = trim($partes[1]);
    } else {
        $callePreload = $sinCP;
    }
}

?>

<section class="account-page py-5">

    <div class="container">

        <!-- Header -->
        <header class="mb-5 account-page-header">
            <p class="section-label">— EDITAR DIRECCIÓN —</p>

            <h1 class="section-title">
                Tu <em>dirección</em> de envío
            </h1>

            <p class="account-subtitle">
                Actualizá tu dirección para agilizar tus próximos pedidos en Vinito Club.
            </p>
        </header>

        <div class="row g-4">

            <!-- Vista previa en vivo -->
            <aside class="col-lg-4">
                <div class="account-sidebar">

                    <div class="account-avatar">
                        <i class="bi bi-geo-alt-fill" style="font-size:1.6rem;"></i>
                    </div>

                    <h2 class="account-name" style="font-size:1.05rem;" id="previewDireccion">
                        <?= $direccionGuardada !== ''
                            ? htmlspecialchars($direccionGuardada)
                            : 'Tu dirección'; ?>
                    </h2>

                    <span class="account-badge">Dirección de envío</span>

                    <div class="account-divider"></div>

                    <div class="account-contact">

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-person"></i>
                            </span>
                            <span class="account-contact-text">
                                <?= htmlspecialchars($usuario->getNombre()); ?>
                            </span>
                        </div>

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <span class="account-contact-text">
                                <?= htmlspecialchars($usuario->getEmail()); ?>
                            </span>
                        </div>

                    </div>

                </div>
            </aside>

            <!-- Formulario -->
            <div class="col-lg-8">
                <div class="account-module">

                    <div class="account-module-header">
                        <h2>
                            <i class="bi bi-geo-alt"></i>
                            Datos de la dirección
                        </h2>
                    </div>

                    <!-- Alertas PHP -->
                    <?php if ($error === 'campos'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Completá todos los campos obligatorios para continuar.</span>
                    </div>
                    <?php elseif ($error === 'calle'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Ingresá una calle válida (mínimo 3 caracteres).</span>
                    </div>
                    <?php elseif ($error === 'ciudad'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Ingresá una ciudad válida.</span>
                    </div>
                    <?php elseif ($error === 'actualizar'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>No se pudieron guardar los cambios. Intentá nuevamente.</span>
                    </div>
                    <?php endif; ?>

                    <!-- Alerta JS (validación cliente) -->
                    <div class="alert-vinito alert-vinito--error js-alert-hidden" id="jsAlertDir" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span id="jsAlertDirMsg"></span>
                    </div>

                    <!-- Formulario -->
                    <form
                        class="login-form"
                        id="editarDireccionForm"
                        action="acciones/perfil/actualizar-direccion.php"
                        method="POST"
                        novalidate
                    >

                        <div class="form-grid">

                            <!-- Calle y número -->
                            <div class="form-floating-custom full-width">
                                <label for="calle" class="form-label-custom">Calle y número <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="calle"
                                        name="calle"
                                        class="form-control"
                                        placeholder="Ej: Av. Corrientes 1234"
                                        autocomplete="street-address"
                                        required
                                        value="<?= htmlspecialchars($callePreload); ?>"
                                        aria-describedby="calleError"
                                    >
                                    <i class="bi bi-signpost" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="calleError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá la calle y número.</span>
                                </p>
                            </div>

                            <!-- Ciudad -->
                            <div class="form-floating-custom">
                                <label for="ciudad" class="form-label-custom">Ciudad <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="ciudad"
                                        name="ciudad"
                                        class="form-control"
                                        placeholder="Ej: Buenos Aires"
                                        autocomplete="address-level2"
                                        required
                                        value="<?= htmlspecialchars($ciudadPreload); ?>"
                                        aria-describedby="ciudadError"
                                    >
                                    <i class="bi bi-building" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="ciudadError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá la ciudad.</span>
                                </p>
                            </div>

                            <!-- Código postal -->
                            <div class="form-floating-custom">
                                <label for="codigo_postal" class="form-label-custom">Código postal <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="codigo_postal"
                                        name="codigo_postal"
                                        class="form-control"
                                        placeholder="Ej: 1043"
                                        autocomplete="postal-code"
                                        required
                                        value="<?= htmlspecialchars($cpPreload); ?>"
                                        aria-describedby="cpError"
                                    >
                                    <i class="bi bi-mailbox" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="cpError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá el código postal.</span>
                                </p>
                            </div>

                            <!-- Referencia (opcional) -->
                            <div class="form-floating-custom full-width">
                                <label for="referencia" class="form-label-custom">Referencia <span>(opcional)</span></label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="referencia"
                                        name="referencia"
                                        class="form-control"
                                        placeholder="Ej: Piso 3, Depto B"
                                        value="<?= htmlspecialchars($referenciaPreload); ?>"
                                    >
                                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                                </div>
                            </div>

                        </div>

                        <!-- Acciones -->
                        <div class="account-actions d-flex gap-3 mt-4">

                            <a href="index.php?seccion=mi-cuenta" class="btn-hero-outline flex-fill justify-content-center">
                                <i class="bi bi-arrow-left"></i>
                                Cancelar
                            </a>

                            <button type="submit" class="btn-contacto-submit flex-fill" id="submitBtnDir" aria-label="Guardar dirección">
                                <div class="btn-spinner" id="btnSpinnerDir" aria-hidden="true"></div>
                                <i class="bi bi-check-lg" id="btnIconDir" aria-hidden="true"></i>
                                <span id="btnTextDir">Guardar dirección</span>
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>

</section>
