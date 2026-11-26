<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../modelos/usuarios/reportesusu.php";
require_once __DIR__ . "/../../modelos/personal.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteusuControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Reporteusu();
    }

    public function Inicio()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $misMascotas  = $this->modelo->obtenerMascotasUsuario($_SESSION['id_usuario']);
        //$listaEstados = $this->modelo->obtenerEstados();
        $modelosPersonal = new Personal();
        $listaEstados = $modelosPersonal->obtenerEstados() ?? [];

        // Si hay un id, se carga el reporte y sus listas geográficas para editar
        $reporte         = null;
        $listaMunicipios = [];
        $listaColonias   = [];

        if (!empty($_GET['id'])) {
            $id_rep = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id_rep) {
                $reporte = $this->modelo->obtenerDetalleReporte($id_rep);
                if (!empty($reporte['id_estado'])) {
                    $listaMunicipios = $modelosPersonal->obtenerMunicipiosPorEstado($reporte['id_estado']) ?? [];
                }
                if (!empty($reporte['nombre_municipio'])) {
                    $listaColonias = $modelosPersonal->obtenerColoniasPorAlcaldias($reporte['nombre_municipio']) ?? [];
                }
            }
        }

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/reportextravio.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }

    public function procesarAjax()
    {
        header('Content-Type: application/json; charset=utf-8');
        $action = $_GET['action'] ?? '';
        $modeloPersonal = new Personal();

        if ($action === 'getMunicipios') {
            $estado_id = filter_input(INPUT_GET, 'estado_id', FILTER_VALIDATE_INT);
            echo json_encode($modeloPersonal->obtenerMunicipiosPorEstado($estado_id), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'getColoniasPorCP') {
            $cp = preg_replace('/[^0-9]/', '', $_GET['cp'] ?? '');
            echo json_encode($modeloPersonal->obtenerColoniasPorCP($cp), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'getColoniasPorMunicipio') {
            // En Personal se busca por nombre de alcaldía/municipio en texto
            $nombre_alcaldia = $_GET['alcaldia_nombre'] ?? $_GET['nombre_municipio'] ?? '';
            echo json_encode($modeloPersonal->obtenerColoniasPorAlcaldias($nombre_alcaldia), JSON_UNESCAPED_UNICODE);
        }
        exit();
    }


    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario  = $_SESSION['id_usuario'] ?? null;
            if (!$id_usuario) {
                header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
                exit();
            }
            $modeloPersonal = new Personal();
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');
            $colonia_nombre = trim($_POST['colonia_nombre'] ?? '');

            $id_codigo = $modeloPersonal->obtenerIdCodigoPorCP($codigo_postal);
            $id_colonia = $modeloPersonal->obtenerColoniaPorNombre($colonia_nombre, $id_codigo);

            $id_mascota  = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);
            //$id_colonia  = filter_input(INPUT_POST, 'id_colonia', FILTER_VALIDATE_INT);
            $calle       = mb_convert_case(trim($_POST['calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $entre_calle = mb_convert_case(trim($_POST['entre_calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $y_calle     = mb_convert_case(trim($_POST['y_calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $fecha       = $_POST['fecha'] ?? date('Y-m-d');
            $hora        = $_POST['hora'] ?? date('H:i');
            $comentarios = trim($_POST['comentarios'] ?? '');

            $id_reporte_edit = filter_input(INPUT_POST, 'id_reporte', FILTER_VALIDATE_INT);

            // Foto opcional específica del momento del extravío (si no sube, usa la de la mascota)
            $rutaFotoReporte = null;
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = "extravio_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['foto']['name']);
                $directorio = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/reportes/";
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $directorio . $nombreFoto)) {
                    $rutaFotoReporte = "/RUPE/uploads/reportes/" . $nombreFoto;
                }
            }
            if ($id_reporte_edit) {
                $ok = $this->modelo->modificarReporteExtravio(
                    $id_reporte_edit,
                    $id_usuario,
                    $id_mascota,
                    $id_colonia,
                    $calle,
                    $entre_calle,
                    $y_calle,
                    $rutaFotoReporte,
                    $fecha,
                    $hora,
                    $comentarios
                );
                if ($ok) {
                    header("Location: /RUPE/controladores/reportesusu.php?accion=listar");
                    exit();
                }
            } else {

                // Registrar y obtener el id_reporte generado (Folio único)
                $id_reporte = $this->modelo->registrarReporteExtravio(
                    $id_usuario,
                    $id_mascota,
                    $id_colonia,
                    $calle,
                    $entre_calle,
                    $y_calle,
                    $rutaFotoReporte,
                    $fecha,
                    $hora,
                    $comentarios
                );

                if ($id_reporte) {
                    // Guardamos la URL exacta que contendrá el Código QR en la columna codigo_qr
                    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$id_reporte}";

                    //Se crea carpeta para guardar los QR
                    $dirQR = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/qr";
                    if (!is_dir($dirQR)) {
                        mkdir($dirQR, 0777, true);
                    }
                    $nombreArchivoQR = "qr_reporte_" . $id_reporte . ".png";
                    $rutaFisicaQR = $dirQR . $nombreArchivoQR;
                    $rutaWebQR = "/RUPE/uploads/qr/" . $nombreArchivoQR;

                    //Archivo PNG del codigo QR local
                    $opcionesQR = new QROptions([
                        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                        'eccLevel' => QRCode::ECC_H,
                        'scale' => 6,
                        'imageBase64' => false,
                    ]);
                    (new QRCode($opcionesQR))->render($urlQR, $rutaFisicaQR);

                    //Guarda la ruta de la imagen QR de la base de datos
                    $this->modelo->actualizarCodigoQR($id_reporte, $rutaWebQR);
                    //Redirigir al cartel PDF
                    header("Location: /RUPE/controladores/reportesusu.php?accion=cartel&rolio=" . $id_reporte);
                    exit();
                } else {
                    echo "<script>alert('Error al generar el reporte.'); window.history.back();</script>";
                }
            }
        }
    }

    // Vista que genera el Folio, el Código QR y descarga el Cartel en PDF
    public function verCartel()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $folio = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);
        $reporte = $this->modelo->obtenerDetalleReporte($folio);

        if (!$reporte) {
            echo "<script>alert('Reporte no encontrado.'); window.location.href='/RUPE/index.php?c=perfilusuario&a=Inicio';</script>";
            exit();
        }

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/cartelpdf.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }
    public function descargarPdf()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $folio = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);
        $reporte = $this->modelo->obtenerDetalleReporte($folio);

        if (!$reporte) {
            echo "<script>alert('Reporte no encontrado.'); window.history.back();</script>";
            exit();
        }

        $folioFormateado = "RUPE-" . str_pad($reporte['id_reporte'], 4, '0', STR_PAD_LEFT);

        // 1. Convertir foto de mascota a Base64 para que Dompdf la incruste sin problemas de rutas
        $rutaWebFoto = !empty($reporte['foto']) ? $reporte['foto'] : (!empty($reporte['foto_mascota']) ? $reporte['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');
        $rutaFisicaFoto = $_SERVER['DOCUMENT_ROOT'] . $rutaWebFoto;
        $srcFotoBase64 = '';
        if (file_exists($rutaFisicaFoto)) {
            $ext = pathinfo($rutaFisicaFoto, PATHINFO_EXTENSION);
            $dataFoto = base64_encode(file_get_contents($rutaFisicaFoto));
            $srcFotoBase64 = "data:image/{$ext};base64,{$dataFoto}";
        }

        // 2. Obtener el Código QR en Base64 (SVG o PNG)
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$reporte['id_reporte']}";

        if (!empty($reporte['codigo_qr']) && str_ends_with($reporte['codigo_qr'], '.png') && file_exists($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr'])) {
            $dataQR = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr']));
            $srcQrBase64 = "data:image/png;base64,{$dataQR}";
        } else {
            $srcQrBase64 = (new QRCode())->render($urlQR);
        }

        // 3. Plantilla HTML optimizada para Dompdf (1 sola hoja tamaño Carta)
        $nombreMascota = htmlspecialchars($reporte['nombre_mascota'] ?: 'Sin Nombre');
        $especieRaza   = htmlspecialchars(($reporte['nom_especie'] ?? '') . ' - ' . ($reporte['nom_raza'] ?? 'No especificada'));
        $sexoEdad      = htmlspecialchars(($reporte['sexos'] ?? '-') . ' • ' . (!empty($reporte['edad']) ? $reporte['edad'] . ' años' : '-'));
        $color         = htmlspecialchars($reporte['color'] ?? 'No especificado');
        $tamano        = htmlspecialchars($reporte['tamano'] ?? 'No especificado');
        $senas         = htmlspecialchars($reporte['sena_particular'] ?? 'Sin señas registradas');
        $fechaHora     = date('d/m/Y', strtotime($reporte['fecha'])) . ' a las ' . substr($reporte['hora'], 0, 5) . ' hrs.';
        $lugar         = htmlspecialchars(trim(($reporte['calle'] ?? '') . ' ' . (!empty($reporte['entre_calle']) ? '(Entre ' . $reporte['entre_calle'] . ' y ' . $reporte['y_calle'] . ')' : '')) . ', Col. ' . ($reporte['nombre_colonia'] ?? '') . ', ' . ($reporte['nombre_municipio'] ?? '') . ', ' . ($reporte['nombre_estado'] ?? ''));
        $comentarios   = htmlspecialchars($reporte['comentarios'] ?? '');

        $html = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            @page { size: letter portrait; margin: 0.35in; }
            body { font-family: Helvetica, Arial, sans-serif; margin: 0; padding: 0; color: #212529; }
            .cartel { border: 4px solid #dc3545; border-radius: 10px; padding: 18px; }
            .header { background-color: #dc3545; color: #ffffff; text-align: center; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
            .header h1 { font-size: 38px; margin: 0; letter-spacing: 3px; }
            .header p { font-size: 15px; margin: 4px 0 0 0; font-weight: bold; }
            table.principal { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
            td.col-foto { width: 48%; vertical-align: top; text-align: center; padding-right: 14px; }
            td.col-datos { width: 52%; vertical-align: top; }
            .foto-mascota { width: 100%; height: 290px; object-fit: cover; border: 2px solid #212529; border-radius: 6px; }
            .nombre-mascota { font-size: 28px; color: #dc3545; text-transform: uppercase; border-bottom: 2px solid #dee2e6; padding-bottom: 6px; margin: 0 0 10px 0; }
            .dato-item { font-size: 14px; margin-bottom: 7px; line-height: 1.35; }
            .caja-senas { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 8px; font-size: 13px; margin-top: 8px; }
            .caja-extravio { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px; margin-bottom: 14px; font-size: 13.5px; line-height: 1.4; }
            .caja-extravio h3 { margin: 0 0 6px 0; font-size: 15px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px; }
            table.footer-qr { width: 100%; border-top: 1px solid #dee2e6; padding-top: 10px; border-collapse: collapse; }
            td.col-texto-qr { width: 72%; vertical-align: middle; padding-right: 10px; }
            td.col-img-qr { width: 28%; vertical-align: middle; text-align: center; }
            .qr-box { border: 1px solid #dee2e6; padding: 5px; display: inline-block; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='cartel'>
            <div class='header'>
                <h1>¡SE BUSCA!</h1>
                <p>MASCOTA EXTRAVIADA • FOLIO: {$folioFormateado}</p>
            </div>

            <table class='principal'>
                <tr>
                    <td class='col-foto'>
                        <img src='{$srcFotoBase64}' class='foto-mascota'>
                    </td>
                    <td class='col-datos'>
                        <h2 class='nombre-mascota'>{$nombreMascota}</h2>
                        <div class='dato-item'><strong>Especie / Raza:</strong><br>{$especieRaza}</div>
                        <div class='dato-item'><strong>Sexo / Edad:</strong> {$sexoEdad}</div>
                        <div class='dato-item'><strong>Color:</strong> {$color}</div>
                        <div class='dato-item'><strong>Tamaño:</strong> {$tamano}</div>
                        <div class='caja-senas'><strong>Señas particulares:</strong><br>{$senas}</div>
                    </td>
                </tr>
            </table>

            <div class='caja-extravio'>
                <h3>Visto por última vez:</h3>
                <div><strong>Fecha y Hora:</strong> {$fechaHora}</div>
                <div style='margin-top:4px;'><strong>Lugar:</strong> {$lugar}</div>
                " . (!empty($comentarios) ? "<div style='margin-top:4px;'><strong>Detalles adicionales:</strong> {$comentarios}</div>" : "") . "
            </div>

            <table class='footer-qr'>
                <tr>
                    <td class='col-texto-qr'>
                        <div style='font-size: 16px; font-weight: bold; color: #dc3545; margin-bottom: 4px;'>¿Lo has visto o lo tienes resguardado?</div>
                        <div style='font-size: 12.5px; color: #495057; line-height: 1.35;'>
                            Escanea el código QR con la cámara de tu celular para ingresar al sistema RUPE, registrar la ubicación donde lo viste y dejar tu teléfono de contacto de forma segura.
                        </div>
                        <div style='font-size: 11.5px; color: #6c757d; margin-top: 6px;'>Sistema RUPE • Folio de seguimiento: <strong>{$folioFormateado}</strong></div>
                    </td>
                    <td class='col-img-qr'>
                        <div class='qr-box'>
                            <img src='{$srcQrBase64}' style='width: 115px; height: 115px;'>
                        </div>
                        <div style='font-size: 10px; font-weight: bold; margin-top: 3px;'>ESCANEA AQUÍ</div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
    </html>";

        // 4. Generar y descargar el archivo PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        $nombreArchivo = "Cartel_{$folioFormateado}_" . preg_replace("/[^a-zA-Z0-9]/", "", $reporte['nombre_mascota'] ?? 'Mascota') . ".pdf";
        $dompdf->stream($nombreArchivo, ["Attachment" => true]);
        exit();
    }
    public function listar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $misReportes = $this->modelo->obtenerReportesUsuario($_SESSION['id_usuario']);

        foreach ($misReportes as &$rep) {
            $rep['avisos'] = $this->modelo->obtenerAvisosLocalizacion($rep['id_reporte']);
        }
        unset($rep);

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/misreportes.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }
    


    public function cambiarEstado()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $id_reporte = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $estado     = ($_GET['estado'] ?? '') === 'recuperado' ? 'recuperado' : 'activo';

        if ($id_reporte && $this->modelo->cambiarEstadoReporte($id_reporte, $_SESSION['id_usuario'], $estado)) {
            $msg = ($estado === 'recuperado') ? '¡Qué alegría! Reporte marcado como LOCALIZADO / RECUPERADO.' : 'El reporte volvió a estado EXTRAVIADO (activo).';
            echo "<script>alert('{$msg}'); window.location.href='/RUPE/controladores/reportesusu.php?accion=listar';</script>";
        } else {
            echo "<script>alert('No se pudo actualizar el estado.'); window.history.back();</script>";
        }
        exit();
    }

    public function eliminar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $id_reporte = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id_reporte && $this->modelo->eliminarReporte($id_reporte, $_SESSION['id_usuario'])) {
            echo "<script>alert('Reporte eliminado correctamente.'); window.location.href='/RUPE/controladores/reportesusu.php?accion=listar';</script>";
        } else {
            echo "<script>alert('No se pudo eliminar el reporte.'); window.history.back();</script>";
        }
        exit();
    }
}

$controlador = new ReporteusuControlador();

if (isset($_GET['action'])) {
    $controlador->procesarAjax();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'listar') {
    $controlador->listar();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'estado') {
    $controlador->cambiarEstado();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $controlador->eliminar();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'cartel') {
    $controlador->verCartel();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'pdf') {
    $controlador->descargarPdf();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->guardar();
} else {
    $controlador->Inicio();
}
