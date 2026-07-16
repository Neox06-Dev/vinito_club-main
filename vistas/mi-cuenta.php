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

$success = $_GET['success'] ?? '';

?>

<section class="account-page py-5">

    <div class="container">

        <?php if ($success === 'perfil'): ?>
        <div class="alert-vinito alert-vinito--success mb-4" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>Tus datos se actualizaron correctamente.</span>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <header class="mb-5 account-page-header">
            <p class="section-label">— MI CUENTA —</p>

            <h1 class="section-title">
                ¡Hola, <em><?= htmlspecialchars($usuario->getNombre()); ?></em>!
            </h1>

            <p class="account-subtitle">
                Gestioná tu información personal dentro de Vinito Club.
            </p>
        </header>

        <div class="row g-4">

            <!-- Sidebar -->
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
                                <i class="bi bi-envelope"></i>
                            </span>
                            <span class="account-contact-text">
                                <?= htmlspecialchars($usuario->getEmail()); ?>
                            </span>
                        </div>

                        <div class="account-contact-item">
                            <span class="account-contact-icon">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <span class="account-contact-text">
                                <?= !empty($usuario->getTelefono())
                                    ? htmlspecialchars($usuario->getTelefono())
                                    : 'Sin teléfono'; ?>
                            </span>
                        </div>

                    </div>

                    <a href="acciones/auth/logout.php" class="btn-hero-primary w-100 justify-content-center mt-4">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </a>

                    <button
                        type="button"
                        class="btn btn-hero-outline btn-eliminar w-100 justify-content-center mt-3"
                        data-href="acciones/perfil/eliminar-cuenta.php"
                        data-nombre="tu cuenta"
                    >
                        <i class="bi bi-trash"></i>
                        Eliminar cuenta
                    </button>

                </div>
            </aside>

            <!-- Contenido -->
            <div class="col-lg-8">
                <div class="row g-4">

                    <!-- Información personal -->
                    <div class="col-md-6">
                        <div class="account-module h-100">

                            <div class="account-module-header">
                                <h2>
                                    <i class="bi bi-person"></i>
                                    Información personal
                                </h2>

                                <a href="index.php?seccion=editar-perfil" class="account-edit-btn">
                                    <i class="bi bi-pencil"></i>
                                    Editar
                                </a>
                            </div>

                            <div class="account-info-list">

                                <div class="account-info-item">
                                    <span>Nombre de usuario</span>
                                    <strong><?= htmlspecialchars($usuario->getNombre()); ?></strong>
                                </div>

                                <div class="account-info-item">
                                    <span>Correo electrónico</span>
                                    <strong><?= htmlspecialchars($usuario->getEmail()); ?></strong>
                                </div>

                                <div class="account-info-item">
                                    <span>Teléfono</span>
                                    <strong>
                                        <?= !empty($usuario->getTelefono())
                                            ? htmlspecialchars($usuario->getTelefono())
                                            : 'No registrado'; ?>
                                    </strong>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="col-md-6">
                        <div class="account-module h-100">

                            <div class="account-module-header">
                                <h2>
                                    <i class="bi bi-geo-alt"></i>
                                    Dirección
                                </h2>
                            </div>

                            <div class="account-empty-state">
                                <div class="account-empty-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <p class="account-empty-title">
                                    Todavía no registraste una dirección
                                </p>
                                <p class="account-empty-text">
                                    Vas a poder agregarla durante tu primera compra
                                    para agilizar tus próximos pedidos.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Seguridad -->
                    <div class="col-12">
                        <div class="account-module account-module--row">

                            <div class="account-module-header account-module-header--inline">
                                <h2>
                                    <i class="bi bi-shield-lock"></i>
                                    Seguridad
                                </h2>

                                <a href="index.php?seccion=seguridad" class="account-edit-btn">
                                    <i class="bi bi-key"></i>
                                    Cambiar contraseña
                                </a>
                            </div>

                            <p class="account-empty-text account-security-text">
                                Actualizá tu contraseña periódicamente para mantener
                                tu cuenta protegida.
                            </p>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</section>

<!-- ── MODAL DE CONFIRMACIÓN: ELIMINAR CUENTA ──────────────────── -->
<div class="vinito-overlay" id="deleteOverlay"></div>
<div class="vinito-modal" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="vinito-modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <h3 id="deleteModalTitle">¿Eliminar tu cuenta?</h3>
    <p>Estás a punto de eliminar <strong id="deleteModalNombre"></strong> de Vinito Club. Esta acción no se puede deshacer y perderás el acceso a tu historial y datos.</p>
    <div class="vinito-modal-actions">
        <button type="button" class="btn-hero-outline" id="deleteCancelar">Cancelar</button>
        <a href="#" id="deleteConfirmar" class="btn-confirm-eliminar">Sí, eliminar</a>
    </div>
</div>
