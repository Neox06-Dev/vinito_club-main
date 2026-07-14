<?php

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?seccion=login');
    exit;
}

?>

<section class="account-page py-5">

    <div class="container">

        <div class="account-card">

            <div class="account-header">

                <p class="section-label">
                    — MI PERFIL —
                </p>

                <h1 class="section-title">
                    Hola,
                    <em><?= htmlspecialchars($_SESSION['nombre']) ?></em>
                </h1>

                <p class="account-subtitle">
                    Desde aquí podrás administrar tu cuenta y consultar tus pedidos.
                </p>

            </div>

            <div class="account-info">

                <div class="account-info-item">

                    <span>Nombre de usuario</span>

                    <strong>
                        <?= htmlspecialchars($_SESSION['nombre']) ?>
                    </strong>

                </div>

                <div class="account-info-item">

                    <span>Email</span>

                    <strong>
                        <?= htmlspecialchars($_SESSION['email']) ?>
                    </strong>

                </div>

                <div class="account-info-item">

                    <span>Rol</span>

                    <strong>
                        <?= ucfirst($_SESSION['rol']) ?>
                    </strong>

                </div>

            </div>

            <div class="account-actions">
                <a
                    href="index.php?seccion=mis-pedidos"
                    class="btn-hero-outline">

                    <i class="bi bi-receipt"></i>

                    Mis pedidos
                </a>

                <a
                    href="acciones/auth/logout.php"
                    class="btn-hero-primary">

                    <i class="bi bi-box-arrow-right"></i>

                    Cerrar sesión
                </a>

            </div>

        </div>

    </div>

</section>