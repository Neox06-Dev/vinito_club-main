<?php
/* Aseguramos que $footerBase esté definido para evitar errores en rutas relativas en los enlaces del footer. Si no se ha definido previamente, lo inicializamos como una cadena vacía.
*/

$footerBase = $footerBase ?? '';
?>

<!-- FOOTER -->
<footer class="vinito-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">
                    <a href="<?= $footerBase ?>index.php">
                        <img src="<?= $footerBase ?>assets/img/logo.png" alt="Vinito Club Logo">
                    </a>
                </div>
                <p class="footer-desc">Elegimos los mejores vinos de bodegas boutique argentinas para que vos solo tengas que descorchar y disfrutar.</p>
                <div class="footer-social d-flex gap-2 mt-3">
                    <a href="https://www.instagram.com" target="_blank" class="social-link" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com" target="_blank" class="social-link" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.tiktok.com" target="_blank" class="social-link" title="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Navegar</h6>
                <ul class="footer-links">
                    <li><a href="<?= $footerBase ?>index.php?seccion=inicio">Inicio</a></li>
                    <li><a href="<?= $footerBase ?>index.php?seccion=tienda">Tienda</a></li>
                    <li><a href="<?= $footerBase ?>index.php?seccion=datos">Datos</a></li>
                    <li><a href="<?= $footerBase ?>index.php?seccion=contacto">Contacto</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Contacto</h6>
                <ul class="footer-links">
                    <li><i class="bi bi-geo-alt"></i> Av. del Libertador 1234, CABA</li>
                    <li><i class="bi bi-telephone"></i> 11 1234-5678</li>
                    <li><i class="bi bi-envelope"></i> soporte@vinitoclub.com.ar</li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="footer-heading">Recibí novedades</h6>
                <p class="footer-desc">Enterate de los nuevos vinos y ofertas exclusivas.</p>
                <div class="footer-newsletter d-flex gap-2">
                    <input type="email" class="form-control footer-input" placeholder="tu@email.com">
                    <button class="btn btn-footer-sub">Suscribirse</button>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="footer-copy">© 2026 Vinito Club. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="footer-copy">Escuela Davinci - Desarrollo Web - Nicolás González</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $footerBase ?>js/main.js"></script>
</body>

</html>