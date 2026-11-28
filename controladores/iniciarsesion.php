<?php

require_once "modelos/iniciarsesion.php";

class IniciarSesionControlador
{
    private $modelo;

    public function __construct()
    {
        //metodo que se ejecuta al instanciar la clase
        $this->modelo = new IniciarSesion();
    }

    public function Inicio()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        //el usuario no tiene la sesión activa, pero SÍ tiene la cookie de "recordarme"
    if (!isset($_SESSION['id_usuario']) && isset($_COOKIE['rupe_recordar']) && isset($_COOKIE['rupe_usuario']))
        {
        // Busca al usuario en la base de datos usando la cookie de correo
        require_once __DIR__ . "/../modelos/registro.php";        
    }
        require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        require_once __DIR__ . "/../vistas/formularios/iniciarsesion.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }

    public function Validar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //limpiar y obtener los valores de las variables de entrada
            $correo = isset($_POST['correo']) ? trim($_POST['correo']) : null;
            $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : null;
            
            

            //validar credenciales
            $usuario = $this->modelo->iniciarSesion($correo, $contrasena);

            if($usuario && !empty($usuario['cuenta_inactiva'])){
                $this->recargarLogin("Esta cuenta no esta activa. Deber registrarse");
                return;
            }

            if ($usuario) {
                //Para inicio de sesión correcto
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['correo'] = $usuario['correo_electronico'];
                $_SESSION['id_roles'] = $usuario['id_roles'];
                //para limpiar la sesión antes de iniciar nueva
                unset($_SESSION['usuario_nombre'], $_SESSION['usuario_foto'], $_SESSION['admin_nombre'], 
                $_SESSION['admin_foto']);
                
                //recordar al usuario que ingreso 
                //if(isset($_POST['recordarme'])){
                    //se define el tiempo que dura la cookie
                //    $tiempo = time() + (60 * 60 * 24 * 30);

                    //guardar correo en cookie
                //    setcookie("rupe_usuario", $usuario['correo_electronico'], $tiempo, "/", "", false, true);
                //}else{
                    //borrar cookies en caso de no marcar la casilla
                //    setcookie("rupe_usuario", "", time() - 3600, "/");
                //    setcookie("rupe_recordar", "", time() - 3600, "/");
                //}

                if ($usuario['id_roles'] == 1 || $usuario['id_roles'] === 'administrador') {                    
                    require_once __DIR__ . "/../modelos/personal.php";
                    $modeloPersonal = new Personal();
                    $administrador = $modeloPersonal->obtenerDatosPerfil($usuario['id_usuario']);

                    if($administrador){
                        //Verificar que esten sus datos registrados
                        $_SESSION['admin_nombre'] = trim($administrador['nombre_usuario'] . '' . $administrador['ap_paterno'] . '' . $administrador['ap_materno']);
                        $_SESSION['admin_foto'] = !empty($administrador['foto']) ? $administrador['foto'] : '/RUPE/assets/fotos/avatar-de-perfil.png';

                        header("Location: /RUPE/index.php?c=perfiladministrador&a=Inicio");
                        exit();
                    } else{
                        //Si es su primer ingreso va directo al formulario
                        header("Location: /RUPE/controladores/administrador/personaladmin.php");
                        exit();
                    }
                    
                } elseif ($usuario['id_roles'] == 2 || $usuario['id_roles'] === 'usuario') {
                    header("Location: index.php?c=perfilusuario&a=Inicio");
                    exit();
                } else {
                    $this->recargarLogin("Error: El rol no es válido");
                    return;
                }
            } else {
                $this->recargarLogin("El correo o la contraseña son incorrectos");
            }
        }
    }   
    
    private function recargarLogin($error)
    {
        require_once __DIR__ . "/../vistas/inicio/encabezado.php";
        require_once __DIR__ . "/../vistas/formularios/iniciarsesion.php";
        require_once __DIR__ . "/../vistas/inicio/pie.php";
    }
}




