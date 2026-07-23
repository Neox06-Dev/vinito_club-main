<section class="contacto-section" id="contacto">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <div class="section-label">— Contacto —</div>
                <h2 class="section-title">Hablemos de <em>vinos</em></h2>
                <p class="nosotros-text">¿Tenés una consulta, querés unirte al club o simplemente querés recomendarnos un vino? Escribinos, te respondemos en menos de 24 horas.</p>
            </div>
        </div>

        <div class="row g-5">
            <!-- FORMULARIO -->
            <div class="col-lg-7">
                <form action="index.php?seccion=procesar_contacto" method="POST" class="contacto-form" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label-custom">
                                Nombre
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-custom" id="nombre" name="nombre"
                                placeholder="Tu nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label-custom">
                                Apellido
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-custom" id="apellido" name="apellido"
                                placeholder="Tu apellido" required>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label-custom">
                                Email
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control form-control-custom" id="email" name="email"
                                placeholder="tu@email.com" required>
                        </div>
                        <div class="col-12">
                            <label for="telefono" class="form-label-custom">Teléfono</label>
                            <input type="tel" class="form-control form-control-custom" id="telefono" name="telefono"
                                placeholder="+54 11 1234-5678">
                        </div>
                        <div class="col-12">
                            <label for="motivo" class="form-label-custom">
                                Motivo de contacto
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-control-custom" id="motivo" name="motivo" required>
                                <option value="" disabled selected>Seleccioná un motivo</option>
                                <option value="consulta">Consulta sobre un vino</option>
                                <option value="club">Quiero unirme al club</option>
                                <option value="mayorista">Consulta mayorista</option>
                                <option value="regalo">Quiero armar un regalo</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="mensaje" class="form-label-custom">
                                Mensaje
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-custom" id="mensaje" name="mensaje"
                                rows="5" placeholder="Contanos en qué podemos ayudarte..." required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check custom-check">
                                <input class="form-check-input" type="checkbox" id="suscribir" name="suscribir" value="1">
                                <label class="form-check-label" for="suscribir">
                                    Quiero recibir novedades y ofertas exclusivas del club
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-contacto-submit w-100 text-center">
                                Enviar mensaje &nbsp;<i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- INFO DE CONTACTO -->
            <div class="col-lg-5">
                <div class="contacto-info-panel">
                    <h4 class="contacto-info-title">Información de contacto</h4>

                    <div class="contacto-info-item">
                        <div class="contacto-info-icon"><i class="bi bi-envelope"></i></div>
                        <div>
                            <p class="contacto-info-label">Email</p>
                            <p class="contacto-info-val">soporte@vinitoclub.com.ar</p>
                        </div>
                    </div>

                    <div class="contacto-info-item">
                        <div class="contacto-info-icon"><i class="bi bi-telephone"></i></div>
                        <div>
                            <p class="contacto-info-label">Teléfono</p>
                            <p class="contacto-info-val">+54 11 4000-1234</p>
                        </div>
                    </div>

                    <div class="contacto-info-item">
                        <div class="contacto-info-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <p class="contacto-info-label">Dirección</p>
                            <p class="contacto-info-val">Av. del Libertador 1234, Piso 3<br>Buenos Aires, Argentina</p>
                        </div>
                    </div>

                    <div class="contacto-info-item">
                        <div class="contacto-info-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <p class="contacto-info-label">Horario de atención</p>
                            <p class="contacto-info-val">Lun — Vie: 9:00 a 18:00 hs<br>Sáb: 10:00 a 14:00 hs</p>
                        </div>
                    </div>

                    <div class="contacto-social mt-4">
                        <p class="contacto-info-label">Seguinos en redes</p>
                        <div class="d-flex gap-3 mt-2">
                            <a href="https://www.instagram.com" target="_blank" class="social-link-lg" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com" target="_blank" class="social-link-lg" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.tiktok.com" target="_blank" class="social-link-lg" title="TikTok">
                                <i class="bi bi-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
