document.addEventListener('DOMContentLoaded', function () {

    // ── ACCOUNT DROPDOWN: ANIMACIÓN DE CIERRE ──────────
    document.querySelectorAll('.account-dropdown').forEach(function (dropdown) {
        const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu   = dropdown.querySelector('.dropdown-menu');
        if (!toggle || !menu || typeof bootstrap === 'undefined') return;

        const instancia = bootstrap.Dropdown.getOrCreateInstance(toggle);

        dropdown.addEventListener('hide.bs.dropdown', function (e) {
            // Si ya está animando el cierre, dejamos que Bootstrap lo oculte de verdad
            if (menu.classList.contains('closing')) {
                menu.classList.remove('closing');
                return;
            }

            e.preventDefault();
            menu.classList.add('closing');

            menu.addEventListener('animationend', function onEnd() {
                menu.removeEventListener('animationend', onEnd);
                instancia.hide();
            }, { once: true });
        });
    });

    // ── NAVBAR SCROLL ─────────────────────────────────
    const navbar = document.getElementById('mainNav');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.style.borderBottomColor = 'rgba(196,163,103,0.35)';
            } else {
                navbar.style.borderBottomColor = 'rgba(196,163,103,0.2)';
            }
        });
    }

    // ── FADE IN SCROLL ────────────────────────────────────
    const observerOpciones = {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOpciones);

    // Aplicar a product cards
    document.querySelectorAll('.product-card, .beneficio-item, .stat-number').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s ease ${i * 0.07}s, transform 0.5s ease ${i * 0.07}s`;
        observer.observe(el);
    });

    // ── SCROLL SUAVE ────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── AGREGAR AL CARRITO (AJAX) ────────────────────────────
    const carritoToastEl = document.getElementById('carritoToast');
    const carritoToastTitle = document.getElementById('carritoToastTitle');
    const carritoToastBody = document.getElementById('carritoToastBody');
    const carritoToastIcon = document.getElementById('carritoToastIcon');
    const cartCountEl = document.getElementById('cartCount');

    const carritoToast = carritoToastEl && window.bootstrap
        ? bootstrap.Toast.getOrCreateInstance(carritoToastEl, { autohide: true, delay: 2400 })
        : null;

    const mostrarToast = (mensaje, tipo = 'success') => {
        if (!carritoToastEl || !carritoToastBody || !carritoToastTitle || !carritoToastIcon) {
            return;
        }

        const config = {
            success: {
                clase: 'toast-success',
                icono: 'bi bi-bag-check-fill me-2',
                titulo: 'Producto agregado'
            },
            removed: {
                clase: 'toast-removed',
                icono: 'bi bi-bag-x-fill me-2',
                titulo: 'Producto eliminado'
            },
            error: {
                clase: 'toast-error',
                icono: 'bi bi-exclamation-triangle-fill me-2',
                titulo: 'No se pudo completar'
            },
            warning: {
                clase: 'toast-error',
                icono: 'bi bi-exclamation-circle-fill me-2',
                titulo: 'Carrito vacío'
            }
        };

        const { clase, icono, titulo } = config[tipo] || config.success;

        carritoToastEl.classList.remove('toast-success', 'toast-error', 'toast-removed');
        carritoToastEl.classList.add(clase);
        carritoToastIcon.className = icono;
        carritoToastTitle.textContent = titulo;
        carritoToastBody.textContent = mensaje;

        carritoToast?.show();
    };

    const actualizarContadorCarrito = (count) => {
        if (!cartCountEl) return;

        cartCountEl.textContent = String(count);
        cartCountEl.classList.toggle('is-empty', count <= 0);
    };

    const ejecutarAgregarCarrito = async (btn) => {
        const idVino = btn.dataset.id;

        if (!idVino || btn.dataset.loading === '1') {
            return;
        }

        // Bloqueo simple contra doble clic mientras viaja la petición,
        // sin tocar el contenido ni el aspecto del botón.
        btn.dataset.loading = '1';

        try {
            const response = await fetch('acciones/carrito/agregar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: new URLSearchParams({ id: idVino }).toString()
            });

            const data = await response.json().catch(() => null);

            if (!response.ok || !data || !data.success) {
                throw new Error((data && data.message) || 'No se pudo agregar el producto.');
            }

            actualizarContadorCarrito(Number(data.cantidad) || 0);
            mostrarToast(data.message || 'Producto agregado al carrito.');
        } catch (error) {
            mostrarToast(error.message || 'No se pudo agregar el producto.', 'error');
        } finally {
            delete btn.dataset.loading;
        }
    };

    document.querySelectorAll('.js-agregar-carrito').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            ejecutarAgregarCarrito(this);
        });

        if (btn.getAttribute('role') === 'button') {
            btn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    ejecutarAgregarCarrito(this);
                }
            });
        }
    });

    // ── CARRITO: SUMAR / RESTAR / ELIMINAR / VACIAR ──────────
    const carritoColumna     = document.getElementById('carritoColumnaProductos');
    const resumenCantidadEl  = document.getElementById('resumenCantidad');
    const resumenSubtotalEl  = document.getElementById('resumenSubtotal');
    const resumenEnvioEl     = document.getElementById('resumenEnvio');
    const resumenEnvioNotaEl = document.getElementById('resumenEnvioNota');
    const resumenTotalEl     = document.getElementById('resumenTotal');
    const vaciarCarritoBtn   = document.getElementById('vaciarCarrito');

    const ENVIO_GRATIS_DESDE = 25000;
    const COSTO_ENVIO_BASE   = 3500;

    const formatearPrecio = (valor) => {
        return Math.round(Number(valor) || 0).toLocaleString('es-AR', { maximumFractionDigits: 0 });
    };

    const actualizarResumenCarrito = (cantidad, subtotal) => {
        const cant = Number(cantidad) || 0;
        const sub  = Number(subtotal) || 0;
        const costoEnvio = (sub > 0 && sub < ENVIO_GRATIS_DESDE) ? COSTO_ENVIO_BASE : 0;
        const total = sub + costoEnvio;

        if (resumenCantidadEl) {
            resumenCantidadEl.textContent = `${cant} ${cant === 1 ? 'vino' : 'vinos'}`;
        }
        if (resumenSubtotalEl) {
            resumenSubtotalEl.textContent = `$ ${formatearPrecio(sub)}`;
        }
        if (resumenEnvioEl) {
            resumenEnvioEl.textContent = costoEnvio > 0 ? `$ ${formatearPrecio(costoEnvio)}` : 'Gratis';
        }
        if (resumenEnvioNotaEl) {
            if (sub <= 0) {
                resumenEnvioNotaEl.style.display = 'none';
            } else if (costoEnvio > 0) {
                resumenEnvioNotaEl.style.display = '';
                resumenEnvioNotaEl.className = 'carrito-envio-nota';
                resumenEnvioNotaEl.textContent = `Te faltan $ ${formatearPrecio(ENVIO_GRATIS_DESDE - sub)} para envío gratis`;
            } else {
                resumenEnvioNotaEl.style.display = '';
                resumenEnvioNotaEl.className = 'carrito-envio-nota carrito-envio-nota--gratis';
                resumenEnvioNotaEl.innerHTML = '<i class="bi bi-check-circle-fill"></i> Envío gratis';
            }
        }
        if (resumenTotalEl) {
            resumenTotalEl.textContent = `$ ${formatearPrecio(total)}`;
        }

        actualizarContadorCarrito(cant);
    };

    const mostrarCarritoVacio = () => {
        if (!carritoColumna) return;

        carritoColumna.innerHTML =
            '<div class="carrito-empty text-center">' +
                '<i class="bi bi-bag-x"></i>' +
                '<h2>Tu carrito está vacío</h2>' +
                '<p>Descubrí etiquetas únicas y empezá a armar tu selección.</p>' +
                '<a href="index.php?seccion=tienda" class="btn-hero-primary">Explorar catálogo</a>' +
            '</div>';
    };

    const llamarAccionCarrito = async (accion, idVino) => {
        const response = await fetch(`acciones/carrito/${accion}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ id: idVino }).toString()
        });

        const data = await response.json().catch(() => null);

        if (!response.ok || !data || !data.success) {
            throw new Error((data && data.message) || 'No se pudo actualizar el carrito.');
        }

        return data;
    };

    if (carritoColumna) {
        carritoColumna.addEventListener('click', async function (e) {
            const btnMinus  = e.target.closest('.qty-minus');
            const btnPlus   = e.target.closest('.qty-plus');
            const btnRemove = e.target.closest('.carrito-item-remove');

            const btn = btnMinus || btnPlus || btnRemove;
            if (!btn || btn.dataset.loading === '1') return;

            const item = btn.closest('.carrito-item');
            if (!item) return;

            const idVino = item.dataset.id;
            if (!idVino) return;

            btn.dataset.loading = '1';

            const accion = btnMinus ? 'restar' : (btnRemove ? 'eliminar' : 'sumar');

            try {
                const data = await llamarAccionCarrito(accion, idVino);

                if (accion === 'eliminar') {
                    item.remove();
                    mostrarToast('Producto eliminado del carrito.', 'removed');
                } else {
                    const precio = Number(item.dataset.precio) || 0;
                    let cantidadItem = (parseInt(item.dataset.cantidad, 10) || 0) + (accion === 'sumar' ? 1 : -1);

                    if (cantidadItem <= 0) {
                        item.remove();
                        mostrarToast('Producto eliminado del carrito.', 'removed');
                    } else {
                        item.dataset.cantidad = String(cantidadItem);

                        const qtyValueEl = item.querySelector('.js-qty-value');
                        if (qtyValueEl) qtyValueEl.textContent = String(cantidadItem);

                        const subtotalItemEl = item.querySelector('.js-subtotal-item');
                        if (subtotalItemEl) subtotalItemEl.textContent = `$ ${formatearPrecio(precio * cantidadItem)}`;
                    }
                }

                actualizarResumenCarrito(data.cantidad, data.subtotal);

                if (!carritoColumna.querySelector('.carrito-item')) {
                    mostrarCarritoVacio();
                }

                actualizarEstadoCheckout();
            } catch (error) {
                mostrarToast(error.message || 'No se pudo actualizar el carrito.', 'error');
            } finally {
                delete btn.dataset.loading;
            }
        });
    }

    if (vaciarCarritoBtn) {
        vaciarCarritoBtn.addEventListener('click', async function () {
            if (vaciarCarritoBtn.dataset.loading === '1') return;

            if (!carritoColumna || !carritoColumna.querySelector('.carrito-item')) {
                return;
            }

            vaciarCarritoBtn.dataset.loading = '1';

            try {
                const data = await llamarAccionCarrito('vaciar', '0');

                mostrarCarritoVacio();
                actualizarResumenCarrito(data.cantidad, data.subtotal);
                actualizarEstadoCheckout();
                mostrarToast('Carrito vaciado.', 'removed');
            } catch (error) {
                mostrarToast(error.message || 'No se pudo vaciar el carrito.', 'error');
            } finally {
                delete vaciarCarritoBtn.dataset.loading;
            }
        });
    }

    function actualizarEstadoCheckout() {

        const checkoutBtn = document.getElementById('checkoutBtn');

        if (!checkoutBtn) return;

        const cantidadProductos = document.querySelectorAll('.carrito-item').length;

        if (cantidadProductos === 0) {

            checkoutBtn.classList.add('disabled');
            checkoutBtn.removeAttribute('href');
            checkoutBtn.setAttribute('aria-disabled', 'true');

        } else {

            checkoutBtn.classList.remove('disabled');
            checkoutBtn.href = 'index.php?seccion=checkout';
            checkoutBtn.removeAttribute('aria-disabled');

        }
    }

    const checkoutBtnEl = document.getElementById('checkoutBtn');
    if (checkoutBtnEl) {
        checkoutBtnEl.addEventListener('click', function (e) {
            if (checkoutBtnEl.classList.contains('disabled') || checkoutBtnEl.disabled) {
                e.preventDefault();
                mostrarToast('Agregá al menos un producto para finalizar la compra.', 'warning');
            }
        });
    }

    actualizarEstadoCheckout();

    // ── PANEL ADMIN: CONTADORES ANIMADOS ─────────────────────
    const statNumbers = document.querySelectorAll('.stat-card h2');
    if (statNumbers.length) {
        const animarContador = (el) => {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duracion = 900;
            const inicio = performance.now();

            const paso = (ahora) => {
                const progreso = Math.min((ahora - inicio) / duracion, 1);
                // easeOutCubic
                const easedProgreso = 1 - Math.pow(1 - progreso, 3);
                el.textContent = Math.floor(easedProgreso * target);
                if (progreso < 1) {
                    requestAnimationFrame(paso);
                } else {
                    el.textContent = target;
                }
            };
            requestAnimationFrame(paso);
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animarContador(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });

        statNumbers.forEach(el => {
            el.dataset.target = (el.textContent.replace(/\D/g, '') || '0');
            el.textContent = '0';
            counterObserver.observe(el);
        });
    }

    // ── PANEL ADMIN: ENLACE ACTIVO EN SIDEBAR ────────────────
    const adminLinks = document.querySelectorAll('.admin-nav a');
    if (adminLinks.length) {
        const pagina = window.location.pathname.split('/').pop() || 'index.php';
        adminLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === pagina) {
                link.classList.add('active');
            }
        });
    }

    // ── PANEL ADMIN: MODAL DE CONFIRMACIÓN (ELIMINAR VINO) ───
    const deleteModal     = document.getElementById('deleteModal');
    const deleteOverlay   = document.getElementById('deleteOverlay');
    const deleteNombreEl  = document.getElementById('deleteModalNombre');
    const deleteConfirmar = document.getElementById('deleteConfirmar');
    const deleteCancelar  = document.getElementById('deleteCancelar');

    if (deleteModal && deleteOverlay && deleteConfirmar) {
        const abrirModalEliminar = (nombre, href) => {
            if (deleteNombreEl) deleteNombreEl.textContent = nombre;
            deleteConfirmar.setAttribute('href', href);
            deleteModal.classList.add('active');
            deleteOverlay.classList.add('active');
            document.body.classList.add('admin-nav-open');
        };

        const cerrarModalEliminar = () => {
            deleteModal.classList.remove('active');
            deleteOverlay.classList.remove('active');
            document.body.classList.remove('admin-nav-open');
        };

        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', () => {
                const nombre = btn.dataset.nombre || 'este vino';
                const href   = btn.dataset.href || '#';
                abrirModalEliminar(nombre, href);
            });
        });

        if (deleteCancelar) deleteCancelar.addEventListener('click', cerrarModalEliminar);
        deleteOverlay.addEventListener('click', cerrarModalEliminar);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal.classList.contains('active')) {
                cerrarModalEliminar();
            }
        });
    }

    // ── PANEL ADMIN: MENÚ HAMBURGUESA (RESPONSIVE) ───────────
    const adminToggle  = document.getElementById('adminToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const adminOverlay = document.getElementById('adminOverlay');

    if (adminToggle && adminSidebar && adminOverlay) {
        const toggleIconEl = adminToggle.querySelector('i');

        const abrirMenu = () => {
            adminSidebar.classList.add('active');
            adminOverlay.classList.add('active');
            adminToggle.classList.add('active');
            adminToggle.setAttribute('aria-expanded', 'true');
            adminToggle.setAttribute('aria-label', 'Cerrar menú');
            if (toggleIconEl) toggleIconEl.className = 'bi bi-x-lg';
            document.body.classList.add('admin-nav-open');
        };

        const cerrarMenu = () => {
            adminSidebar.classList.remove('active');
            adminOverlay.classList.remove('active');
            adminToggle.classList.remove('active');
            adminToggle.setAttribute('aria-expanded', 'false');
            adminToggle.setAttribute('aria-label', 'Abrir menú');
            if (toggleIconEl) toggleIconEl.className = 'bi bi-list';
            document.body.classList.remove('admin-nav-open');
        };

        adminToggle.addEventListener('click', () => {
            if (adminSidebar.classList.contains('active')) {
                cerrarMenu();
            } else {
                abrirMenu();
            }
        });

        // Cerrar al hacer click fuera (overlay)
        adminOverlay.addEventListener('click', cerrarMenu);

        // Cerrar al elegir una opción del menú en mobile
        adminSidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 991) cerrarMenu();
            });
        });

        // Cerrar con la tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && adminSidebar.classList.contains('active')) {
                cerrarMenu();
            }
        });

        // Cerrar automáticamente si se agranda la ventana a escritorio
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991 && adminSidebar.classList.contains('active')) {
                cerrarMenu();
            }
        });
    }
});

// ── LOGIN PAGE ────────────────────────────────────────────────
(function () {
    'use strict';

    // Solo ejecutar si está en la página de login
    if (!document.getElementById('loginForm')) return;

    // — Toggle contraseña —
    const toggleBtn  = document.getElementById('togglePassword');
    const passwordIn = document.getElementById('password');
    

    if (toggleBtn && passwordIn) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordIn.type === 'password';
            passwordIn.type  = isPassword ? 'text' : 'password';
            if (toggleIcon) {
                toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
            toggleBtn.title = isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña';
            toggleBtn.setAttribute('aria-pressed', String(isPassword));
        });
    }

    // — Validación cliente —
    const form       = document.getElementById('loginForm');
    const emailIn    = document.getElementById('email');
    const emailErr   = document.getElementById('emailError');
    const passErr    = document.getElementById('passwordError');
    const jsAlert    = document.getElementById('jsAlert');
    const jsAlertMsg = document.getElementById('jsAlertMsg');
    const submitBtn  = document.getElementById('submitBtn');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnIcon    = document.getElementById('btnIcon');
    const btnText    = document.getElementById('btnText');

    function showError(input, msgEl) {
        input.classList.add('is-invalid-custom');
        msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        input.classList.remove('is-invalid-custom');
        msgEl.classList.remove('show');
    }

    // Limpieza en tiempo real
    if (emailIn) {
        emailIn.addEventListener('input', function () {
            if (this.validity.valid) clearError(this, emailErr);
        });
    }
    if (passwordIn) {
        passwordIn.addEventListener('input', function () {
            if (this.value.length > 0) clearError(this, passErr);
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            let valid = true;

            // Validar email
            if (!emailIn.value.trim() || !emailIn.validity.valid) {
                showError(emailIn, emailErr);
                valid = false;
            } else {
                clearError(emailIn, emailErr);
            }

            // Validar password
            if (!passwordIn.value) {
                showError(passwordIn, passErr);
                valid = false;
            } else {
                clearError(passwordIn, passErr);
            }

            if (!valid) {
                e.preventDefault();
                jsAlertMsg.textContent = 'Por favor, completá todos los campos correctamente.';
                jsAlert.style.display  = 'flex';
                emailIn.focus();
                return;
            }

            // Estado loading
            jsAlert.style.display    = 'none';
            btnSpinner.style.display = 'block';
            btnIcon.style.display    = 'none';
            btnText.textContent      = 'Verificando…';
            submitBtn.disabled       = true;
        });
    }

    // Auto-ocultar alertas PHP después de 6s
    const phpAlert = document.querySelector('.alert-vinito--error:not(#jsAlert), .alert-vinito--success');
    if (phpAlert) {
        setTimeout(function () {
            phpAlert.style.transition = 'opacity 0.5s';
            phpAlert.style.opacity    = '0';
            setTimeout(function () { phpAlert.remove(); }, 500);
        }, 6000);
    }
})();

// ── REGISTRO PAGE ─────────────────────────────────────────────
(function () {
    'use strict';

    const form = document.getElementById('registroForm');
    if (!form) return;

    const nombreIn    = document.getElementById('nombre');
    const nombreErr   = document.getElementById('nombreError');
    const emailIn     = document.getElementById('email');
    const emailErr    = document.getElementById('emailError');
    const telefonoIn  = document.getElementById('telefono');
    const telefonoErr = document.getElementById('telefonoError');
    const passwordIn  = document.getElementById('password');
    const passErr     = document.getElementById('passwordError');
    const password2In = document.getElementById('password2');
    const pass2Err    = document.getElementById('password2Error');
    const pass2ErrMsg = document.getElementById('password2ErrorMsg');
    const jsAlert     = document.getElementById('jsAlert');
    const jsAlertMsg  = document.getElementById('jsAlertMsg');
    const submitBtn   = document.getElementById('submitBtn');
    const btnSpinner  = document.getElementById('btnSpinner');
    const btnIcon     = document.getElementById('btnIcon');
    const btnText     = document.getElementById('btnText');

    // Togglear mostrar/ocultar contraseña (contraseña y confirmar contraseña)
    [['togglePassword', 'password'], ['togglePassword2', 'password2']].forEach(([btnId, inputId]) => {
        const btn   = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        if (!btn || !input) return;

        btn.addEventListener('click', function () {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.title = isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña';
            btn.setAttribute('aria-pressed', String(isPassword));
        });
    });

    function showError(input, msgEl) {
        input.classList.add('is-invalid-custom');
        msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        input.classList.remove('is-invalid-custom');
        msgEl.classList.remove('show');
    }

    function validarPassword2() {
        if (!password2In.value) {
            pass2ErrMsg.textContent = 'La confirmación de la contraseña no puede estar vacía.';
            showError(password2In, pass2Err);
            return false;
        }
        if (password2In.value !== passwordIn.value) {
            pass2ErrMsg.textContent = 'Las contraseñas no coinciden.';
            showError(password2In, pass2Err);
            return false;
        }
        clearError(password2In, pass2Err);
        return true;
    }

    // Limpieza / validación en tiempo real
    if (nombreIn) {
        nombreIn.addEventListener('input', function () {
            if (this.value.trim().length >= 2) clearError(this, nombreErr);
        });
    }
    if (emailIn) {
        emailIn.addEventListener('input', function () {
            if (this.validity.valid) clearError(this, emailErr);
        });
    }
    if (telefonoIn) {
        telefonoIn.addEventListener('input', function () {

            const telefono = this.value.trim();

            if ( telefono === '' || telefono.length >= 6 ) {
                clearError(this, telefonoErr);
            }

        });
    }
    if (passwordIn) {
        passwordIn.addEventListener('input', function () {
            if (this.value.length >= 6) clearError(this, passErr);
            // Si ya se había tocado la confirmación, revalidamos en vivo
            if (password2In.value) validarPassword2();
        });
    }
    if (password2In) {
        password2In.addEventListener('input', validarPassword2);
    }

    form.addEventListener('submit', function (e) {
        let valid = true;

        if (!nombreIn.value.trim() || nombreIn.value.trim().length < 2) {
            showError(nombreIn, nombreErr);
            valid = false;
        } else {
            clearError(nombreIn, nombreErr);
        }

        if (!emailIn.value.trim() || !emailIn.validity.valid) {
            showError(emailIn, emailErr);
            valid = false;
        } else {
            clearError(emailIn, emailErr);
        }

        if (telefonoIn.value.trim() && telefonoIn.value.trim().length < 6) {
            showError(telefonoIn, telefonoErr);
            valid = false;
        } else {
            clearError(telefonoIn, telefonoErr);
        }

        if (!passwordIn.value.trim()) {

            passErr.textContent = 'Ingresá una contraseña.';
            showError(passwordIn, passErr);
            valid = false;

        } else if (passwordIn.value.length < 6) {

            passErr.textContent = 'La contraseña debe tener al menos 6 caracteres.';
            showError(passwordIn, passErr);
            valid = false;

        } else {

            clearError(passwordIn, passErr);

        }

        if (!validarPassword2()) {
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            jsAlertMsg.textContent = 'Por favor, revisá los campos marcados en rojo.';
            jsAlert.style.display  = 'flex';
            return;
        }

        jsAlert.style.display    = 'none';
        btnSpinner.style.display = 'block';
        btnIcon.style.display    = 'none';
        btnText.textContent      = 'Creando cuenta…';
        submitBtn.disabled       = true;
    });

    // Auto-ocultar alertas PHP después de 6s
    const phpAlertRegistro = document.querySelector('.alert-vinito--error:not(#jsAlert), .alert-vinito--success');
    if (phpAlertRegistro) {
        setTimeout(function () {
            phpAlertRegistro.style.transition = 'opacity 0.5s';
            phpAlertRegistro.style.opacity    = '0';
            setTimeout(function () { phpAlertRegistro.remove(); }, 500);
        }, 6000);
    }
})();

// ── EDITAR PERFIL PAGE ──────────────────────────────────────────
(function () {
    'use strict';

    const form = document.getElementById('editarPerfilForm');
    if (!form) return;

    const nombreIn    = document.getElementById('nombre');
    const nombreErr   = document.getElementById('nombreError');
    const emailIn     = document.getElementById('email');
    const emailErr    = document.getElementById('emailError');
    const telefonoIn  = document.getElementById('telefono');
    const telefonoErr = document.getElementById('telefonoError');
    const jsAlert     = document.getElementById('jsAlert');
    const jsAlertMsg  = document.getElementById('jsAlertMsg');
    const submitBtn   = document.getElementById('submitBtn');
    const btnSpinner  = document.getElementById('btnSpinner');
    const btnIcon     = document.getElementById('btnIcon');
    const btnText     = document.getElementById('btnText');

    // — Vista previa en vivo —
    const previewAvatar   = document.getElementById('previewAvatar');
    const previewNombre   = document.getElementById('previewNombre');
    const previewEmail    = document.getElementById('previewEmail');
    const previewTelefono = document.getElementById('previewTelefono');

    function actualizarPreview() {
        const nombre = nombreIn.value.trim();
        const email = emailIn.value.trim();
        const telefono = telefonoIn.value.trim();

        if (previewNombre) previewNombre.textContent = nombre || 'Tu nombre';
        if (previewAvatar) previewAvatar.textContent = nombre ? nombre.charAt(0).toUpperCase() : '?';
        if (previewEmail) previewEmail.textContent = email || 'tu@email.com';
        if (previewTelefono) previewTelefono.textContent = telefono || 'Sin teléfono';
    }

    [nombreIn, emailIn, telefonoIn].forEach((input) => {
        if (input) input.addEventListener('input', actualizarPreview);
    });

    function showError(input, msgEl) {
        input.classList.add('is-invalid-custom');
        msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        input.classList.remove('is-invalid-custom');
        msgEl.classList.remove('show');
    }

    // Limpieza en tiempo real
    if (nombreIn) {
        nombreIn.addEventListener('input', function () {
            if (this.value.trim().length >= 2) clearError(this, nombreErr);
        });
    }
    if (emailIn) {
        emailIn.addEventListener('input', function () {
            if (this.validity.valid) clearError(this, emailErr);
        });
    }
    if (telefonoIn) {
        telefonoIn.addEventListener('input', function () {

            const telefono = this.value.trim();

            if (telefono === '' || telefono.length >= 6) {
                clearError(this, telefonoErr);
            }

        });
    }

    form.addEventListener('submit', function (e) {
        let valid = true;

        if (!nombreIn.value.trim() || nombreIn.value.trim().length < 2) {
            showError(nombreIn, nombreErr);
            valid = false;
        } else {
            clearError(nombreIn, nombreErr);
        }

        if (!emailIn.value.trim() || !emailIn.validity.valid) {
            showError(emailIn, emailErr);
            valid = false;
        } else {
            clearError(emailIn, emailErr);
        }

        if (telefonoIn.value.trim() && telefonoIn.value.trim().length < 6) {
            showError(telefonoIn, telefonoErr);
            valid = false;
        } else {
            clearError(telefonoIn, telefonoErr);
        }

        if (!valid) {
            e.preventDefault();
            jsAlertMsg.textContent = 'Por favor, revisá los campos marcados en rojo.';
            jsAlert.style.display  = 'flex';
            return;
        }

        jsAlert.style.display    = 'none';
        btnSpinner.style.display = 'block';
        btnIcon.style.display    = 'none';
        btnText.textContent      = 'Guardando…';
        submitBtn.disabled       = true;
    });

    // Auto-ocultar alertas PHP después de 6s
    const phpAlertPerfil = document.querySelector('.alert-vinito--error:not(#jsAlert), .alert-vinito--success');
    if (phpAlertPerfil) {
        setTimeout(function () {
            phpAlertPerfil.style.transition = 'opacity 0.5s';
            phpAlertPerfil.style.opacity    = '0';
            setTimeout(function () { phpAlertPerfil.remove(); }, 500);
        }, 6000);
    }
})();
// ── CHECKOUT PAGE ────────────────────────────────────────────────
(function () {
    'use strict';

    const form = document.getElementById('checkoutForm');
    if (!form) return;

    const nombreIn    = document.getElementById('nombre');
    const nombreErr   = document.getElementById('nombreError');
    const emailIn     = document.getElementById('email');
    const emailErr    = document.getElementById('emailError');
    const telefonoIn  = document.getElementById('telefono');
    const telefonoErr = document.getElementById('telefonoError');

    const direccionWrap = document.getElementById('direccionWrap');
    const calleIn        = document.getElementById('calle');
    const calleErr       = document.getElementById('calleError');
    const ciudadIn       = document.getElementById('ciudad');
    const ciudadErr      = document.getElementById('ciudadError');
    const cpIn           = document.getElementById('codigo_postal');
    const cpErr          = document.getElementById('cpError');

    const tarjetaWrap    = document.getElementById('tarjetaWrap');
    const numTarjetaIn   = document.getElementById('numero_tarjeta');
    const numTarjetaErr  = document.getElementById('tarjetaError');
    const vencIn         = document.getElementById('vencimiento_tarjeta');
    const vencErr        = document.getElementById('vencimientoError');
    const cvvIn          = document.getElementById('cvv_tarjeta');
    const cvvErr         = document.getElementById('cvvError');
    const cuotasSelect   = document.getElementById('cuotas');
    const cuotasDetalle  = document.getElementById('cuotasDetalle');

    const entregaRadios = form.querySelectorAll('input[name="tipo_entrega"]');
    const pagoRadios    = form.querySelectorAll('input[name="metodo_pago"]');

    const resumenEnvio = document.getElementById('resumenEnvio');
    const resumenTotal = document.getElementById('resumenTotal');
    const envioInput   = document.getElementById('envioInput');
    const totalInput   = document.getElementById('totalInput');
    const subtotal     = parseFloat(document.querySelector('input[name="subtotal"]').value || '0');
    const costoEnvioDomicilio = parseFloat(envioInput.value || '0');

    const jsAlert    = document.getElementById('jsAlert');
    const jsAlertMsg = document.getElementById('jsAlertMsg');
    const submitBtn  = document.getElementById('submitBtn');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnIcon    = document.getElementById('btnIcon');
    const btnText    = document.getElementById('btnText');

    function formatearMoneda(valor) {
        return '$ ' + Math.round(valor).toLocaleString('es-AR');
    }

    function actualizarSeleccion(radios) {
        radios.forEach((radio) => {
            const option = radio.closest('.checkout-option');
            if (!option) return;
            option.classList.toggle('is-selected', radio.checked);
        });
    }

    function tipoEntregaActual() {
        const seleccionado = form.querySelector('input[name="tipo_entrega"]:checked');
        return seleccionado ? seleccionado.value : 'domicilio';
    }

    function metodoPagoActual() {
        const seleccionado = form.querySelector('input[name="metodo_pago"]:checked');
        return seleccionado ? seleccionado.value : 'tarjeta';
    }

    function actualizarCuotas() {
        if (!cuotasSelect || !cuotasDetalle) return;

        const total = subtotal + (tipoEntregaActual() === 'domicilio' ? costoEnvioDomicilio : 0);
        const cantidadCuotas = parseInt(cuotasSelect.value, 10) || 1;
        const montoCuota = total / cantidadCuotas;

        cuotasDetalle.textContent = cantidadCuotas === 1
            ? `1 pago de ${formatearMoneda(total)}`
            : `${cantidadCuotas} cuotas de ${formatearMoneda(montoCuota)}`;
    }

    function actualizarResumen() {
        const esDomicilio = tipoEntregaActual() === 'domicilio';
        const envio = esDomicilio ? costoEnvioDomicilio : 0;
        const total = subtotal + envio;

        resumenEnvio.textContent = envio > 0 ? formatearMoneda(envio) : 'Gratis';
        resumenTotal.textContent = formatearMoneda(total);
        envioInput.value = envio;
        totalInput.value = total;

        actualizarCuotas();
    }

    function actualizarVisibilidadBloques() {
        const esDomicilio = tipoEntregaActual() === 'domicilio';
        const esTarjeta   = metodoPagoActual() === 'tarjeta';

        direccionWrap.classList.toggle('is-collapsed', !esDomicilio);
        tarjetaWrap.classList.toggle('is-collapsed', !esTarjeta);

        // Los campos ocultos no deben bloquear la validación ni el envío
        const camposDireccion = [
            [calleIn, calleErr],
            [ciudadIn, ciudadErr],
            [cpIn, cpErr]
        ];
        camposDireccion.forEach(([input, errEl]) => {
            input.required = esDomicilio;
            if (!esDomicilio) clearError(input, errEl);
        });

        const camposTarjeta = [
            [numTarjetaIn, numTarjetaErr],
            [vencIn, vencErr],
            [cvvIn, cvvErr]
        ];
        camposTarjeta.forEach(([input, errEl]) => {
            input.required = esTarjeta;
            if (!esTarjeta) clearError(input, errEl);
        });
    }

    function showError(input, msgEl) {
        if (!input || !msgEl) return;
        input.classList.add('is-invalid-custom');
        msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        if (!input || !msgEl) return;
        input.classList.remove('is-invalid-custom');
        msgEl.classList.remove('show');
    }

    // Selección de entrega
    entregaRadios.forEach((radio) => {
        radio.addEventListener('change', function () {
            actualizarSeleccion(entregaRadios);
            actualizarVisibilidadBloques();
            actualizarResumen();
        });
    });

    // Selección de pago
    pagoRadios.forEach((radio) => {
        radio.addEventListener('change', function () {
            actualizarSeleccion(pagoRadios);
            actualizarVisibilidadBloques();
        });
    });

    // Recalcular detalle de cuotas al cambiar la cantidad
    if (cuotasSelect) {
        cuotasSelect.addEventListener('change', actualizarCuotas);
    }

    // Formateo automático del número de tarjeta
    if (numTarjetaIn) {
        numTarjetaIn.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').slice(0, 16);
            this.value = valor.replace(/(.{4})/g, '$1 ').trim();
            if (valor.length >= 13) clearError(this, numTarjetaErr);
        });
    }

    // Formateo automático del vencimiento MM/AA
    if (vencIn) {
        vencIn.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '').slice(0, 4);
            if (valor.length > 2) valor = valor.slice(0, 2) + '/' + valor.slice(2);
            this.value = valor;
            if (valor.length === 5) clearError(this, vencErr);
        });
    }

    if (cvvIn) {
        cvvIn.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
            if (this.value.length >= 3) clearError(this, cvvErr);
        });
    }

    // Limpieza en tiempo real de campos base
    if (nombreIn) {
        nombreIn.addEventListener('input', function () {
            if (this.value.trim().length >= 2) clearError(this, nombreErr);
        });
    }
    if (emailIn) {
        emailIn.addEventListener('input', function () {
            if (this.validity.valid) clearError(this, emailErr);
        });
    }
    if (telefonoIn) {
        telefonoIn.addEventListener('input', function () {
            if (this.value.trim().length >= 6) clearError(this, telefonoErr);
        });
    }
    if (calleIn) calleIn.addEventListener('input', function () { if (this.value.trim()) clearError(this, calleErr); });
    if (ciudadIn) ciudadIn.addEventListener('input', function () { if (this.value.trim()) clearError(this, ciudadErr); });
    if (cpIn) cpIn.addEventListener('input', function () { if (this.value.trim()) clearError(this, cpErr); });

    // Estado inicial
    actualizarSeleccion(entregaRadios);
    actualizarSeleccion(pagoRadios);
    actualizarVisibilidadBloques();
    actualizarCuotas();

    form.addEventListener('submit', function (e) {
        let valid = true;
        const esDomicilio = tipoEntregaActual() === 'domicilio';
        const esTarjeta   = metodoPagoActual() === 'tarjeta';

        if (!nombreIn.value.trim() || nombreIn.value.trim().length < 2) {
            showError(nombreIn, nombreErr);
            valid = false;
        } else {
            clearError(nombreIn, nombreErr);
        }

        if (!emailIn.value.trim() || !emailIn.validity.valid) {
            showError(emailIn, emailErr);
            valid = false;
        } else {
            clearError(emailIn, emailErr);
        }

        if (!telefonoIn.value.trim() || telefonoIn.value.trim().length < 6) {
            showError(telefonoIn, telefonoErr);
            valid = false;
        } else {
            clearError(telefonoIn, telefonoErr);
        }

        if (esDomicilio) {
            if (!calleIn.value.trim()) { showError(calleIn, calleErr); valid = false; } else clearError(calleIn, calleErr);
            if (!ciudadIn.value.trim()) { showError(ciudadIn, ciudadErr); valid = false; } else clearError(ciudadIn, ciudadErr);
            if (!cpIn.value.trim()) { showError(cpIn, cpErr); valid = false; } else clearError(cpIn, cpErr);
        }

        if (esTarjeta) {
            if (numTarjetaIn.value.replace(/\s/g, '').length < 13) { showError(numTarjetaIn, numTarjetaErr); valid = false; } else clearError(numTarjetaIn, numTarjetaErr);
            if (vencIn.value.length < 5) { showError(vencIn, vencErr); valid = false; } else clearError(vencIn, vencErr);
            if (cvvIn.value.length < 3) { showError(cvvIn, cvvErr); valid = false; } else clearError(cvvIn, cvvErr);
        }

        if (!valid) {
            e.preventDefault();
            jsAlertMsg.textContent = 'Por favor, revisá los campos marcados en rojo.';
            jsAlert.style.display  = 'flex';
            jsAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        jsAlert.style.display    = 'none';
        btnSpinner.style.display = 'block';
        btnIcon.style.display    = 'none';
        btnText.textContent      = 'Procesando…';
        submitBtn.disabled       = true;
    });

    // Auto-ocultar alertas PHP después de 6s
    const phpAlertCheckout = document.querySelector('.alert-vinito--error:not(#jsAlert), .alert-vinito--success');
    if (phpAlertCheckout) {
        setTimeout(function () {
            phpAlertCheckout.style.transition = 'opacity 0.5s';
            phpAlertCheckout.style.opacity    = '0';
            setTimeout(function () { phpAlertCheckout.remove(); }, 500);
        }, 6000);
    }
})();

// ── DASHBOARD ADMIN (gráfico de ventas) ─────────────────────────
(function () {
    'use strict';

    const canvas = document.getElementById('ventasChart');
    if (!canvas || typeof Chart === 'undefined' || !window.ventasPorDiaData) return;

    const datos = window.ventasPorDiaData;

    const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    const etiquetas = Object.keys(datos).map((fecha) => {
        const d = new Date(fecha + 'T00:00:00');
        return diasSemana[d.getDay()] + ' ' + d.getDate();
    });

    const valores = Object.values(datos);

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Ventas',
                data: valores,
                backgroundColor: 'rgba(196,163,103,0.55)',
                hoverBackgroundColor: 'rgba(196,163,103,0.85)',
                borderRadius: 6,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => '$ ' + ctx.parsed.y.toLocaleString('es-AR')
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(245,245,237,0.65)' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(196,163,103,0.1)' },
                    ticks: {
                        color: 'rgba(245,245,237,0.65)',
                        callback: (valor) => '$ ' + Number(valor).toLocaleString('es-AR')
                    }
                }
            }
        }
    });
})();

// ── EDITAR DIRECCIÓN ────────────────────────────────────────────────
(function () {
    'use strict';

    const form = document.getElementById('editarDireccionForm');
    if (!form) return;

    const calleIn  = document.getElementById('calle');
    const calleErr = document.getElementById('calleError');
    const ciudadIn  = document.getElementById('ciudad');
    const ciudadErr = document.getElementById('ciudadError');
    const cpIn      = document.getElementById('codigo_postal');
    const cpErr     = document.getElementById('cpError');
    const jsAlert   = document.getElementById('jsAlertDir');
    const jsAlertMsg = document.getElementById('jsAlertDirMsg');
    const submitBtn  = document.getElementById('submitBtnDir');
    const btnSpinner = document.getElementById('btnSpinnerDir');
    const btnIcon    = document.getElementById('btnIconDir');
    const btnText    = document.getElementById('btnTextDir');

    // Vista previa en vivo
    const previewDireccion = document.getElementById('previewDireccion');

    function actualizarPreviewDir() {
        const calle  = calleIn ? calleIn.value.trim() : '';
        const ciudad = ciudadIn ? ciudadIn.value.trim() : '';
        const cp     = cpIn ? cpIn.value.trim() : '';
        const ref    = document.getElementById('referencia') ? document.getElementById('referencia').value.trim() : '';

        let preview = '';
        if (calle)  preview += calle;
        if (ciudad) preview += (preview ? ', ' : '') + ciudad;
        if (cp)     preview += ' (CP ' + cp + ')';
        if (ref)    preview += ' - ' + ref;

        if (previewDireccion) {
            previewDireccion.textContent = preview || 'Tu dirección';
        }
    }

    [calleIn, ciudadIn, cpIn, document.getElementById('referencia')].forEach(function (input) {
        if (input) input.addEventListener('input', actualizarPreviewDir);
    });

    function showError(input, msgEl) {
        if (input) input.classList.add('is-invalid-custom');
        if (msgEl) msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        if (input) input.classList.remove('is-invalid-custom');
        if (msgEl) msgEl.classList.remove('show');
    }

    // Limpieza en tiempo real
    if (calleIn) {
        calleIn.addEventListener('input', function () {
            if (this.value.trim().length >= 3) clearError(this, calleErr);
        });
    }
    if (ciudadIn) {
        ciudadIn.addEventListener('input', function () {
            if (this.value.trim().length >= 2) clearError(this, ciudadErr);
        });
    }
    if (cpIn) {
        cpIn.addEventListener('input', function () {
            if (this.value.trim().length >= 1) clearError(this, cpErr);
        });
    }

    form.addEventListener('submit', function (e) {
        let valid = true;

        if (!calleIn.value.trim() || calleIn.value.trim().length < 3) {
            showError(calleIn, calleErr);
            valid = false;
        } else {
            clearError(calleIn, calleErr);
        }

        if (!ciudadIn.value.trim() || ciudadIn.value.trim().length < 2) {
            showError(ciudadIn, ciudadErr);
            valid = false;
        } else {
            clearError(ciudadIn, ciudadErr);
        }

        if (!cpIn.value.trim()) {
            showError(cpIn, cpErr);
            valid = false;
        } else {
            clearError(cpIn, cpErr);
        }

        if (!valid) {
            e.preventDefault();
            jsAlertMsg.textContent = 'Por favor, revisá los campos marcados en rojo.';
            jsAlert.style.display  = 'flex';
            return;
        }

        jsAlert.style.display    = 'none';
        btnSpinner.style.display = 'block';
        btnIcon.style.display    = 'none';
        btnText.textContent      = 'Guardando…';
        submitBtn.disabled       = true;
    });

    // Auto-ocultar alertas PHP después de 6s
    const phpAlertDir = document.querySelector('.alert-vinito--error:not(#jsAlertDir), .alert-vinito--success');
    if (phpAlertDir) {
        setTimeout(function () {
            phpAlertDir.style.transition = 'opacity 0.5s';
            phpAlertDir.style.opacity    = '0';
            setTimeout(function () { phpAlertDir.remove(); }, 500);
        }, 6000);
    }
})();

// ── SIDEBAR DE FILTROS (TIENDA) ──────────────────────────────────────
(function () {
    'use strict';

    const btnOpen    = document.getElementById('btnFiltrosMobile');
    const btnClose   = document.getElementById('btnCloseFiltros');
    const panel      = document.getElementById('filtrosPanel');
    const overlay    = document.getElementById('filtrosOverlay');
    const body       = document.body;

    if (!btnOpen || !panel || !overlay) return;

    function openFiltros() {
        panel.classList.add('active');
        overlay.classList.add('active');
        body.style.overflow = 'hidden'; // Prevenir scroll del fondo
    }

    function closeFiltros() {
        panel.classList.remove('active');
        overlay.classList.remove('active');
        body.style.overflow = '';
    }

    btnOpen.addEventListener('click', openFiltros);
    
    if (btnClose) {
        btnClose.addEventListener('click', closeFiltros);
    }

    overlay.addEventListener('click', closeFiltros);

    // Abrir automáticamente si venimos de aplicar un filtro (indicador en URL)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('filtros') === 'abiertos') {
        // Solo en mobile/tablet (donde el botón es visible)
        if (window.getComputedStyle(btnOpen).display !== 'none') {
            openFiltros();
        }
    }

    // Cerrar con la tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && panel.classList.contains('active')) {
            closeFiltros();
        }
    });
})();

// ── ADMIN: AGREGAR VINO ──────────────────────────────────────────────
(function () {
    'use strict';

    const form = document.getElementById('vinoForm');
    if (!form) return;

    const inputs = {
        nombre: { in: document.getElementById('nombre'), err: document.getElementById('nombreError') },
        descripcion: { in: document.getElementById('descripcion'), err: document.getElementById('descripcionError') },
        precio: { in: document.getElementById('precio'), err: document.getElementById('precioError') },
        stock: { in: document.getElementById('stock'), err: document.getElementById('stockError') },
        volumen: { in: document.getElementById('volumen_ml'), err: document.getElementById('volumenError') },
        imagen: { in: document.getElementById('imagen'), err: document.getElementById('imagenError') },
        bodega: { in: document.getElementById('bodega'), err: document.getElementById('bodegaError') },
        region: { in: document.getElementById('region_id'), err: document.getElementById('regionError') },
        anio: { in: document.getElementById('anio_cosecha'), err: document.getElementById('anioError') },
        temp: { in: document.getElementById('temperatura_servicio'), err: document.getElementById('tempError') },
        varietal: { in: document.getElementById('varietal_id'), err: document.getElementById('varietalError') }
    };

    const jsAlert    = document.getElementById('jsAlertVino');
    const jsAlertMsg = document.getElementById('jsAlertVinoMsg');
    const submitBtn  = document.getElementById('submitBtnVino');
    const btnSpinner = document.getElementById('btnSpinnerVino');
    const btnIcon    = document.getElementById('btnIconVino');
    const btnText    = document.getElementById('btnTextVino');

    function showError(input, msgEl) {
        if (input) input.classList.add('is-invalid-custom');
        if (msgEl) msgEl.classList.add('show');
    }

    function clearError(input, msgEl) {
        if (input) input.classList.remove('is-invalid-custom');
        if (msgEl) msgEl.classList.remove('show');
    }

    // Limpieza en tiempo real para todos los inputs
    Object.values(inputs).forEach(obj => {
        if (obj.in) {
            obj.in.addEventListener('input', function () {
                if (this.value.trim() !== "") clearError(this, obj.err);
            });
            obj.in.addEventListener('change', function () {
                if (this.value !== "" && this.value !== "0") clearError(this, obj.err);
            });
        }
    });

    form.addEventListener('submit', function (e) {
        let valid = true;
        let firstInvalid = null;

        // Validar campos requeridos
        for (const [key, obj] of Object.entries(inputs)) {
            if (obj.in && obj.in.hasAttribute('required')) {
                if (!obj.in.value || obj.in.value.trim() === "") {
                    showError(obj.in, obj.err);
                    valid = false;
                    if (!firstInvalid) firstInvalid = obj.in;
                } else {
                    clearError(obj.in, obj.err);
                }
            }
        }

        if (!valid) {
            e.preventDefault();
            jsAlertMsg.textContent = 'Por favor, completá todos los campos obligatorios.';
            jsAlert.style.display  = 'flex';
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        // Estado loading
        jsAlert.style.display    = 'none';
        if (btnSpinner) btnSpinner.style.display = 'inline-block';
        if (btnIcon) btnIcon.style.display    = 'none';
        if (btnText) btnText.textContent      = 'Guardando...';
        if (submitBtn) submitBtn.disabled     = true;
    });
})();
