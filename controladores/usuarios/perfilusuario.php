<?php

require_once __DIR__ . "/../../modelos/perfil.php";

class PerfilusuarioControlador {
    private $modelo;
    
    public function __construct() { 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modelo = new Perfil();
    }

    public function Inicio() {
        //Validar sesión y rol
        if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_roles'] != 2 && $_SESSION['id_roles'] !== 'usuario')) {
            header("Location: index.php?c=IniciarSesion&a=Inicio");
            exit();
        }
        //Para evitar regresar a la sesión
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $correo = $_SESSION['correo'];
        $perfil = $this->modelo->obtenerPerfil($correo);

        if ($perfil) { //Se agrega _DIR_ para asegurar se encuentre la dirección
            require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php"; 
            require_once __DIR__ . "/../../vistas/perfil/usuario.php"; // Vista exclusiva de Usuario
            require_once __DIR__ . "/../../vistas/inicio/pie.php";
        } else {
            echo "Error al obtener el perfil del usuario.";
        }
    }
}
