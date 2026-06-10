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

    // ── AGREGAR AL CARRITO (simulado) ────────────────────────
    document.querySelectorAll('.product-agregar').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const card = this.closest('.product-card');
            const nombre = card.querySelector('.product-name');
            if (nombre) {
                this.textContent = '✓ Agregado';
                this.style.color = '#6dbf7e';
                setTimeout(() => {
                    this.innerHTML = 'AGREGAR <i class="bi bi-plus"></i>';
                    this.style.color = '';
                }, 2000);
            }
        });
    });
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
