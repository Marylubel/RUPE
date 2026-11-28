<?php

require_once __DIR__ . "/../modelos/registro.php";

class RegistroControlador
{
    private $modelo;

    public function __construct()
    { //metodo que se ejecuta al instanciar la clase
        //Inicia el modelo  
        $this->modelo = new Registro();
    }

    public function Inicio()
    {
        //Evita que con el botón "Atrás" muestre datos tras cerrar sesión)
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        require_once "vistas/inicio/encabezado.php";
        require_once "vistas/formularios/registrousu.php";
        require_once "vistas/inicio/pie.php";
    }

    public function RegistrarUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //limpiar y obtener los valores de las variables de entrada, se agrega mb_strtolower
            //para que se ingrese los datos del correo siempre en minusculas
            $correo = isset($_POST['correo']) ? mb_strtolower(trim($_POST['correo']), 'UTF-8') : null;
            $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;
            $confirmar_contrasena = isset($_POST['confirmar_contrasena']) ? $_POST['confirmar_contrasena'] : null;
            $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;
            $captcha = isset($_POST['g-recaptcha-response']) ? trim($_POST['g-recaptcha-response']) : ''; //Validar captcha

            //validar campos vacios            
            if ($correo === '' || $contrasena === '') {
                $this->recargarLogin("Todos los campos son obligatorios");
                return;
            }
            //validar que se haya marcado la casilla del captcha
            if ($captcha === '') {
                echo "<script>alert('Favor de marcar la casilla \"No soy un robot\".'); window.history.back();</script>";
                return;
            }
            //Verificar token local
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                $captchavalido = true;
            } else {
                $llave = "6Leaaf8tAAAAALGcpESc5NKhDE8-DhCiKK3hn5Mu";
                $url = "https://google.com{$llave}&response=" . urlencode($captcha);
                //para consultar en 2do plano
                $respuesta = @file_get_contents($url);
                $datosrespuesta = json_decode($respuesta, true);
                $captchavalido = !empty($datosrespuesta['success']);
            }                
            
            if (!$captchavalido) {
                echo "<script>alert('Verificación invalida, intente de nuevo'); window.history.back();</script>";
                return;
            }

            //Seguridad en el Servidor
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                echo "Error: Correo electrónico no válido.";
                return;
            }
            //Mínimo 8 caracteres, letras y números
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $contrasena)) {
                echo "Error: La contraseña debe ser alfanumérica con mínimo 8 caracteres.";
                return;
            }
            if ($contrasena !== $confirmar_contrasena) {
                echo "Error: Las contraseñas no coinciden.";
                return;
            }
            $id_roles = '2'; //rol usuario asignada

            //Validar rol administrador
            if ($rol === 'administrador') {
                $confirmacion_admin = isset($_POST['contrasena_admin']) ? trim($_POST['contrasena_admin']) : ''; //recibe contraseña de confirmación

                $CONTRASENA_ADMIN_REQUERIDA = "Responsable2026"; //contraseña para validar al administrador

                if ($confirmacion_admin !== $CONTRASENA_ADMIN_REQUERIDA) {
                    echo "<script>alert('Error: La contraseña de confirmación para el rol Administrador es incorrecta.'); window.history.back();</script>";
                    return;
                }
                $id_roles = '1'; //rol Administrador asignada
            }
            $contrasenaencriptada = password_hash($contrasena, PASSWORD_DEFAULT); //Encripta la contraseña antes de guardarla en la base de datos

            // Llamar al método del modelo para registrar el usuario
            $resultado = $this->modelo->registrarUsuario($id_roles, $correo, $contrasenaencriptada);

            if ($resultado) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['mensaje_exito'] = "¡Registro correcto! Iniciar Sesión";
                // Debe enviar a la pagina de inicio de sesión
                header("Location: index.php?c=iniciarsesion&a=Inicio");
                exit();
            } else {
                echo "Error al registrar el usuario.";
                return;
            }
        }
    }
}
