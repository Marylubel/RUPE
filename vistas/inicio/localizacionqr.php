<?php $urlRegreso = !empty($_SESSION['id_usuario']) ? '/RUPE/index.php?c=perfilusuario&a=Inicio' : '/RUPE/index.php'; ?>

<main class="container py-5 mt-5">
    <section class="pt-3">
        <div class="col-lg-8 mx-auto">

            <?php if (empty($reporte)): ?>
                <div class="alert alert-warning text-center p-5 shadow-sm rounded-3">
                    <h4 class="fw-bold">Reporte no encontrado o ya finalizado</h4>
                    <p class="mb-3">El código QR escaneado no corresponde a un reporte de extravío activo en el sistema RUPE.</p>
                    <a href="<?= $urlRegreso ?>" class="btn btn-primary fw-bold">
                        <?= !empty($_SESSION['id_usuario']) ? 'Regresar a mi Perfil' : 'Ir al Inicio de RUPE' ?>
                    </a>
                </div>
            <?php elseif ($reporte['estado_reporte'] === 'recuperado'): ?>
                <div class="alert alert-success text-center p-5 shadow-sm rounded-3">
                    <h3 class="fw-bold">¡Buenas noticias! Esta mascota ya fue localizada</h3>
                    <p class="mb-3">El dueño de <strong><?= htmlspecialchars($reporte['nombre_mascota']) ?></strong> ya marcó este reporte como recuperado. ¡Gracias por tu interés en ayudar!</p>
                    <a href="<?= $urlRegreso ?>" class="btn btn-success fw-bold">
                        <?= !empty($_SESSION['id_usuario']) ? 'Regresar a mi Perfil' : 'Ir al Inicio de RUPE' ?>
                    </a>
                </div>
            <?php else: ?>
                <?php
                $folioFormateado = "RUPE-" . str_pad($reporte['id_reporte'], 4, '0', STR_PAD_LEFT);
                $fotoMascota = !empty($reporte['foto_reporte']) ? $reporte['foto_reporte'] : (!empty($reporte['foto_mascota']) ? $reporte['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');
                ?>

                <!-- Encabezado de Alerta -->
                <div class="p-3 mb-4 bg-danger text-white rounded-3 shadow-sm text-center">
                    <h3 class="fw-bold mb-1">¡Gracias por ayudar a localizarla!</h3>
                    <p class="mb-0 small">Folio de búsqueda: <strong><?= $folioFormateado ?></strong></p>
                </div>

                <!-- Ficha resumida de identificación de la mascota -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-4 col-sm-3 text-center">
                                <img src="<?= htmlspecialchars($fotoMascota) ?>"
                                    alt="<?= htmlspecialchars($reporte['nombre_mascota']) ?>"
                                    class="img-fluid rounded border shadow-sm"
                                    style="height: 120px; width: 120px; object-fit: cover;">
                            </div>
                            <div class="col-8 col-sm-9">
                                <h4 class="fw-bold text-danger mb-1"><?= htmlspecialchars($reporte['nombre_mascota'] ?: 'Mascota Extraviada') ?></h4>
                                <p class="small text-muted mb-1">
                                    <strong><?= htmlspecialchars(($reporte['nom_especie'] ?? '') . ' • ' . ($reporte['nom_raza'] ?? '')) ?></strong>
                                    (<?= htmlspecialchars($reporte['sexos'] ?? '') ?>, Color: <?= htmlspecialchars($reporte['color'] ?? '-') ?>)
                                </p>
                                <?php if (!empty($reporte['sena_particular'])): ?>
                                    <p class="small mb-0"><strong>Señas:</strong> <?= htmlspecialchars($reporte['sena_particular']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario exclusivo para el ciudadano que localizó a la mascota -->
                <div class="card border-0 shadow-sm rounded-3 mb-5">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="fw-bold mb-0 text-primary">Reportar Ubicación de la Mascota</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="/RUPE/controladores/localizacion.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_reporte_padre" value="<?= $reporte['id_reporte'] ?>">

                            <!-- 1. Calle donde se localizó -->
                            <div class="mb-3">
                                <label for="calle" class="form-label fw-bold">Calle y referencias donde la viste o localizaste *</label>
                                <input type="text" class="form-control" id="calle" name="calle"
                                    placeholder="Ej. Calle Hidalgo #120, esquina con Morelos, frente al parque..." required>
                            </div>

                            <!-- 2. Nombre completo y teléfono de contacto -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label for="nombre_invitado" class="form-label fw-bold">Tu nombre completo *</label>
                                    <input type="text" class="form-control" id="nombre_invitado" name="nombre_invitado"
                                        placeholder="Ej. María González Pérez" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="telefono_invitado" class="form-label fw-bold">Teléfono de contacto (10 dígitos) *</label>
                                    <input type="tel" class="form-control" id="telefono_invitado" name="telefono_invitado"
                                        maxlength="10" pattern="[0-9]{10}" placeholder="Ej. 5512345678"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                </div>
                            </div>

                            <!-- 3. Indicador de resguardo -->
                            <div class="mb-3 p-3 bg-light border rounded-3">
                                <label class="form-label fw-bold d-block mb-2">¿Tienes a la mascota en tu resguardo actualmente? *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="en_resguardo" id="resguardo_si" value="1" required>
                                    <label class="form-check-label fw-semibold text-success" for="resguardo_si">
                                        Sí, la tengo resguardada conmigo
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mt-1 mt-sm-0">
                                    <input class="form-check-input" type="radio" name="en_resguardo" id="resguardo_no" value="0" checked required>
                                    <label class="form-check-label fw-semibold text-secondary" for="resguardo_no">
                                        No, solo la vi en la calle
                                    </label>
                                </div>
                            </div>

                            <!-- 4. Fotografía de evidencia -->
                            <div class="mb-3">
                                <label for="foto" class="form-label fw-bold">Subir fotografía de la mascota (opcional)</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <div class="form-text small">Si puedes tomarle una foto ayudará al dueño a confirmar que es su mascota.</div>
                            </div>

                            <!-- 5. Comentarios breves opcionales -->
                            <div class="mb-4">
                                <label for="comentarios" class="form-label fw-semibold">Observaciones adicionales (opcional)</label>
                                <textarea class="form-control" id="comentarios" name="comentarios" rows="2"
                                    placeholder="Ej. Iba caminando hacia el sur, parece cansada pero tranquila..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm">
                                    Enviar Reporte de Localización
                                </button>
                                <a href="<?= $urlRegreso ?>" class="btn btn-outline-secondary fw-bold">
                                    Cancelar y Regresar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>