<?php


require_once __DIR__ . "/../modelos/perfil.php";


class PerfiladministradorControlador {
    private $modelo;
    
    public function __construct() { 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modelo = new Perfil();        
    }

    public function Inicio() {
        //Valida sesión y rol
        if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] !== 'administrador')) {
            header("Location: index.php?c=IniciarSesion&a=Inicio");
            exit();
        }
        //Para evitar regresar a la sesión
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        //valida al usuario para obtener los datos del perfil
        $correo = $_SESSION['correo'];
        $perfil = $this->modelo->obtenerPerfil($correo);    
        
        if ($perfil) {
            require_once __DIR__ . "/../vistas/inicio/encabezadoadmon.php";
            require_once __DIR__ . "/../vistas/perfil/administrador.php"; // Vista exclusiva de Admin
            require_once __DIR__ . "/../vistas/inicio/pie.php";
        } else {
           
            echo "Error al obtener el perfil del usuario.";
        }
    }
}