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
