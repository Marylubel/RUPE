<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../modelos/reporteinvitado.php";
require_once __DIR__ . "/../modelos/personal.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteinvitadoControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Reporteinvitado();
    }

    // Mostrar el formulario público de extravío sin cuenta
    public function Inicio()
    {
        $listaEspecies   = $this->modelo->obtenerEspecies();
        $listaSexos      = $this->modelo->obtenerSexosMascota();
        $modelosPersonal = new Personal();
        $listaEstados    = $modelosPersonal->obtenerEstados() ?? [];

        require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        require_once __DIR__ . "/../vistas/inicio/reporteinvitado.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }

    // Endpoints AJAX para razas y ubicación SEPOMEX
    public function procesarAjax()
    {
        header('Content-Type: application/json; charset=utf-8');
        $action = $_GET['action'] ?? '';
        $modelosPersonal = new Personal();

        if ($action === 'getRazas' && isset($_GET['especie_id'])) {
            $id_especie = filter_input(INPUT_GET, 'especie_id', FILTER_VALIDATE_INT);
            echo json_encode($id_especie ? $this->modelo->obtenerRazasPorEspecie($id_especie) : [], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if ($action === 'getMunicipios' && isset($_GET['estado_id'])) {
            $id_estado = filter_input(INPUT_GET, 'estado_id', FILTER_VALIDATE_INT);
            echo json_encode($id_estado ? ($modelosPersonal->obtenerMunicipiosPorEstado($id_estado) ?? []) : [], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if ($action === 'getColoniasPorMunicipio' && isset($_GET['alcaldia_nombre'])) {
            $nombre = trim($_GET['alcaldia_nombre']);
            echo json_encode($nombre !== '' ? ($modelosPersonal->obtenerColoniasPorAlcaldias($nombre) ?? []) : [], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if ($action === 'getColoniasPorCP' && isset($_GET['cp'])) {
            $cp = preg_replace('/[^0-9]/', '', $_GET['cp']);
            echo json_encode(strlen($cp) === 5 ? ($modelosPersonal->obtenerColoniasPorCP($cp) ?? []) : [], JSON_UNESCAPED_UNICODE);
            exit();
        }

        echo json_encode([]);
        exit();
    }

    // Guardar el reporte completo del invitado
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Datos de contacto del dueño sin cuenta
            $nombre_invitado   = mb_convert_case(trim($_POST['nombre_invitado'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $telefono_invitado = preg_replace('/[^0-9]/', '', $_POST['telefono_invitado'] ?? '');

            // 2. Datos físicos de la mascota
            $nombre_mascota  = mb_convert_case(trim($_POST['nombre_mascota'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $id_catalogos    = filter_input(INPUT_POST, 'id_catalogos', FILTER_VALIDATE_INT);
            $id_sexos        = filter_input(INPUT_POST, 'id_sexos', FILTER_VALIDATE_INT);
            $edad            = trim($_POST['edad'] ?? '');
            $color           = mb_convert_case(trim($_POST['color'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $tamano          = trim($_POST['tamano'] ?? '');
            $peso_aprox      = trim($_POST['peso_aprox'] ?? '');
            $sena_particular = trim($_POST['sena_particular'] ?? '');

            // 3. Datos del extravío
            $fecha          = trim($_POST['fecha'] ?? '');
            $hora           = trim($_POST['hora'] ?? '');
            $cp             = preg_replace('/[^0-9]/', '', $_POST['codigo_postal'] ?? '');
            $colonia_nombre = trim($_POST['colonia_nombre'] ?? '');
            $calle          = mb_convert_case(trim($_POST['calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $entre_calle    = mb_convert_case(trim($_POST['entre_calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $y_calle        = mb_convert_case(trim($_POST['y_calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $comentarios    = trim($_POST['comentarios'] ?? '');

            if (empty($nombre_invitado) || strlen($telefono_invitado) < 10 || empty($nombre_mascota) || empty($fecha) || empty($hora) || empty($calle)) {
                echo "<script>alert('Por favor completa todos los campos obligatorios (contacto a 10 dígitos, datos de la mascota, fecha, hora y calle).'); window.history.back();</script>";
                exit();
            }

            // Resolver id_colonia desde SEPOMEX
            $modelosPersonal = new Personal();
            $id_colonia = null;
            if ($cp !== '' && $colonia_nombre !== '') {
                $id_colonia = $modelosPersonal->obtenerIdColonia($cp, $colonia_nombre);
            }

            // Subir fotografía de la mascota
            $rutaFoto = "/RUPE/assets/fotos/Mascota.jpg";
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = "invitado_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['foto']['name']);
                $directorio = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/reportes/";
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $directorio . $nombreFoto)) {
                    $rutaFoto = "/RUPE/uploads/reportes/" . $nombreFoto;
                }
            }

            $datosMascota = [
                'nombre'          => $nombre_mascota,
                'id_catalogos'    => $id_catalogos,
                'id_sexos'        => $id_sexos,
                'edad'            => $edad,
                'color'           => $color,
                'tamano'          => $tamano,
                'peso_aprox'      => $peso_aprox,
                'sena_particular' => $sena_particular,
                'foto'            => $rutaFoto
            ];

            $datosReporte = [
                'id_colonia'        => $id_colonia,
                'nombre_invitado'   => $nombre_invitado,
                'telefono_invitado' => $telefono_invitado,
                'calle'             => $calle,
                'entre_calle'       => $entre_calle,
                'y_calle'           => $y_calle,
                'fecha'             => $fecha,
                'hora'              => $hora,
                'comentarios'       => $comentarios
            ];

            $id_reporte = $this->modelo->registrarReporteCompletoInvitado($datosMascota, $datosReporte);

            if ($id_reporte) {
                $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$id_reporte}";

                $dirQR = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/qr/";
                if (!is_dir($dirQR)) {
                    mkdir($dirQR, 0777, true);
                }

                $nombreArchivoQR = "qr_reporte_" . $id_reporte . ".png";
                $rutaFisicaQR    = $dirQR . $nombreArchivoQR;
                $rutaWebQR       = "/RUPE/uploads/qr/" . $nombreArchivoQR;

                $opcionesQR = new QROptions([
                    'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                    'eccLevel'    => QRCode::ECC_H,
                    'scale'       => 6,
                    'imageBase64' => false,
                ]);
                (new QRCode($opcionesQR))->render($urlQR, $rutaFisicaQR);

                $this->modelo->actualizarCodigoQR($id_reporte, $rutaWebQR);

                header("Location: /RUPE/index.php?c=reporteinvitado&a=cartel&folio=" . $id_reporte);
                exit();
            } else {
                echo "<script>alert('Error al guardar el reporte.'); window.history.back();</script>";
                exit();
            }
        }
    }

    // Mostrar el Cartel generado con Folio y Código QR para el invitado
    public function cartel()
    {
        $folio   = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);
        $reporte = $folio ? $this->modelo->obtenerDetalleReporte($folio) : null;

        if (!$reporte) {
            echo "<script>alert('Reporte no encontrado.'); window.location.href='/RUPE/index.php';</script>";
            exit();
        }

        require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        require_once __DIR__ . "/../vistas/inicio/cartel_invitado.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }

    // Descarga directa del Cartel en PDF (1 hoja tamaño carta)
    public function pdf()
    {
        $folio   = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);
        $reporte = $folio ? $this->modelo->obtenerDetalleReporte($folio) : null;

        if (!$reporte) {
            echo "<script>alert('Reporte no encontrado.'); window.location.href='/RUPE/index.php';</script>";
            exit();
        }

        $folioFormateado = "RUPE-" . str_pad($reporte['id_reporte'], 4, '0', STR_PAD_LEFT);

        $rutaWebFoto = !empty($reporte['foto']) ? $reporte['foto'] : (!empty($reporte['foto_mascota']) ? $reporte['foto_mascota'] : '/RUPE/assets/fotos/Mascota.jpg');
        $rutaFisicaFoto = $_SERVER['DOCUMENT_ROOT'] . $rutaWebFoto;
        $srcFotoBase64 = '';
        if (file_exists($rutaFisicaFoto)) {
            $ext = pathinfo($rutaFisicaFoto, PATHINFO_EXTENSION);
            $dataFoto = base64_encode(file_get_contents($rutaFisicaFoto));
            $srcFotoBase64 = "data:image/{$ext};base64,{$dataFoto}";
        }

        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $urlQR = "{$protocolo}://{$host}/RUPE/index.php?c=localizacion&a=Inicio&folio={$reporte['id_reporte']}";
        if (!empty($reporte['codigo_qr']) && str_ends_with($reporte['codigo_qr'], '.png') && file_exists($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr'])) {
            $dataQR = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $reporte['codigo_qr']));
            $srcQrBase64 = "data:image/png;base64,{$dataQR}";
        } else {
            $srcQrBase64 = (new QRCode())->render($urlQR);
        }
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





¡SE BUSCA!
MASCOTA EXTRAVIADA • FOLIO: {$folioFormateado}
	Visto por última vez:
Fecha y Hora: {$fechaHora}
Lugar: {$lugar}
" . (!empty($comentarios) ? "Detalles adicionales: {$comentarios}" : "") . "
	";
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
}
if (basename($_SERVER['SCRIPT_FILENAME']) === 'reporteinvitado.php') {
    $ctrl = new ReporteinvitadoControlador();
    if (isset($_GET['action'])) {
        $ctrl->procesarAjax();
    } elseif (isset($_GET['a']) && $_GET['a'] === 'cartel') {
        $ctrl->cartel();
    } elseif (isset($_GET['a']) && $_GET['a'] === 'pdf') {
        $ctrl->pdf();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ctrl->guardar();
    } else {
        $ctrl->Inicio();
    }
}
