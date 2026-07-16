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

?>

<section class="account-page py-5">

    <div class="container">

        <!-- Header -->
        <header class="mb-5 account-page-header">
            <p class="section-label">— EDITAR PERFIL —</p>

            <h1 class="section-title">
                Actualizá tus <em>datos</em>
            </h1>

            <p class="account-subtitle">
                Mantené tu información personal al día dentro de Vinito Club.
            </p>
        </header>

        <div class="row g-4">

            <!-- Vista previa en vivo -->
            <aside class="col-lg-4">
                <div class="account-sidebar">

                    <div class="account-avatar" id="previewAvatar">
                        <?= strtoupper(substr($usuario->getNombre(), 0, 1)); ?>
                    </div>

                    <h2 class="account-name" id="previewNombre">
                        <?= htmlspecialchars($usuario->getNombre()); ?>
                    </h2>

                    <span class="account-badge">Cliente Vinito Club</span>

                    <div class="account-divider"></div>

                    <div class="account-contact">

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <span class="account-contact-text" id="previewEmail">
                                <?= htmlspecialchars($usuario->getEmail()); ?>
                            </span>
                        </div>

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <span class="account-contact-text" id="previewTelefono">
                                <?= !empty($usuario->getTelefono())
                                    ? htmlspecialchars($usuario->getTelefono())
                                    : 'Sin teléfono'; ?>
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
                            <i class="bi bi-person-vcard"></i>
                            Datos de la cuenta
                        </h2>
                    </div>

                    <!-- Alertas dinámicas -->
                    <?php if ($error === 'campos'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Completá todos los campos obligatorios para continuar.</span>
                    </div>
                    <?php elseif ($error === 'email'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Ingresá un email válido.</span>
                    </div>
                    <?php elseif ($error === 'existe'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Ya existe otra cuenta registrada con ese email.</span>
                    </div>
                    <?php elseif ($error === 'actualizar'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>No se pudieron guardar los cambios. Intentá nuevamente.</span>
                    </div>
                    <?php endif; ?>

                    <!-- Alerta JS (validación cliente) -->
                    <div class="alert-vinito alert-vinito--error js-alert-hidden" id="jsAlert" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span id="jsAlertMsg"></span>
                    </div>

                    <!-- Formulario -->
                    <form
                        class="login-form"
                        id="editarPerfilForm"
                        action="acciones/perfil/actualizar-perfil.php"
                        method="POST"
                        novalidate
                    >

                        <div class="form-grid">

                            <!-- Nombre -->
                            <div class="form-floating-custom">
                                <label for="nombre" class="form-label-custom">Nombre</label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        class="form-control"
                                        placeholder="Tu nombre"
                                        autocomplete="name"
                                        required
                                        value="<?= htmlspecialchars($usuario->getNombre()); ?>"
                                        aria-describedby="nombreError"
                                    >
                                    <i class="bi bi-person" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="nombreError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá un nombre válido.</span>
                                </p>
                            </div>

                            <!-- Email -->
                            <div class="form-floating-custom">
                                <label for="email" class="form-label-custom">Email</label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Tu email"
                                        autocomplete="email"
                                        required
                                        value="<?= htmlspecialchars($usuario->getEmail()); ?>"
                                        aria-describedby="emailError"
                                    >
                                    <i class="bi bi-envelope" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="emailError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá un email válido.</span>
                                </p>
                            </div>

                            <!-- Teléfono -->
                            <div class="form-floating-custom full-width">
                                <label for="telefono" class="form-label-custom">Teléfono</label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="tel"
                                        id="telefono"
                                        name="telefono"
                                        class="form-control"
                                        placeholder="Tu teléfono"
                                        autocomplete="tel"
                                        value="<?= htmlspecialchars($usuario->getTelefono() ?? ''); ?>"
                                        aria-describedby="telefonoError"
                                    >
                                    <i class="bi bi-telephone" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="telefonoError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá un teléfono válido.</span>
                                </p>
                            </div>

                        </div>

                        <!-- Acciones -->
                        <div class="account-actions d-flex gap-3 mt-4">

                            <a href="index.php?seccion=mi-cuenta" class="btn-hero-outline flex-fill  justify-content-center">
                                <i class="bi bi-arrow-left"></i>
                                Cancelar
                            </a>

                            <button type="submit" class="btn-contacto-submit flex-fill" id="submitBtn" aria-label="Guardar cambios">
                                <div class="btn-spinner" id="btnSpinner" aria-hidden="true"></div>
                                <i class="bi bi-check-lg" id="btnIcon" aria-hidden="true"></i>
                                <span id="btnText">Guardar cambios</span>
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>

</section>