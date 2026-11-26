<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../modelos/usuarios/mascotausu.php";

class MascotausuControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Mascotausu();
    }

    public function Inicio()
    {
        //Evita que con el botón "Atrás" muestre datos tras cerrar sesión)
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $listaEspecies = $this->modelo->obtenerEspecies();
        $listaSexos    = $this->modelo->obtenerSexos();

        //Identificando a la mascota por id para su modificación
        $mascota = null;
        if (!empty($_GET['id'])) {
            $id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $mascota = $this->modelo->obtenerMascotaPorId($id_mascota, $_SESSION['id_usuario']);
        }

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/mascota.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }

    public function listar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $misMascotas = $this->modelo->obtenerMascotasPorUsuario($_SESSION['id_usuario']);

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/mismascotas.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }

    public function eliminar()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id_mascota && $this->modelo->eliminarMascota($id_mascota, $_SESSION['id_usuario'])) {
            echo "<script>alert('Mascota eliminada correctamente.'); window.location.href = '/RUPE/controladores/usuarios/mascotausu.php?accion=listar';</script>";
        } else {
            echo "<script>alert('No se pudo eliminar la mascota.'); window.history.back();</script>";
        }
        exit();
    }

    public function procesarAjax()
    {
        header('Content-Type: application/json; charset=utf-8');
        $action = $_GET['action'] ?? '';

        if ($action === 'getRazas') {
            $id_especie = filter_input(INPUT_GET, 'id_especie', FILTER_VALIDATE_INT);
            echo json_encode($this->modelo->obtenerRazasPorEspecie($id_especie), JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    public function guardar()
    {
        //En caso de que expire la sesión
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'] ?? null;

            if (!$id_usuario) {
                header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
                exit();
            }

            $id_mascota      = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);
            $nombre          = mb_convert_case(trim($_POST['nombre'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $id_raza         = filter_input(INPUT_POST, 'id_raza', FILTER_VALIDATE_INT);
            $id_sexo         = filter_input(INPUT_POST, 'id_sexos', FILTER_VALIDATE_INT);
            $edad            = trim($_POST['edad'] ?? '');
            $color           = $_POST['color'] ?? '';
            $tamano          = $_POST['tamano'] ?? '';
            $peso_aprox      = !empty($_POST['peso_aprox']) ? (float)$_POST['peso_aprox'] : null;
            $sena_particular = trim($_POST['sena_particular'] ?? '');

            //direccion de la foto
            $rutaWebFoto = "/RUPE/assets/fotos/Mascota.jpg";

            //Para conservar la foto si no sube una nueva
            if ($id_mascota) {
                $mascotaVigente = $this->modelo->obtenerMascotaPorId($id_mascota, $id_usuario);
                if ($mascotaVigente && !empty($mascotaVigente['foto'])) {
                    $rutaWebFoto = $mascotaVigente['foto'];
                }
            }

            // Subida de fotografía de la mascota
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = "mascota_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['foto']['name']);
                $directorioUploads = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/mascotas/";

                if (!is_dir($directorioUploads)) {
                    mkdir($directorioUploads, 0777, true);
                }

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $directorioUploads . $nombreFoto)) {
                    $rutaWebFoto = "/RUPE/uploads/mascotas/" . $nombreFoto;
                }
            }
            //para modificar datos de la mascota
            if ($id_mascota) {
                $resultado = $this->modelo->modificarMascota(
                    $id_mascota,
                    $id_usuario,
                    $id_raza,
                    $id_sexo,
                    $nombre,
                    $edad,
                    $color,
                    $rutaWebFoto,
                    $sena_particular,
                    $tamano,
                    $peso_aprox
                );
                $mensaje = "¡Datos actualizados correctamente!";
            } else {
                $resultado = $this->modelo->registrarMascota(
                    $id_usuario,
                    $id_raza,
                    $id_sexo,
                    $nombre,
                    $edad,
                    $color,
                    $rutaWebFoto,
                    $sena_particular,
                    $tamano,
                    $peso_aprox
                );
                $mensaje = "¡Mascota registrada correctamente en RUPE!";
            }
            //se imprime el mensaje dinamicamente
            if ($resultado) {
                echo "<script>
                        alert('$mensaje');
                        window.location.href = '/RUPE/controladores/usuarios/mascotausu.php?accion=listar';
                      </script>";
            } else {
                echo "Error al procesar datos de la mascota.";
            }
        }
    }
}



$controlador = new MascotausuControlador();

if (isset($_GET['action'])) {
    $controlador->procesarAjax();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'listar') {
    $controlador->listar();
} elseif (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $controlador->eliminar();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->guardar();
} else {
    $controlador->Inicio();
}
