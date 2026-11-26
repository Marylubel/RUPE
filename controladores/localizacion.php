<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../modelos/localizacion.php";

class LocalizacionControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Localizacion();
    }

    // Vista pública que abre al escanear el Código QR
    public function Inicio()
    {
        $folio   = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);
        $reporte = $folio ? $this->modelo->obtenerReportePorFolio($folio) : null;

        //Se da la opción de regreso en el muro de localizados, si tiene cuentar regresa al perfil
        //en caso de ingresar sin entrar a la cuenta, lo regresa a la vista de inicio
        if (!empty($_SESSION['id_usuario'])) {
            require_once __DIR__ . "/../vistas/inicio/encabezadoingreso.php";
        } else {
            require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        }

        require_once __DIR__ . "/../vistas/inicio/localizacionqr.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }

    // Guardar el reporte de localización del ciudadano
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destino = !empty($_SESSION['id_usuario']) ? '/RUPE/index.php?c=perfilusuario&a=Inicio' : '/RUPE/index.php';

            $id_reporte_padre  = filter_input(INPUT_POST, 'id_reporte_padre', FILTER_VALIDATE_INT);
            $reporteOriginal   = $id_reporte_padre ? $this->modelo->obtenerReportePorFolio($id_reporte_padre) : null;

            if (!$reporteOriginal) {
                echo "<script>alert('El folio de reporte no es válido o ya fue cerrado.'); window.location.href='/RUPE/index.php';</script>";
                exit();
            }

            $nombre_invitado   = mb_convert_case(trim($_POST['nombre_invitado'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $telefono_invitado = preg_replace('/[^0-9]/', '', $_POST['telefono_invitado'] ?? '');
            $calle             = mb_convert_case(trim($_POST['calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $en_resguardo      = isset($_POST['en_resguardo']) && $_POST['en_resguardo'] === '1' ? 1 : 0;
            $comentarios       = trim($_POST['comentarios'] ?? '');

            if (empty($nombre_invitado) || strlen($telefono_invitado) < 10 || empty($calle)) {
                echo "<script>alert('Por favor completa tu nombre completo, teléfono a 10 dígitos y la calle donde viste a la mascota.'); window.history.back();</script>";
                exit();
            }

            // Subir fotografía de evidencia (opcional)
            $rutaFoto = null;
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = "avistamiento_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['foto']['name']);
                $directorio = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/reportes/";
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $directorio . $nombreFoto)) {
                    $rutaFoto = "/RUPE/uploads/reportes/" . $nombreFoto;
                }
            }

            $guardado = $this->modelo->registrarLocalizacionInvitado(
                $id_reporte_padre,
                $reporteOriginal['id_mascota'],
                $reporteOriginal['id_colonia'],
                $nombre_invitado,
                $telefono_invitado,
                $calle,
                $en_resguardo,
                $rutaFoto,
                $comentarios
            );

            if ($guardado) {
                echo "<script>alert('¡Muchas gracias! Tu reporte de localización ha sido registrado y notificado al dueño de la mascota.'); window.location.href='/RUPE/index.php';</script>";
            } else {
                echo "<script>alert('Ocurrió un error al guardar el reporte. Intenta nuevamente.'); window.history.back();</script>";
            }
            exit();
        }
        
    }
    // Vista pública de todas las mascotas localizadas
    public function muro()
    {
        $localizadas = $this->modelo->obtenerMascotasLocalizadas();

        if (!empty($_SESSION['id_usuario'])) {
            require_once __DIR__ . "/../vistas/inicio/encabezadoingreso.php";
        } else {
            require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        }

        require_once __DIR__ . "/../vistas/inicio/mascotaslocalizadas.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }

    // Endpoint AJAX para validar folio y desbloquear teléfono/dirección
    public function verificarFolio()
    {
        header('Content-Type: application/json; charset=utf-8');
        $id_avistamiento = filter_input(INPUT_POST, 'id_avistamiento', FILTER_VALIDATE_INT);
        $folio           = trim($_POST['folio'] ?? '');

        $datos = ($id_avistamiento && $folio !== '')
            ? $this->modelo->validarFolioParaContacto($id_avistamiento, $folio)
            : null;

        if ($datos) {
            echo json_encode(['ok' => true, 'datos' => $datos], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'El folio ingresado no corresponde a un reporte de extravío activo en RUPE.'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
        // Endpoint AJAX para que el dueño confirme que ya recuperó a su mascota desde el muro
    public function confirmarRecuperacion()
    {
        header('Content-Type: application/json; charset=utf-8');
        $id_avistamiento = filter_input(INPUT_POST, 'id_avistamiento', FILTER_VALIDATE_INT);
        $folio           = trim($_POST['folio'] ?? '');

        if ($id_avistamiento && $folio !== '' && $this->modelo->confirmarRecuperacionPorFolio($id_avistamiento, $folio)) {
            echo json_encode(['ok' => true, 'mensaje' => '¡Felicidades! Se ha registrado que recuperaste a tu mascota y el reporte ha sido cerrado.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'No se pudo confirmar la recuperación. Verifica tu folio e intenta de nuevo.'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

}

// Permite ejecutar guardar() si el formulario apunta directo a controladores/localizacion.php
if (basename($_SERVER['SCRIPT_FILENAME']) === 'localizacion.php') {
    $ctrl = new LocalizacionControlador();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ctrl->guardar();
    } else {
        $ctrl->Inicio();
    }
}
