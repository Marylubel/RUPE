<?php
    $folioFormateado = "RUPE-" . str_pad($reporte['id_reporte'], 4, '0', STR_PAD_LEFT);
    $fotoMostrar = !empty($reporte['foto']) ? $reporte['foto'] : (!empty($reporte['foto_mascota']) ? $reporte['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');

    if (!empty($reporte['codigo_qr']) && str_ends_with($reporte['codigo_qr'], '.png') && file_exists($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr'])) {
        $imgSrcQR = $reporte['codigo_qr'];
    } else {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$reporte['id_reporte']}";
        $imgSrcQR = (new \chillerlan\QRCode\QRCode())->render($urlQR);
    }
?>

<main class="container py-3 mt-2 contenedor-principal-cartel">
    <section class="pt-1">
        <!-- Barra de acciones superior -->
        <div class="alert alert-success d-flex flex-wrap justify-content-between align-items-center shadow-sm mb-3 no-print">
            <div>
                <h5 class="fw-bold mb-1">¡Reporte Registrado! Guarda tu Folio: <span class="badge bg-dark fs-6"><?= $folioFormateado ?></span></h5>
                <p class="mb-0 small">Como reportaste sin cuenta, conserva este folio para consultar el muro de mascotas localizadas. Descarga o imprime tu cartel QR.</p>
            </div>
            <div class="mt-2 mt-md-0 d-flex gap-2">
                <a href="/RUPE/index.php?c=reporteinvitado&a=pdf&folio=<?= $reporte['id_reporte'] ?>" class="btn btn-danger fw-bold shadow-sm">
                    Descargar PDF
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-dark fw-bold">
                    Imprimir
                </button>
                <a href="/RUPE/index.php" class="btn btn-secondary fw-bold">
                    Volver al Inicio
                </a>
            </div>
        </div>

        <!-- DISEÑO DEL CARTEL -->
        <div id="cartel-pdf" class="mx-auto bg-white border border-4 border-danger rounded-3 p-4 shadow" style="max-width: 780px;">
            <div class="bg-danger text-white text-center py-2 rounded-2 mb-3">
                <h1 class="display-4 fw-bold mb-0" style="letter-spacing: 3px;">¡SE BUSCA!</h1>
                <p class="fs-5 mb-0 fw-semibold">MASCOTA EXTRAVIADA • FOLIO: <?= $folioFormateado ?></p>
            </div>

            <div class="row align-items-center g-4 mb-3">
                <div class="col-6 text-center">
                    <img src="<?= htmlspecialchars($fotoMostrar) ?>" 
                         alt="<?= htmlspecialchars($reporte['nombre_mascota']) ?>" 
                         class="img-fluid rounded border border-2 border-dark shadow-sm"
                         style="height: 310px; width: 100%; object-fit: cover;">
                </div>

                <div class="col-6">
                    <h2 class="display-6 fw-bold text-uppercase text-danger border-bottom border-2 pb-2 mb-3">
                        <?= htmlspecialchars($reporte['nombre_mascota'] ?: 'Sin Nombre') ?>
                    </h2>
                    <ul class="list-unstyled mb-0" style="font-size: 1.05rem; line-height: 1.45;">
                        <li class="mb-2"><strong>Especie / Raza:</strong><br><?= htmlspecialchars(($reporte['nom_especie'] ?? '') . ' - ' . ($reporte['nom_raza'] ?? 'No especificada')) ?></li>
                        <li class="mb-2"><strong>Sexo / Edad:</strong> <?= htmlspecialchars(($reporte['sexos'] ?? '-') . ' • ' . (!empty($reporte['edad']) ? $reporte['edad'] . ' años' : '-')) ?></li>
                        <li class="mb-2"><strong>Color:</strong> <?= htmlspecialchars($reporte['color'] ?? 'No especificado') ?></li>
                        <li class="mb-2"><strong>Tamaño:</strong> <?= htmlspecialchars($reporte['tamano'] ?? 'No especificado') ?></li>
                        <?php if (!empty($reporte['sena_particular'])): ?>
                            <li class="p-2 bg-light border rounded mt-2" style="font-size: 0.98rem;">
                                <strong>Señas particulares:</strong><br>
                                <?= htmlspecialchars($reporte['sena_particular']) ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="bg-light border rounded p-3 mb-3" style="font-size: 1rem;">
                <h5 class="fw-bold text-dark border-bottom pb-1 mb-2">Visto por última vez:</h5>
                <p class="mb-1">
                    <strong>Fecha y Hora:</strong> <?= date('d/m/Y', strtotime($reporte['fecha'])) ?> a las <?= substr($reporte['hora'], 0, 5) ?> hrs.
                </p>
                <p class="mb-1">
                    <strong>Lugar:</strong> 
                    <?= htmlspecialchars(trim(($reporte['calle'] ?? '') . ' ' . (!empty($reporte['entre_calle']) ? '(Entre ' . $reporte['entre_calle'] . ' y ' . $reporte['y_calle'] . ')' : ''))) ?>,
                    Col. <?= htmlspecialchars($reporte['nombre_colonia'] ?? '') ?>, 
                    <?= htmlspecialchars(($reporte['nombre_municipio'] ?? '') . ', ' . ($reporte['nombre_estado'] ?? '')) ?>.
                </p>
                <?php if (!empty($reporte['comentarios'])): ?>
                    <p class="mb-0 mt-1"><strong>Detalles adicionales:</strong> <?= htmlspecialchars($reporte['comentarios']) ?></p>
                <?php endif; ?>
            </div>

            <div class="row align-items-center border-top pt-2">
                <div class="col-8">
                    <h5 class="fw-bold text-danger mb-1">¿Lo has visto o lo tienes resguardado?</h5>
                    <p class="mb-1 text-muted" style="font-size: 0.92rem;">
                        Escanea el código QR con la cámara de tu celular para ingresar al sistema RUPE, registrar la ubicación donde lo viste y dejar tu teléfono de contacto de forma segura.
                    </p>
                    <div class="small text-muted">Sistema RUPE • Folio de seguimiento: <strong><?= $folioFormateado ?></strong></div>
                </div>

                <div class="col-4 text-center">
                    <div class="d-inline-block p-1 border bg-white rounded">
                        <img src="<?= $imgSrcQR ?>" alt="Código QR <?= $folioFormateado ?>" style="width: 130px; height: 130px; display: block;">
                    </div>
                    <div class="fw-bold small mt-1" style="font-size: 0.8rem;">ESCANEA AQUÍ</div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    @media print {
        @page { size: letter portrait; margin: 0; }
        .no-print, .fixed-top, header, nav, footer { display: none !important; }
        body { background: #fff !important; margin: 0 !important; padding: 0.35in !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .contenedor-principal-cartel, section { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        #cartel-pdf { border: 4px solid #dc3545 !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; margin: 0 auto !important; page-break-inside: avoid !important; }
    }
</style>