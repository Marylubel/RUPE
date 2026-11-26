<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../modelos/personal.php";

class PersonalusuarioControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Personal();
    }

    public function Inicio()
    {
        //Evita que con el botón "Atrás" muestre datos tras cerrar sesión)
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");


        if(!isset($_SESSION['id_usuario'])){
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        //Para evitar regresar a la sesión
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $rol = 'usuario';
        // Carga dinámica de estados directo desde la BD para la vista
        $listaEstados = $this->modelo->obtenerEstados() ?? [];
        //Se recuperan los datos del perfil del usuario para mostrarlos en la vista.        
        $perfil = $this->modelo->obtenerDatosPerfil($_SESSION['id_usuario']);

        require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        require_once __DIR__ . "/../../vistas/usuarios/personal.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }
    // Gestiona las respuestas JSON 
    public function procesarPeticionesAjax()
    {
        //Respuestas JSON 
        header('Content-Type: application/json; charset=utf-8');
        $action = $_GET['action'] ?? '';

        //Obtener municipios, códigos postales y colonias según la selección del usuario
        if ($action === 'getMunicipios') {
            $estado_id = filter_input(INPUT_GET, 'estado_id', FILTER_VALIDATE_INT);
            echo json_encode($this->modelo->obtenerMunicipiosPorEstado($estado_id), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'getColoniasPorCP') {
            $cp = preg_replace('/[^0-9]/', '', $_GET['cp'] ?? ''); // Elimina cualquier carácter que no sea dígito            
            echo json_encode($this->modelo->obtenerColoniasPorCP($cp), JSON_UNESCAPED_UNICODE);
        } elseif ($action === 'getColoniasPorAlcaldia') {
            $nombre_alcaldia = $_GET['alcaldia_nombre'] ?? $_GET['nombre_alcaldia'] ?? '';
            echo json_encode($this->modelo->obtenerColoniasPorAlcaldias($nombre_alcaldia), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'] ?? null;
            if (!$id_usuario){
                header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
                exit();
            }
            //se agrega mb_convert_case para convertir lo ingresado en Mayuscula inicial
            //y minuscula en el resto del dato. Con la finalidad de que se vea unificado el ingreso de datos
            $nombre = mb_convert_case(trim($_POST['nombre'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $ap_pat = mb_convert_case(trim($_POST['appaterno'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $ap_mat = mb_convert_case(trim($_POST['apmaterno'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            //Para curp, numero interior y exterior siempre en mayuscula
            $curp = mb_strtoupper(trim($_POST['curp'] ?? ''), 'UTF-8');
            $telefono = $_POST['telefono'] ?? '';
            $ocupacion = $_POST['ocupacion'] ?? '';
            $genero = $_POST['sexo'] ?? '';

            //validar que la edad sea positiva
            $edad = filter_input(INPUT_POST, 'edad', FILTER_VALIDATE_INT, 
            ['options' => ['min_range' =>1, 'max_range' => 120]]) ?: null;

            $calle = mb_convert_case(trim($_POST['calle'] ?? ''), MB_CASE_TITLE, 'UTF-8');
            $numint = mb_strtoupper(trim($_POST['numint'] ?? ''), 'UTF-8');
            $numext = mb_strtoupper(trim($_POST['numext'] ?? ''), 'UTF-8');

            // Recibe los datos de colonia y codigo postal
            $colonia_nombre = $_POST['colonia_nombre'] ?? '';
            $codigo_postal = trim($_POST['codigo_postal'] ?? $_POST['codigo_id'] ?? '');
            
            //Verificar que el usuario tiene perfil            
            $perfilActual = $this->modelo->obtenerDatosPerfil($id_usuario);
            $rutaWebFoto = $perfilActual['foto'] ?? $_SESSION['usuario_foto'] ?? "/RUPE/assets/fotos/avatar-de-perfil.png";
            
            // Subida de imagen
            //$rutaWebFoto = $_SESSION['usuario_foto'] ?? "/RUPE/assets/fotos/avatar-de-perfil.png";
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $nombreFoto = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['foto']['name']);
                $rutaTemporal = $_FILES['foto']['tmp_name'];
                //establece donde se guarda la imagen
                $directorioUploads = $_SERVER['DOCUMENT_ROOT'] . "/RUPE/uploads/";
                if(!is_dir($directorioUploads)){
                    mkdir($directorioUploads, 0777, true);
                }
                //Para guardar la foto en la carpeta uploads y obtener la ruta web para la base de datos                
                $carpetaDestino = $directorioUploads . $nombreFoto;

                //Se establece la carpeta destino para la foto de perfil del usuario                
                if (move_uploaded_file($rutaTemporal, $carpetaDestino)) {
                    $rutaWebFoto = "/RUPE/uploads/" . $nombreFoto;                    
                }else {
                    die('Error al subir la foto.');
                }                
            }
            $resultado = $this->modelo->guardarModificarDatos(
                $id_usuario,
                $nombre,
                $ap_pat,
                $ap_mat,
                $curp,
                $telefono,
                $calle,
                $numint,
                $numext,
                $edad,
                $genero,
                $ocupacion,
                $rutaWebFoto,
                $colonia_nombre,
                $codigo_postal
            );

            if ($resultado) {
                $_SESSION['usuario_nombre'] = trim($nombre . " " . $ap_pat . " " . $ap_mat);
                $_SESSION['usuario_foto'] = $rutaWebFoto;
                echo "<script>alert('Datos del usuario guardados correctamente'); 
                window.location.href='/RUPE/index.php?c=perfilusuario&a=Inicio';</script>";
            } else {
                echo "Error al guardar en la base de datos.";
            }
        }
    }
    public function eliminar()
    {
        if (!isset($_SESSION['id_usuario'])){
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }
        $id_usuario = $_SESSION['id_usuario'];
        if ($this->modelo->borradoLogicoPerfil($id_usuario)){
            //se hace un borrado de datos mostrados en el formulario
            unset($_SESSION['usuario_nombre'], $_SESSION['usuario_foto']);

            echo "<script> 
            alert('Tus datos han sido borrados. Puedes ingresar nuevamente tus datos cuando lo desees.'); 
            window.location.href= '/RUPE/index.php?c=perfilusuario&a=Inicio';
            </script>";
            exit();
        } else {
            echo "<script> alert('No se pudieron eliminar tus datos.'); 
            window.history.back();
            </script>";
            exit();
        }
    }
}

$controlador = new PersonalusuarioControlador();

if (isset($_GET['action'])) {
    $controlador->procesarPeticionesAjax();
}elseif (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
    $controlador->eliminar();
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->editar();
} else {
    $controlador->Inicio();
}
