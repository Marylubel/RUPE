<?php $urlRegreso = !empty($_SESSION['id_usuario']) ? '/RUPE/index.php?c=perfilusuario&a=Inicio' : '/RUPE/index.php'; ?>

<?php 
    $tieneSesion = !empty($_SESSION['id_usuario']);
    $urlRegreso  = $tieneSesion ? '/RUPE/index.php?c=perfilusuario&a=Inicio' : '/RUPE/index.php';
    // Si tiene sesión (barra fixed-top) lleva mt-5 py-5; si es público exterior lleva mt-2 py-3
    $clasesMain  = $tieneSesion ? 'container py-5 mt-3' : 'container py-3 mt-2';
?>

<main class="<?= $clasesMain ?>">
    <section class="<?= $tieneSesion ? 'pt-3' : 'pt-1' ?>">
        <div class="py-2 px-3 mb-3 text-black rounded-3 shadow-sm" style="background-color: #75b0d7;">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Mascotas Localizadas y Avistamientos</h4>
                    <p class="mb-0 small">Consulta las mascotas reportadas como vistas o resguardadas por ciudadanos. Si reconoces a tu mascota, ingresa tu folio de extravío para ver los datos de contacto.</p>
                </div>
                <a href="<?= $urlRegreso ?>" class="btn btn-light btn-light btn-sm py-1 px-2 fw-bold" style="font-size: 0.8rem;">
                    <?= !empty($_SESSION['id_usuario']) ? 'Regresar a mi Perfil' : 'Regresar al Inicio' ?>
                </a>
            </div>
        </div>

        <?php if (empty($localizadas)): ?>
            <div class="alert alert-light border text-center p-5 shadow-sm rounded-3">
                <h4 class="text-muted mb-2">Aún no hay reportes de mascotas localizadas</h4>
                <p class="text-muted mb-0">Cuando algún ciudadano reporte una mascota vista o resguardada aparecerá aquí.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($localizadas as $item): ?>
                    <?php
                        $fotoMostrar = !empty($item['foto_avistamiento'])
                            ? $item['foto_avistamiento']
                            : (!empty($item['foto_mascota']) ? $item['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');
                        $yaRecuperada = ($item['estado_padre'] === 'recuperado');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <div class="position-relative">
                                <img src="<?= htmlspecialchars($fotoMostrar) ?>"
                                     class="card-img-top bg-light"
                                     alt="Mascota localizada"
                                     style="height: 240px; width: 100%; object-fit: cover; object-position: top;">

                                <?php if ($yaRecuperada): ?>
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2 px-3 py-2 fs-6 shadow">
                                        RECUPERADA POR SU DUEÑO
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2 px-3 py-2 fs-6 shadow">
                                        LOCALIZADA
                                    </span>
                                    <?php if ($item['en_resguardo'] == 1): ?>
                                        <span class="badge bg-warning text-dark position-absolute bottom-0 end-0 m-2 px-2 py-1 shadow-sm">
                                            En resguardo ciudadano
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-1">
                                    <?= htmlspecialchars(!empty($item['nombre_mascota']) ? $item['nombre_mascota'] : 'Mascota Localizada') ?>
                                </h5>
                                <p class="text-muted small mb-2">
                                    <?= htmlspecialchars(($item['nom_especie'] ?? '') . ' • ' . ($item['nom_raza'] ?? 'Raza no especificada')) ?>
                                </p>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><strong>Color / Tamaño:</strong> <?= htmlspecialchars(($item['color'] ?? '-') . ' • ' . ($item['tamano'] ?? '-')) ?></li>
                                    <li class="mb-1"><strong>Fecha reporte:</strong> <?= date('d/m/Y', strtotime($item['fecha'])) ?></li>
                                </ul>
                            </div>

                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <?php if ($yaRecuperada): ?>
                                    <div class="alert alert-success py-2 px-3 mb-0 text-center small fw-bold">
                                        ¡Esta mascota ya regresó felizmente con su familia!
                                    </div>
                                <?php else: ?>
                                    <button type="button"
                                            class="btn btn-outline-danger w-100 fw-bold"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidarFolio"
                                            onclick="prepararModalFolio(<?= $item['id_reporte'] ?>)">
                                        ¿Es tu mascota? Ingresa tu folio
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Modal para validar Folio -->
<div class="modal fade" id="modalValidarFolio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Validar Folio de Extravío</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted">
                    Por seguridad y privacidad, ingresa tu <strong>Folio de Reporte de Extravío</strong> (ej. <code>RUPE-0001</code> o <code>1</code>) para desbloquear el teléfono y ubicación exacta de la persona que reportó a la mascota.
                </p>

                <form id="formValidarFolio" onsubmit="consultarFolio(event)">
                    <input type="hidden" id="modal_id_avistamiento" name="id_avistamiento">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" id="input_folio" name="folio" placeholder="Ej. RUPE-0001" required>
                        <button class="btn btn-danger fw-bold px-4" type="submit">Verificar</button>
                    </div>
                </form>

                <div id="alerta-folio-error" class="alert alert-warning small mb-0" style="display: none;"></div>

                <div id="resultado-contacto" class="bg-light border border-success rounded-3 p-3 mt-3" style="display: none;">
                    <h6 class="fw-bold text-success border-bottom pb-2 mb-2">¡Folio Verificado! Datos de Contacto:</h6>
                    <p class="mb-1 small"><strong>Persona que reporta:</strong> <span id="res-nombre"></span></p>
                    <p class="mb-1 small"><strong>Teléfono:</strong> <a id="res-telefono-link" href="#" class="fw-bold fs-6 text-decoration-none"><span id="res-telefono"></span></a></p>
                    <p class="mb-1 small"><strong>Estatus:</strong> <span id="res-resguardo" class="badge"></span></p>
                    <p class="mb-1 small"><strong>Ubicación exacta:</strong> <span id="res-calle"></span></p>
                    <p class="mb-0 small text-muted" id="res-comentarios"></p>
                                        <hr class="my-2">
                    <div class="d-grid mt-2">
                        <button type="button" id="btn-confirmar-recuperacion" class="btn btn-success btn-sm fw-bold shadow-sm" onclick="marcarComoRecuperada()">
                            ¡Sí, es mi mascota! Confirmar que ya la recuperé
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para que el dueño valide su Folio y vea los datos de contacto -->
<div class="modal fade" id="modalValidarFolio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Validar Folio de Extravío</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted">
                    Por seguridad y privacidad, ingresa tu <strong>Folio de Reporte de Extravío</strong> (ej. <code>RUPE-0001</code> o <code>1</code>) para desbloquear el teléfono y ubicación exacta de la persona que reportó a la mascota.
                </p>

                <form id="formValidarFolio" onsubmit="consultarFolio(event)">
                    <input type="hidden" id="modal_id_avistamiento" name="id_avistamiento">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" id="input_folio" name="folio" placeholder="Ej. RUPE-0001" required>
                        <button class="btn btn-danger fw-bold px-4" type="submit">Verificar</button>
                    </div>
                </form>

                <!-- Mensaje de error si el folio no es válido -->
                <div id="alerta-folio-error" class="alert alert-warning small mb-0" style="display: none;"></div>

                <!-- Caja de resultado con los datos desbloqueados -->
                <div id="resultado-contacto" class="bg-light border border-success rounded-3 p-3 mt-3" style="display: none;">
                    <h6 class="fw-bold text-success border-bottom pb-2 mb-2">¡Folio Verificado! Datos de Contacto:</h6>
                    <p class="mb-1 small"><strong>Persona que reporta:</strong> <span id="res-nombre"></span></p>
                    <p class="mb-1 small"><strong>Teléfono:</strong> <a id="res-telefono-link" href="#" class="fw-bold fs-6 text-decoration-none"><span id="res-telefono"></span></a></p>
                    <p class="mb-1 small"><strong>Estatus:</strong> <span id="res-resguardo" class="badge"></span></p>
                    <p class="mb-1 small"><strong>Calle / Referencias:</strong> <span id="res-calle"></span></p>
                    <p class="mb-0 small text-muted" id="res-comentarios"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let modalFolioObj = null;

    function prepararModalFolio(idAvistamiento) {
        document.getElementById('modal_id_avistamiento').value = idAvistamiento;
        document.getElementById('input_folio').value = '';
        document.getElementById('alerta-folio-error').style.display = 'none';
        document.getElementById('resultado-contacto').style.display = 'none';

        if (!modalFolioObj) {
            modalFolioObj = new bootstrap.Modal(document.getElementById('modalValidarFolio'));
        }
        modalFolioObj.show();
    }

    function consultarFolio(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('formValidarFolio'));
        const alertaErr = document.getElementById('alerta-folio-error');
        const cajaRes = document.getElementById('resultado-contacto');

        alertaErr.style.display = 'none';
        cajaRes.style.display = 'none';

        fetch('/RUPE/index.php?c=localizacion&a=verificarFolio', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.ok && resp.datos) {
                    const d = resp.datos;
                    document.getElementById('res-nombre').textContent = d.nombre_invitado;
                    document.getElementById('res-telefono').textContent = d.telefono_invitado;
                    document.getElementById('res-telefono-link').href = 'tel:' + d.telefono_invitado;


                    const zonaCompleta = [d.calle, d.nombre_colonia ? 'Col. ' + d.nombre_colonia : '', d.nombre_municipio].filter(Boolean).join(', ');
                    document.getElementById('res-calle').textContent = d.calle;
                    document.getElementById('res-comentarios').textContent = d.comentarios ? 'Nota: ' + d.comentarios : '';

                    const badgeResg = document.getElementById('res-resguardo');
                    if (String(d.en_resguardo) === '1') {
                        badgeResg.className = 'badge bg-success';
                        badgeResg.textContent = 'La tiene en su resguardo';
                    } else {
                        badgeResg.className = 'badge bg-secondary';
                        badgeResg.textContent = 'Solo avistamiento en vía pública';
                    }
                    cajaRes.style.display = 'block';
                } else {
                    alertaErr.textContent = resp.mensaje || 'Folio no válido.';
                    alertaErr.style.display = 'block';
                }
            })
            .catch(() => {
                alertaErr.textContent = 'Error de conexión al verificar el folio.';
                alertaErr.style.display = 'block';
            });
    }
        function marcarComoRecuperada() {
        if (!confirm('¿Confirmas que esta es tu mascota y ya la recuperaste? Esto cerrará tu reporte de extravío como localizado.')) {
            return;
        }

        const formData = new FormData(document.getElementById('formValidarFolio'));
        const btn = document.getElementById('btn-confirmar-recuperacion');
        btn.disabled = true;
        btn.textContent = 'Actualizando reporte...';

        fetch('/RUPE/index.php?c=localizacion&a=confirmarRecuperacion', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.ok) {
                alert(resp.mensaje);
                window.location.reload();
            } else {
                alert(resp.mensaje || 'No se pudo completar la acción.');
                btn.disabled = false;
                btn.textContent = '¡Sí, es mi mascota! Confirmar que ya la recuperé';
            }
        })
        .catch(() => {
            alert('Error de conexión al confirmar la recuperación.');
            btn.disabled = false;
            btn.textContent = '¡Sí, es mi mascota! Confirmar que ya la recuperé';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('modalValidarFolio');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    });
</script>