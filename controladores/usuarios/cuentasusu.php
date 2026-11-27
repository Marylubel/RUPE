<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../modelos/usuarios/cuentasusu.php";

class CuentausuControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Cuentausu();
    }

    private function Admin()
    {
        return !empty($_SESSION['id_roles']) && ($_SESSION['id_roles'] == 1 || $_SESSION['id_roles'] === 'administrador');
    }

    // Mostrar formulario de cambio de contraseña
    public function Inicio()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        //Encabezado dinámico de acuerdo al rol
        if ($this->Admin()) {
            require_once __DIR__ . "/../../vistas/inicio/encabezadoadmon.php";
        } else {
            require_once __DIR__ . "/../../vistas/inicio/encabezadoingreso.php";
        }
        require_once __DIR__ . "/../../vistas/usuarios/cambiarcontrasena.php";
        require_once __DIR__ . "/../../vistas/inicio/pie.php";
    }

    // Procesar cambio de contraseña
    public function guardarContrasena()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $id_usuario       = $_SESSION['id_usuario'];
        $contrasena_actual  = $_POST['contrasena_actual'] ?? '';
        $contrasena_nueva   = $_POST['contrasena_nueva'] ?? '';
        $contrasena_confirm = $_POST['contrasena_confirm'] ?? '';

        if (strlen($contrasena_nueva) < 6) {
            echo "<script>alert('La nueva contraseña debe tener al menos 6 caracteres.'); 
            window.history.back();</script>";
            exit();
        }

        if ($contrasena_nueva !== $contrasena_confirm) {
            echo "<script>alert('La nueva contraseña y su confirmación no coinciden.'); 
            window.history.back();</script>";
            exit();
        }

        if (!$this->modelo->verificarContrasenaActual($id_usuario, $contrasena_actual)) {
            echo "<script>alert('La contraseña actual es incorrecta.'); window.history.back();</script>";
            exit();
        }

        if ($this->modelo->actualizarContrasena($id_usuario, $contrasena_nueva)) {
            $destino = $this->Admin()
                ? '/RUPE/index.php?c=perfiladministrador&a=Inicio' 
                : '/RUPE/index.php?c=perfilusuario&a=Inicio';
            echo "<script>alert('¡Contraseña actualizada correctamente!'); 
            window.location.href='/RUPE/index.php?c=perfilusuario&a=Inicio';</script>";

        } else {
            echo "<script>alert('Ocurrió un error al actualizar la contraseña.'); 
            window.history.back();</script>";
        }
        exit();
    }

    // Borrado lógico de cuenta y cierre automático de sesión
    public function eliminarCuenta()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /RUPE/index.php?c=iniciarsesion&a=Inicio");
            exit();
        }

        $id_usuario = $_SESSION['id_usuario'];

        if ($this->modelo->eliminarCuentaLogica($id_usuario)) {
            // Destruir sesión completamente
            $_SESSION = [];
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
            session_destroy();

            echo "<script>alert('Tu cuenta ha sido dada de baja correctamente.'); window.location.href='/RUPE/index.php';</script>";
        } else {
            echo "<script>alert('No se pudo dar de baja la cuenta. Intenta más tarde.'); window.history.back();</script>";
        }
        exit();
    }
}
if (basename($_SERVER['SCRIPT_FILENAME']) === 'cuentasusu.php') {
    $controlador = new CuentausuControlador();

    if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {
        $controlador->eliminarCuenta();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controlador->guardarContrasena();
    } else {
        $controlador->Inicio();
    }
}
