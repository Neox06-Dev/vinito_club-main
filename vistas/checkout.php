<?php

require_once 'classes/Usuario.php';
require_once 'classes/Carrito.php';


$usuario = Usuario::buscarPorId($_SESSION['id_usuario']);

$productos = Carrito::obtenerProductos();
$cantidadProductos = Carrito::obtenerCantidadProductos();
$subtotal = Carrito::obtenerSubtotal();

$envioGratisDesde = 25000;
$costoEnvioBase = 3500;

$costoEnvioDomicilio = ($subtotal > 0 && $subtotal < $envioGratisDesde)
    ? $costoEnvioBase
    : 0;

$totalDomicilio = $subtotal + $costoEnvioDomicilio;
$totalRetiro = $subtotal;

$error = $_GET['error'] ?? '';
?>

<section class=" checkout-page py-5">

    <div class="container">

        <!-- Header -->
        <header class="mb-4 justify-content-center text-center">
            <p class="section-label">— ÚLTIMO PASO —</p>

            <h1 class="section-title">
                Finalizá tu <em>pedido</em>
            </h1>

            <p class="nosotros-text">
                Revisá tus datos, elegí cómo recibirlo y confirmá la compra.
            </p>
        </header>

        <!-- Stepper -->
        <div class="checkout-steps mb-5">
            <div class="checkout-step is-done">
                <span class="checkout-step-circle"><i class="bi bi-bag-check"></i></span>
                <span class="checkout-step-label">Carrito</span>
            </div>
            <div class="checkout-step-line"></div>
            <div class="checkout-step is-active">
                <span class="checkout-step-circle">2</span>
                <span class="checkout-step-label">Datos y pago</span>
            </div>
            <div class="checkout-step-line"></div>
            <div class="checkout-step">
                <span class="checkout-step-circle"><i class="bi bi-check-lg"></i></span>
                <span class="checkout-step-label">Confirmación</span>
            </div>
        </div>

        <?php if ($error === 'campos'): ?>
        <div class="alert-vinito alert-vinito--error mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>Completá todos los campos obligatorios para continuar.</span>
        </div>
        <?php elseif ($error === 'direccion'): ?>
        <div class="alert-vinito alert-vinito--error mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>Completá la dirección de entrega o elegí retiro en tienda.</span>
        </div>
        <?php endif; ?>

        <!-- Alerta JS (validación cliente) -->
        <div class="alert-vinito alert-vinito--error js-alert-hidden mb-4" id="jsAlert" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span id="jsAlertMsg"></span>
        </div>

        <form
            class="login-form"
            id="checkoutForm"
            action="acciones/checkout/confirmar-compra.php"
            method="POST"
            novalidate
        >

            <div class="row g-4">

                <!-- Columna principal -->
                <div class="col-lg-8">

                    <!-- Información de contacto -->
                    <div class="checkout-module mb-4">

                        <div class="account-module-header">
                            <h2>
                                <i class="bi bi-person"></i>
                                Información de contacto
                            </h2>
                        </div>

                        <div class="form-grid">

                            <div class="form-floating-custom">
                                <label for="nombre" class="form-label-custom">
                                    Nombre completo
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        class="form-control"
                                        required
                                        value="<?= htmlspecialchars($usuario->getNombre()); ?>"
                                        aria-describedby="nombreError"
                                    >
                                    <i class="bi bi-person" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="nombreError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá tu nombre completo.</span>
                                </p>
                            </div>

                            <div class="form-floating-custom">
                                <label for="email" class="form-label-custom">
                                    Email
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
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

                            <div class="form-floating-custom full-width">
                                <label for="telefono" class="form-label-custom">
                                    Teléfono de contacto
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <input
                                        type="tel"
                                        id="telefono"
                                        name="telefono"
                                        class="form-control"
                                        required
                                        value="<?= htmlspecialchars($usuario->getTelefono() ?? ''); ?>"
                                        aria-describedby="telefonoError"
                                    >
                                    <i class="bi bi-telephone" aria-hidden="true"></i>
                                </div>
                                <p class="invalid-msg" id="telefonoError" role="alert">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Ingresá un teléfono de contacto.</span>
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- Método de entrega -->
                    <div class="checkout-module mb-4">

                        <div class="account-module-header">
                            <h2>
                                <i class="bi bi-truck"></i>
                                Método de entrega
                            </h2>
                        </div>

                        <div class="checkout-options">

                            <label class="checkout-option" data-option-group="entrega">
                                <input type="radio" name="tipo_entrega" value="domicilio" checked>
                                <span class="checkout-option-icon"><i class="bi bi-house-door"></i></span>
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title">Envío a domicilio</span>
                                    <span class="checkout-option-desc">Recibilo en la puerta de tu casa en 3 a 5 días hábiles.</span>
                                </span>
                                <span class="checkout-option-price" id="precioEnvioDomicilio">
                                    <?= $costoEnvioDomicilio > 0
                                        ? '$ ' . number_format($costoEnvioDomicilio, 0, ',', '.')
                                        : 'Gratis'; ?>
                                </span>
                            </label>

                            <label class="checkout-option" data-option-group="entrega">
                                <input type="radio" name="tipo_entrega" value="retiro">
                                <span class="checkout-option-icon"><i class="bi bi-shop"></i></span>
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title">Retiro en tienda</span>
                                    <span class="checkout-option-desc">Av. del Libertador 1234, CABA · Listo en 24 hs.</span>
                                </span>
                                <span class="checkout-option-price">Gratis</span>
                            </label>

                        </div>

                        <!-- Dirección de entrega (solo si es envío a domicilio) -->
                        <div class="checkout-address mt-4" id="direccionWrap">

                            <div class="form-grid">

                                <div class="form-floating-custom full-width">
                                    <label for="calle" class="form-label-custom">
                                        Calle y número
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="calle"
                                            name="calle"
                                            class="form-control"
                                            placeholder="Ej: Av. Corrientes 1234"
                                            aria-describedby="calleError"
                                        >
                                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="calleError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá la calle y el número.</span>
                                    </p>
                                </div>

                                <div class="form-floating-custom">
                                    <label for="ciudad" class="form-label-custom">
                                        Ciudad
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="ciudad"
                                            name="ciudad"
                                            class="form-control"
                                            placeholder="Tu ciudad"
                                            aria-describedby="ciudadError"
                                        >
                                        <i class="bi bi-buildings" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="ciudadError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá tu ciudad.</span>
                                    </p>
                                </div>

                                <div class="form-floating-custom">
                                    <label for="codigo_postal" class="form-label-custom">
                                        Código postal
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="codigo_postal"
                                            name="codigo_postal"
                                            class="form-control"
                                            placeholder="Ej: 1425"
                                            aria-describedby="cpError"
                                        >
                                        <i class="bi bi-mailbox" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="cpError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá tu código postal.</span>
                                    </p>
                                </div>

                                <div class="form-floating-custom full-width">
                                    <label for="referencia" class="form-label-custom">Piso / depto / referencia (opcional)</label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="referencia"
                                            name="referencia"
                                            class="form-control"
                                            placeholder="Ej: 3º B, timbre 'González'"
                                        >
                                        <i class="bi bi-signpost" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Método de pago -->
                    <div class="checkout-module mb-4">

                        <div class="account-module-header">
                            <h2>
                                <i class="bi bi-credit-card"></i>
                                Método de pago
                            </h2>
                        </div>

                        <div class="checkout-options">

                            <label class="checkout-option" data-option-group="pago">
                                <input type="radio" name="metodo_pago" value="tarjeta" checked>
                                <span class="checkout-option-icon"><i class="bi bi-credit-card-2-front"></i></span>
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title">Tarjeta de crédito o débito</span>
                                    <span class="checkout-option-desc">Visa, Mastercard, Mercado Pago y American Express.</span>
                                </span>
                            </label>

                            <label class="checkout-option" data-option-group="pago">
                                <input type="radio" name="metodo_pago" value="transferencia">
                                <span class="checkout-option-icon"><i class="bi bi-bank"></i></span>
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title">Transferencia bancaria</span>
                                    <span class="checkout-option-desc">Te enviamos el CBU por email para transferir.</span>
                                </span>
                            </label>

                            <label class="checkout-option" data-option-group="pago">
                                <input type="radio" name="metodo_pago" value="efectivo">
                                <span class="checkout-option-icon"><i class="bi bi-cash-coin"></i></span>
                                <span class="checkout-option-body">
                                    <span class="checkout-option-title">Efectivo al retirar</span>
                                    <span class="checkout-option-desc">Pagás cuando pasás a buscar tu pedido.</span>
                                </span>
                            </label>

                        </div>

                        <!-- Datos de tarjeta (solo si el método es tarjeta) -->
                        <div class="checkout-address mt-4" id="tarjetaWrap">

                            <div class="form-grid">

                                <div class="form-floating-custom full-width">
                                    <label for="cuotas" class="form-label-custom">Cuotas</label>
                                    <div class="input-icon-wrap">
                                        <select
                                            id="cuotas"
                                            name="cuotas"
                                            class="form-control"
                                            aria-describedby="cuotasHelp"
                                        >
                                            <option class="checkout-select" value="1">1 cuota</option>
                                            <option class="checkout-select" value="2">2 cuotas</option>
                                            <option class="checkout-select" value="3">3 cuotas</option>
                                            <option class="checkout-select" value="4">4 cuotas</option>
                                            <option class="checkout-select" value="5">5 cuotas</option>
                                            <option class="checkout-select" value="6">6 cuotas</option>
                                        </select>
                                        <i class="bi bi-calendar2-range" aria-hidden="true"></i>
                                    </div>
                                    <p class="checkout-cuotas-help" id="cuotasHelp">
                                        <span id="cuotasDetalle">1 pago de $ <?= number_format($totalDomicilio, 0, ',', '.') ?></span>
                                    </p>
                                </div>

                                <div class="form-floating-custom full-width">
                                    <label for="numero_tarjeta" class="form-label-custom">
                                        Número de tarjeta
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="numero_tarjeta"
                                            name="numero_tarjeta"
                                            class="form-control"
                                            placeholder="0000 0000 0000 0000"
                                            inputmode="numeric"
                                            maxlength="19"
                                            aria-describedby="tarjetaError"
                                        >
                                        <i class="bi bi-credit-card" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="tarjetaError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá el número de tarjeta.</span>
                                    </p>
                                </div>

                                <div class="form-floating-custom">
                                    <label for="vencimiento_tarjeta" class="form-label-custom">
                                        Vencimiento
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="vencimiento_tarjeta"
                                            name="vencimiento_tarjeta"
                                            class="form-control"
                                            placeholder="MM/AA"
                                            maxlength="5"
                                            aria-describedby="vencimientoError"
                                        >
                                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="vencimientoError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá la fecha de vencimiento.</span>
                                    </p>
                                </div>

                                <div class="form-floating-custom">
                                    <label for="cvv_tarjeta" class="form-label-custom">
                                        CVV
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-icon-wrap">
                                        <input
                                            type="text"
                                            id="cvv_tarjeta"
                                            name="cvv_tarjeta"
                                            class="form-control"
                                            placeholder="123"
                                            inputmode="numeric"
                                            maxlength="4"
                                            aria-describedby="cvvError"
                                        >
                                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                                    </div>
                                    <p class="invalid-msg" id="cvvError" role="alert">
                                        <i class="bi bi-x-circle-fill"></i>
                                        <span>Ingresá el código de seguridad.</span>
                                    </p>
                                </div>

                            </div>

                            <p class="checkout-secure-note">
                                <i class="bi bi-lock-fill"></i>
                                Tus datos de pago viajan encriptados y no se almacenan en nuestros servidores.
                            </p>

                        </div>

                    </div>

                    <!-- Observaciones -->
                    <div class="checkout-module">

                        <div class="account-module-header">
                            <h2>
                                <i class="bi bi-chat-left-text"></i>
                                Observaciones
                            </h2>
                        </div>

                        <div class="form-floating-custom full-width mb-0">
                            <label for="observaciones" class="form-label-custom">¿Algo que debamos saber? (opcional)</label>
                            <textarea
                                id="observaciones"
                                name="observaciones"
                                class="form-control form-control-custom"
                                rows="4"
                                placeholder="Ej: dejar en portería, horario preferido de entrega, etc."
                            ></textarea>
                        </div>

                    </div>

                </div>

                <!-- Resumen del pedido -->
                <aside class="col-lg-4">

                    <div class="carrito-resumen checkout-resumen">

                        <h2>Resumen del pedido</h2>

                        <div class="checkout-resumen-items">

                            <?php foreach ($productos as $item):

                                $vino = $item['vino'];
                                $cantidad = $item['cantidad'];
                                $subtotalProducto = $item['subtotal'];

                            ?>

                            <div class="checkout-resumen-item">

                                <div class="checkout-resumen-item-img">
                                    <img
                                        src="<?= htmlspecialchars($vino->getImagenSrc()) ?>"
                                        alt="<?= htmlspecialchars($vino->getNombre()) ?>">
                                    <span class="checkout-resumen-item-qty"><?= $cantidad ?></span>
                                </div>

                                <div class="checkout-resumen-item-info">
                                    <p class="checkout-resumen-item-nombre">
                                        <?= htmlspecialchars($vino->getNombre()) ?>
                                    </p>
                                    <p class="checkout-resumen-item-bodega">
                                        <?= htmlspecialchars($vino->getBodega()) ?>
                                    </p>
                                </div>

                                <strong class="checkout-resumen-item-precio">
                                    $ <?= number_format($subtotalProducto, 0, ',', '.') ?>
                                </strong>

                            </div>

                            <?php endforeach; ?>

                        </div>

                        <hr>

                        <div class="carrito-summary-row">
                            <span>Productos:</span>
                            <span><?= $cantidadProductos . ' ' . ($cantidadProductos === 1 ? 'vino' : 'vinos') ?></span>
                        </div>

                        <div class="carrito-summary-row">
                            <span>Subtotal:</span>
                            <span>$ <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>

                        <div class="carrito-summary-row">
                            <span>Envío:</span>
                            <span id="resumenEnvio">
                                <?= $costoEnvioDomicilio > 0
                                    ? '$ ' . number_format($costoEnvioDomicilio, 0, ',', '.')
                                    : 'Gratis'; ?>
                            </span>
                        </div>

                        <hr>

                        <div class="carrito-summary-total product-price">
                            <span>Total:</span>
                            <strong id="resumenTotal">
                                $ <?= number_format($totalDomicilio, 0, ',', '.') ?>
                            </strong>
                        </div>

                        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                        <input type="hidden" id="envioInput" name="costo_envio" value="<?= $costoEnvioDomicilio ?>">
                        <input type="hidden" id="totalInput" name="total" value="<?= $totalDomicilio ?>">

                        <button type="submit" class="btn-contacto-submit w-100 mt-4" id="submitBtn" aria-label="Confirmar compra">
                            <div class="btn-spinner" id="btnSpinner" aria-hidden="true"></div>
                            <i class="bi bi-lock-fill" id="btnIcon" aria-hidden="true"></i>
                            <span id="btnText">Confirmar compra</span>
                        </button>

                        <a href="index.php?seccion=carrito" class="btn btn-hero-outline w-100 mt-3 justify-content-center" aria-label="Volver al carrito">
                            <i class="bi bi-arrow-left"></i>
                            Volver al carrito
                        </a>

                        <div class="checkout-trust">
                            <span><i class="bi bi-shield-check"></i> Compra protegida</span>
                            <span><i class="bi bi-arrow-repeat"></i> Cambios sin cargo</span>
                        </div>

                    </div>

                </aside>

            </div>

        </form>

    </div>

</section>
