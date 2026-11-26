<?php

class CerrarsesionControlador
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function Inicio()
    {
        //Se borran las variables de sesión (id_usuario, correo, rol, nombres y fotos)
        $_SESSION = array();

        //Borrar la cookie de sesión del navegador activa
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        //Eliminar la sesión en el servidor
        session_destroy();

        //Enviar a la pantalla principal
        header("Location: /RUPE/index.php");
        exit();
    }
}

// Para que funcione cerrar llamandolo por index o directamente
if (basename($_SERVER['PHP_SELF']) === 'cerrarsesion.php') {
    $controlador = new CerrarsesionControlador();
    $controlador->Inicio();
}
