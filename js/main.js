document.addEventListener('DOMContentLoaded', function () {

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

        carritoToastEl.classList.remove('toast-success', 'toast-error');
        carritoToastEl.classList.add(tipo === 'error' ? 'toast-error' : 'toast-success');
        carritoToastIcon.className = tipo === 'error'
            ? 'bi bi-exclamation-triangle-fill me-2'
            : 'bi bi-bag-check-fill me-2';
        carritoToastTitle.textContent = tipo === 'error'
            ? 'No se pudo agregar'
            : 'Producto agregado';
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
    const resumenTotalEl     = document.getElementById('resumenTotal');
    const vaciarCarritoBtn   = document.getElementById('vaciarCarrito');

    const formatearPrecio = (valor) => {
        return Math.round(Number(valor) || 0).toLocaleString('es-AR', { maximumFractionDigits: 0 });
    };

    const actualizarResumenCarrito = (cantidad, subtotal) => {
        const cant = Number(cantidad) || 0;

        if (resumenCantidadEl) {
            resumenCantidadEl.textContent = `${cant} ${cant === 1 ? 'vino' : 'vinos'}`;
        }
        if (resumenSubtotalEl) {
            resumenSubtotalEl.textContent = `$ ${formatearPrecio(subtotal)}`;
        }
        if (resumenTotalEl) {
            resumenTotalEl.textContent = `$ ${formatearPrecio(subtotal)}`;
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
                    mostrarToast('Producto eliminado del carrito.');
                } else {
                    const precio = Number(item.dataset.precio) || 0;
                    let cantidadItem = (parseInt(item.dataset.cantidad, 10) || 0) + (accion === 'sumar' ? 1 : -1);

                    if (cantidadItem <= 0) {
                        item.remove();
                        mostrarToast('Producto eliminado del carrito.');
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
                mostrarToast('Carrito vaciado.');
            } catch (error) {
                mostrarToast(error.message || 'No se pudo vaciar el carrito.', 'error');
            } finally {
                delete vaciarCarritoBtn.dataset.loading;
            }
        });
    }

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