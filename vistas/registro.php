<?php
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<section class="login-page">

    <!-- Fondo atmosférico -->
    <div class="login-backdrop" aria-hidden="true">
        <div class="backdrop-orb backdrop-orb--1"></div>
        <div class="backdrop-orb backdrop-orb--2"></div>
        <div class="backdrop-orb backdrop-orb--3"></div>
        <div class="backdrop-lines"></div>
    </div>

    <!-- Contenido principal -->
    <main class="login-wrapper" role="main">
        <div class="login-card" role="region" aria-label="Inicio de sesión">

        <div class="login-logo-wrap">

            <div class="login-logo-header">

                <img
                    src="assets/img/logo.png"
                    alt="Vinito Club"
                    id="logoImg"
                    onerror="this.style.display='none'; document.getElementById('logoFallback').style.display='block';"
                >

                <div id="logoFallback" class="logo-fallback">
                    <span class="login-logo-text">Vinito Club</span>
                </div>

            </div>

            <p class="login-logo-sub">Vinos con alma</p>
        </div>

            <!-- Divisor decorativo -->
            <div class="login-divider" aria-hidden="true">
                <span></span>
                <i class="bi bi-gem"></i>
                <span></span>
            </div>

            <!-- Títulos -->
            <h1 class="login-title">Registrarme</h1>
            <p class="login-subtitle">Creá tu cuenta en Vinito Club y disfrutá de los mejores vinos.</p>

            <!-- Alertas dinámicas -->
            <?php if ($error === 'credenciales'): ?>
            <div class="alert-vinito alert-vinito--error" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>Credenciales incorrectas. Verificá tu email y contraseña.</span>
            </div>
            <?php elseif ($error === 'sesion'): ?>
            <div class="alert-vinito alert-vinito--error" role="alert">
                <i class="bi bi-shield-exclamation"></i>
                <span>Tu sesión expiró. Por favor, volvé a ingresar.</span>
            </div>
            <?php elseif ($success === 'logout'): ?>
            <div class="alert-vinito alert-vinito--success" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>Sesión cerrada correctamente.</span>
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
                id="registroForm"
                action="/acciones/auth/registro.php"
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
                    <div class="form-floating-custom">
                        <label for="telefono" class="form-label-custom">Teléfono</label>
                        <div class="input-icon-wrap">
                            <input
                                type="tel"
                                id="telefono"
                                name="telefono"
                                class="form-control"
                                placeholder="Tu teléfono"
                                autocomplete="tel"
                                required
                                aria-describedby="telefonoError"
                            >
                            <i class="bi bi-telephone" aria-hidden="true"></i>
                        </div>
                        <p class="invalid-msg" id="telefonoError" role="alert">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Ingresá un teléfono válido.</span>
                        </p>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-floating-custom">
                        <label for="password" class="form-label-custom">Contraseña</label>
                        <div class="input-icon-wrap password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Tu contraseña"
                                autocomplete="current-password"
                                required
                                aria-describedby="passwordError"
                                class="form-control password-input"
                            >
                            <i class="bi bi-lock" aria-hidden="true"></i>
                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Mostrar u ocultar contraseña"
                                title="Mostrar contraseña"
                            >
                            </button>
                        </div>
                        <p class="invalid-msg" id="passwordError" role="alert">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>La contraseña no puede estar vacía.</span>
                        </p>
                    </div>
                </div>

                <!-- Confirmar contraseña -->
                    <div class="form-floating-custom full-width">
                        <label for="password2" class="form-label-custom">Confirmar contraseña</label>
                        <div class="input-icon-wrap password-wrap">
                            <input
                                type="password"
                                id="password2"
                                name="password2"
                                placeholder="Confirmá tu contraseña"
                                autocomplete="current-password"
                                required
                                aria-describedby="password2Error"
                                class="form-control password-input"
                            >
                            <i class="bi bi-lock" aria-hidden="true"></i>
                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword2"
                                aria-label="Mostrar u ocultar contraseña"
                                title="Mostrar contraseña"
                            >
                            </button>
                        </div>
                        <p class="invalid-msg" id="password2Error" role="alert">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>La confirmación de la contraseña no puede estar vacía.</span>
                        </p>
                    </div>

                <!-- Recordarme -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="remember"
                        name="remember"
                        value="1"
                    >
                    <label class="form-check-label" for="remember">
                        Recordar mi sesión
                    </label>
                </div>

                <!-- Botón -->
                <button type="submit" class="btn-contacto-submit w-100" id="submitBtn" aria-label="Crear cuenta">
                    <div class="btn-spinner" id="btnSpinner" aria-hidden="true"></div>
                    <i class="bi bi-box-arrow-in-right" id="btnIcon" aria-hidden="true"></i>
                    <span id="btnText">Crear cuenta</span>
                </button>
            </form>

            <div class="text-center mt-4 error-404-extra">
                <p>¿Ya tenés una cuenta? <a href="index.php?seccion=login">Iniciar sesión</a></p>
            </div>

            <!-- Volver al sitio -->
                <div class="login-home-link-wrap">
                    <a href="index.php?seccion=inicio" class="login-home-link">
                        <i class="bi bi-arrow-left"></i>
                        Volver al sitio
                    </a>
                </div>

        </div><!-- /.login-card -->
    </main>

    <!-- Footer -->
    <footer class="login-footer">
        <p class="mb-0">
            &copy; <?= date('Y') ?> Vinito Club — Todos los derechos reservados
        </p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS del sitio -->
    <script src="js/main.js"></script>
</section>