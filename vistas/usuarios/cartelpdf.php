<?php
if (empty($reporte)) {
    echo "<div class='container mt-5 pt-5'><div class='alert alert-danger'>No se encontró la información del reporte. Ingrese desde su perfil o historial de reportes.</div></div>";
    return;
}
$folioFormateado = "RUPE-" . str_pad($reporte['id_reporte'], 4, '0', STR_PAD_LEFT);
$fotoMostrar = !empty($reporte['foto']) ? $reporte['foto'] : (!empty($reporte['foto_mascota']) ? $reporte['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');

// Construimos la URL destino del QR
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$reporte['id_reporte']}";

// Si existe archivo PNG físico en uploads/qr/, lo usamos; si no, generamos Data URI limpio con chillerlan/php-qrcode
if (!empty($reporte['codigo_qr']) && str_ends_with($reporte['codigo_qr'], '.png') && file_exists($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr'])) {
    $imgSrcQR = $reporte['codigo_qr'];
} else {    
    $imgSrcQR = (new \chillerlan\QRCode\QRCode())->render($urlQR);
}
?>

<main class="container py-5 mt-5 contenedor-principal-cartel">
    <section class="pt-2">
        <!-- Barra de acciones superior (oculta al imprimir) -->
        <div class="alert alert-success d-flex flex-wrap justify-content-between align-items-center shadow-sm mb-3 no-print">
            <div>
                <h5 class="fw-bold mb-1">¡Reporte de Extravío Registrado! Folio: <span class="badge bg-dark"><?= $folioFormateado ?></span></h5>
                <p class="mb-0 small">Guarde su folio para seguimiento. Descargue o imprima el cartel con código QR en una hoja tamaño carta.</p>
            </div>
            <div class="mt-2 mt-md-0 d-flex gap-2">
                <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=pdf&folio=<?= $reporte['id_reporte'] ?>" class="btn btn-danger fw-bold shadow-sm">
                    Descargar PDF
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-dark fw-bold">
                    Imprimir
                </button>
                <a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="btn btn-secondary fw-bold">
                    Ir a mi Perfil
                </a>
            </div>
        </div>

        <!-- DISEÑO DEL CARTEL (Ajustado para caber en 1 sola hoja Carta) -->
        <div id="cartel-pdf" class="mx-auto bg-white border border-4 border-danger rounded-3 p-4 shadow" style="max-width: 780px;">

            <!-- Encabezado Rojo de Alerta -->
            <div class="bg-danger text-white text-center py-2 rounded-2 mb-3 encabezado-cartel">
                <h1 class="display-5 fw-bold mb-0" style="letter-spacing: 3px;">¡SE BUSCA!</h1>
                <p class="fs-5 mb-0 fw-semibold">MASCOTA EXTRAVIADA • FOLIO: <?= $folioFormateado ?></p>
            </div>

            <!-- Nombre y Fotografía Principal -->
            <div class="row align-items-center g-4 mb-3">
                <div class="col-6 text-center">
                    <img src="<?= htmlspecialchars($fotoMostrar) ?>"
                        alt="<?= htmlspecialchars($reporte['nombre_mascota']) ?>"
                        class="img-fluid rounded border border-2 border-dark"
                        style="height: 310px; width: 100%; object-fit: cover;">
                </div>

                <div class="col-6">
                    <h2 class="display-6 fw-bold text-uppercase text-danger border-bottom border-2 pb-2 mb-3">
                        <?= htmlspecialchars($reporte['nombre_mascota'] ?: 'Sin Nombre') ?>
                    </h2>
                    <ul class="list-unstyled mb-0" style="font-size: 1.05rem; line-height: 1.45">
                        <li class="mb-1"><strong>Especie / Raza:</strong> <?= htmlspecialchars(($reporte['nom_especie'] ?? '') . ' - ' . ($reporte['nom_raza'] ?? 'No especificada')) ?></li>
                        <li class="mb-1"><strong>Sexo / Edad:</strong> <?= htmlspecialchars(($reporte['sexos'] ?? '-') . ' • ' . (!empty($reporte['edad']) ? $reporte['edad'] . ' años' : '-')) ?></li>
                        <li class="mb-1"><strong>Color:</strong> <?= htmlspecialchars($reporte['color'] ?? 'No especificado') ?></li>
                        <li class="mb-1"><strong>Tamaño:</strong> <?= htmlspecialchars($reporte['tamano'] ?? 'No especificado') ?></li>
                        <?php if (!empty($reporte['sena_particular'])): ?>
                            <li class="p-2 bg-light border rounded mt-2">
                                <strong>Señas particulares:</strong> <?= htmlspecialchars($reporte['sena_particular']) ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Datos del Extravío -->
            <div class="bg-light border rounded p-3 px-3 mb-3 small" style="font-size: 1rem;">
                <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">Visto por última vez:</h6>
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

            <!-- Pie del Cartel con Código QR -->
            <div class="row align-items-center border-top pt-2">
                <div class="col-8">
                    <h5 class="fw-bold text-danger mb-1">¿Lo has visto o lo tienes resguardado?</h5>
                    <p class="mb-1 small text-muted" style="font-size: 0.92rem;">
                        Escanea el código QR con la cámara de tu celular para ingresar al sistema RUPE, registrar la ubicación donde lo viste y dejar tu teléfono de contacto de forma segura.
                    </p>
                    <div class="small text-muted">Sistema RUPE • Folio de seguimiento: <strong><?= $folioFormateado ?></strong></div>
                </div>

                <!-- Código QR como imagen nativa para garantizar impresión y PDF -->
                <div class="col-4 text-center">
                    <div class="d-inline-block p-1 border bg-white rounded">
                        <img src="<?= htmlspecialchars($imgSrcQR) ?>" alt="Código QR <?= $folioFormateado ?>" style="width: 130px; height: 130px;">
                    </div>
                    <div class="fw-bold small mt-1" style="font-size: 0.8rem;">ESCANEA AQUÍ</div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cloudflare.com"></script>

<script>
    function descargarPDF() {
        const elemento = document.getElementById('cartel-pdf');
        const opciones = {
            margin: 0.25,
            filename: 'Cartel_<?= $folioFormateado ?>_<?= preg_replace("/[^a-zA-Z0-9]/", "", $reporte['nombre_mascota'] ?? 'Mascota') ?>.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            }
        };
        html2pdf().set(opciones).from(elemento).save();
    }
</script>

<style>
    @media print {
        @page {
            size: letter portrait;
            margin: 0;
        }

        /* Ocultar barra fija superior, pie de página y botones */
        .no-print,
        .fixed-top,
        header,
        nav,
        footer {
            display: none !important;
        }

        body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0.35in !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .contenedor-principal-cartel,
        section {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        #cartel-pdf {
            border: 4px solid #dc3545 !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            page-break-inside: avoid !important;
        }
    }
</style>