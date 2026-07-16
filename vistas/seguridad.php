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
$success = $_GET['success'] ?? '';

?>

<section class="account-page py-5">

    <div class="container">

        <!-- Header -->
        <header class="mb-5 account-page-header">
            <p class="section-label">— SEGURIDAD —</p>

            <h1 class="section-title">
                Protegé tu <em>cuenta</em>
            </h1>

            <p class="account-subtitle">
                Actualizá tu contraseña periódicamente para mantener tu cuenta segura.
            </p>
        </header>

        <div class="row g-4">

            <!-- Consejos de seguridad -->
            <aside class="col-lg-4">
                <div class="account-sidebar">

                    <div class="account-avatar">
                        <?= strtoupper(substr($usuario->getNombre(), 0, 1)); ?>
                    </div>

                    <h2 class="account-name">
                        <?= htmlspecialchars($usuario->getNombre()); ?>
                    </h2>

                    <span class="account-badge">Cliente Vinito Club</span>

                    <div class="account-divider"></div>

                    <div class="account-contact">

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-check2"></i>
                            </span>
                            <span class="account-contact-text">
                                Usá al menos 6 caracteres
                            </span>
                        </div>

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-check2"></i>
                            </span>
                            <span class="account-contact-text">
                                Combiná letras, números y símbolos
                            </span>
                        </div>

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-check2"></i>
                            </span>
                            <span class="account-contact-text">
                                No reutilices contraseñas de otros sitios
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
                            <i class="bi bi-shield-lock"></i>
                            Cambiar contraseña
                        </h2>
                    </div>

                    <!-- Alertas dinámicas -->
                    <?php if ($error === 'campos'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Completá todos los campos para continuar.</span>
                    </div>
                    <?php elseif ($error === 'longitud'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>La nueva contraseña debe tener al menos 6 caracteres.</span>
                    </div>
                    <?php elseif ($error === 'coincide'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Las contraseñas nuevas no coinciden.</span>
                    </div>
                    <?php elseif ($error === 'actual'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Tu contraseña actual es incorrecta.</span>
                    </div>
                    <?php elseif ($error === 'igual'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>La nueva contraseña debe ser distinta de la actual.</span>
                    </div>
                    <?php elseif ($error === 'actualizar'): ?>
                    <div class="alert-vinito alert-vinito--error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>No se pudo actualizar la contraseña. Intentá nuevamente.</span>
                    </div>
                    <?php elseif ($success === 'password'): ?>
                    <div class="alert-vinito alert-vinito--success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Tu contraseña se actualizó correctamente.</span>
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
                        id="passwordForm"
                        action="acciones/perfil/actualizar-password.php"
                        method="POST"
                        novalidate
                    >

                        <!-- Contraseña actual -->
                        <div class="form-floating-custom full-width">
                            <label for="password_actual" class="form-label-custom">Contraseña actual</label>
                            <div class="input-icon-wrap password-wrap">
                                <input
                                    type="password"
                                    id="password_actual"
                                    name="password_actual"
                                    placeholder="Tu contraseña actual"
                                    autocomplete="current-password"
                                    required
                                    aria-describedby="passwordActualError"
                                    class="form-control password-input"
                                >
                                <i class="bi bi-lock" aria-hidden="true"></i>
                                <button
                                    type="button"
                                    class="password-toggle"
                                    id="togglePasswordActual"
                                    aria-label="Mostrar u ocultar contraseña"
                                    title="Mostrar contraseña"
                                >
                                </button>
                            </div>
                            <p class="invalid-msg" id="passwordActualError" role="alert">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>Ingresá tu contraseña actual.</span>
                            </p>
                        </div>

                        <div class="form-grid">

                            <!-- Nueva contraseña -->
                            <div class="form-floating-custom">
                                <label for="password_nueva" class="form-label-custom">Nueva contraseña</label>
                                <div class="input-icon-wrap password-wrap">
                                    <input
                                        type="password"
                                        id="password_nueva"
                                        name="password_nueva"
                                        placeholder="Tu nueva contraseña"
                                        autocomplete="new-password"
                                        required
                                        aria-describedby="passwordNuevaError"
                                        class="form-control password-input"
                                    >
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                    <button
                                        type="button"
                                        class="password-toggle"
                                        id="togglePasswordNueva"
                                        aria-label="Mostrar u ocultar contraseña"
                                        title="Mostrar contraseña"
                                    >
                                    </button>
                                </div>
                                <p class="invalid-msg" id="passwordNuevaError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>La contraseña debe tener al menos 6 caracteres.</span>
                                </p>
                            </div>

                            <!-- Confirmar nueva contraseña -->
                            <div class="form-floating-custom">
                                <label for="password_confirmar" class="form-label-custom">Confirmar contraseña</label>
                                <div class="input-icon-wrap password-wrap">
                                    <input
                                        type="password"
                                        id="password_confirmar"
                                        name="password_confirmar"
                                        placeholder="Confirmá tu nueva contraseña"
                                        autocomplete="new-password"
                                        required
                                        aria-describedby="passwordConfirmarError"
                                        class="form-control password-input"
                                    >
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                    <button
                                        type="button"
                                        class="password-toggle"
                                        id="togglePasswordConfirmar"
                                        aria-label="Mostrar u ocultar contraseña"
                                        title="Mostrar contraseña"
                                    >
                                    </button>
                                </div>
                                <p class="invalid-msg" id="passwordConfirmarError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span id="passwordConfirmarErrorMsg">Las contraseñas no coinciden.</span>
                                </p>
                            </div>

                        </div>

                        <!-- Acciones -->
                        <div class="account-actions d-flex gap-3 mt-4">

                            <a href="index.php?seccion=mi-cuenta" class="btn-hero-outline flex-fill justify-content-center">
                                <i class="bi bi-arrow-left"></i>
                                Cancelar
                            </a>

                            <button type="submit" class="btn-contacto-submit flex-fill" id="submitBtn" aria-label="Guardar nueva contraseña">
                                <div class="btn-spinner" id="btnSpinner" aria-hidden="true"></div>
                                <i class="bi bi-shield-check" id="btnIcon" aria-hidden="true"></i>
                                <span id="btnText">Guardar contraseña</span>
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>

</section>
