<?php
/** @var string $error */
/** @var string $success */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Vinito Club</title>

    <!-- Estilos del sitio -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="login-page">

    <!-- Fondo atmosférico -->
    <div class="login-backdrop" aria-hidden="true">
        <div class="backdrop-orb backdrop-orb--1"></div>
        <div class="backdrop-orb backdrop-orb--2"></div>
        <div class="backdrop-orb backdrop-orb--3"></div>
        <div class="backdrop-lines"></div>
    </div>

    <!-- Contenido principal -->
    <main class="login-wrapper" role="main">
        <div class="login-card" role="region" aria-label="Panel de administración - Inicio de sesión">

        <div class="login-logo-wrap">

            <div class="login-logo-header">

                <img
                    src="../assets/img/logo.png"
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
            <h1 class="login-title">Iniciar sesión</h1>
            <p class="login-subtitle">Accedé al panel de administración de Vinito Club</p>

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
                id="loginForm"
                action="procesar_login.php"
                method="POST"
                novalidate
            >
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
                <button type="submit" class="btn-contacto-submit w-100" id="submitBtn" aria-label="Ingresar al panel de administración">
                    <div class="btn-spinner" id="btnSpinner" aria-hidden="true"></div>
                    <i class="bi bi-box-arrow-in-right" id="btnIcon" aria-hidden="true"></i>
                    <span id="btnText">Ingresar al Panel</span>
                </button>
            </form>

            <!-- Volver al sitio -->
                <div class="login-home-link-wrap">
                    <a href="../index.php?seccion=inicio" class="login-home-link">
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
    <script src="../js/main.js"></script>
</body>
</html>