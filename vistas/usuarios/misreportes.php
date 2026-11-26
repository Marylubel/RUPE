<main class="container py-5 mt-3">
    <section class="pt-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h2 class="fw-bold mb-0">Mis Reportes y Seguimiento</h2>
                <p class="text-muted mb-0">Administra tus reportes de extravío, descarga su cartel QR, edita datos o actualiza su estado a localizado.</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="/RUPE/controladores/usuarios/reportesusu.php" class="btn btn-danger fw-bold shadow-sm me-2">
                    + Nuevo Reporte
                </a>
                <a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="btn btn-outline-secondary fw-bold">
                    Regresar
                </a>
            </div>
        </div>

        <?php if (empty($misReportes)): ?>
            <div class="alert alert-light border text-center p-5 shadow-sm rounded-3">
                <h4 class="text-muted mb-3">Aún no has generado ningún reporte de extravío</h4>
                <a href="/RUPE/controladores/usuarios/reportesusu.php" class="btn btn-danger px-4 fw-bold">Generar mi primer reporte</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($misReportes as $r): ?>
                    <?php
                        $folio = "RUPE-" . str_pad($r['id_reporte'], 4, '0', STR_PAD_LEFT);
                        $foto = !empty($r['foto']) ? $r['foto'] : (!empty($r['foto_mascota']) ? $r['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');
                        $esLocalizado = ($r['estado_reporte'] === 'recuperado');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <!-- Contenedor de imagen con cinta de Folio y Estado -->
                            <div class="position-relative">
                                <img src="<?= htmlspecialchars($foto) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($r['nombre_mascota'] ?? 'Mascota') ?>" 
                                     style="height: 230px; object-fit: cover;">
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2 px-3 py-2 fs-6 shadow-sm">
                                    <?= $folio ?>
                                </span>
                                <?php if ($esLocalizado): ?>
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2 px-3 py-2 shadow-sm">
                                        LOCALIZADO
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-3 py-2 shadow-sm">
                                        EXTRAVIADO
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-1">
                                    <?= htmlspecialchars(!empty($r['nombre_mascota']) ? $r['nombre_mascota'] : 'Sin nombre'); ?>
                                </h5>
                                <p class="text-muted small mb-2">
                                    <?= htmlspecialchars(($r['nom_especie'] ?? '') . ' • ' . ($r['nom_raza'] ?? 'Raza no especificada')); ?>
                                </p>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><strong>Fecha / Hora:</strong> <?= date('d/m/Y', strtotime($r['fecha'])) ?> a las <?= substr($r['hora'], 0, 5) ?> hrs</li>
                                    <li class="mb-1"><strong>Lugar:</strong> <?= htmlspecialchars(trim(($r['calle'] ?? '') . ', Col. ' . ($r['nombre_colonia'] ?? '') . ', ' . ($r['nombre_municipio'] ?? ''))) ?></li>
                                    <?php if (!empty($r['comentarios'])): ?>
                                        <li class="text-truncate mt-1" title="<?= htmlspecialchars($r['comentarios']) ?>">
                                            <strong>Comentarios:</strong> <?= htmlspecialchars($r['comentarios']) ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                               <!-- Avisos recibidos por escaneo del Código QR -->
                                <?php if (!empty($r['avisos'])): ?>
                                    <div class="mt-3 pt-2 border-top">
                                        <span class="badge bg-info text-dark mb-2">
                                            <?= count($r['avisos']) ?> <?= count($r['avisos']) === 1 ? 'Aviso recibido' : 'Avisos recibidos' ?>
                                        </span>
                                        <div class="accordion accordion-flush border rounded" id="acc-<?= $r['id_reporte'] ?>">
                                            <?php foreach ($r['avisos'] as $av): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed py-2 px-2 small fw-bold" type="button" 
                                                                data-bs-toggle="collapse" data-bs-target="#aviso-<?= $av['id_reporte'] ?>">
                                                            <?php if ($av['en_resguardo'] == 1): ?>
                                                                <span class="badge bg-success me-1">EN RESGUARDO</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary me-1">AVISTAMIENTO</span>
                                                            <?php endif; ?>
                                                            <?= htmlspecialchars($av['nombre_invitado']) ?>
                                                        </button>
                                                    </h2>
                                                    <div id="aviso-<?= $av['id_reporte'] ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= $r['id_reporte'] ?>">
                                                        <div class="accordion-body p-2 small bg-light">
                                                            <div class="mb-1"><strong>Teléfono:</strong> <a href="tel:<?= htmlspecialchars($av['telefono_invitado']) ?>"><?= htmlspecialchars($av['telefono_invitado']) ?></a></div>
                                                            <div class="mb-1"><strong>Ubicación:</strong> <?= htmlspecialchars($av['calle']) ?></div>
                                                            <div class="mb-1"><strong>Fecha/Hora:</strong> <?= date('d/m/Y', strtotime($av['fecha'])) ?> <?= substr($av['hora'], 0, 5) ?></div>
                                                            <?php if (!empty($av['comentarios'])): ?>
                                                                <div class="mb-1"><strong>Nota:</strong> <?= htmlspecialchars($av['comentarios']) ?></div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($av['foto'])): ?>
                                                                <a href="<?= htmlspecialchars($av['foto']) ?>" target="_blank" class="btn btn-outline-dark btn-sm mt-1 w-100">Ver foto enviada</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <!-- Fila 1: Ver Cartel PDF + Cambiar Estado -->
                                <div class="d-flex gap-2 mb-2">
                                    <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=cartel&folio=<?= $r['id_reporte'] ?>" 
                                       class="btn btn-dark btn-sm w-50 fw-bold">
                                       Cartel / QR
                                    </a>

                                    <?php if (!$esLocalizado): ?>
                                        <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=estado&estado=recuperado&id=<?= $r['id_reporte'] ?>" 
                                           class="btn btn-success btn-sm w-50 fw-bold"
                                           onclick="return confirm('¿Confirmas que ya localizaste/recuperaste a tu mascota?');">
                                           ¡Ya apareció!
                                        </a>
                                    <?php else: ?>
                                        <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=estado&estado=activo&id=<?= $r['id_reporte'] ?>" 
                                           class="btn btn-outline-warning text-dark btn-sm w-50 fw-bold"
                                           onclick="return confirm('¿Volver a activar este reporte como Extraviado?');">
                                           Reactivar
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Fila 2: Editar + Eliminar -->
                                <div class="d-flex gap-2">
                                    <a href="/RUPE/controladores/usuarios/reportesusu.php?id=<?= $r['id_reporte'] ?>" 
                                       class="btn btn-outline-primary btn-sm w-50 fw-bold">
                                       Editar
                                    </a>
                                    <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=eliminar&id=<?= $r['id_reporte'] ?>" 
                                       class="btn btn-outline-danger btn-sm w-50 fw-bold"
                                       onclick="return confirm('¿Estás seguro de eliminar este reporte permanentemente?');">
                                       Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
