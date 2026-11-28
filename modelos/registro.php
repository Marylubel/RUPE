<?php

require_once __DIR__ . "/BasedeDatos.php";

class Registro
{
    private $pdo;

    public function __construct()
    { //metodo para ingresar a las tablas de la base de datos
        $this->pdo = BasedeDatos::conectar();
    }

    public function registrarUsuario($id_rol, $correo, $contrasenaencriptada)
    {
        try {
            // Obtener la fecha y hora actual en formato MySQL
            $fechaActual = date("Y-m-d H:i:s");

            //Verifica que el correo exista en la tabla
            $sqlrevisar = "SELECT id_usuario, estatus FROM usuarios WHERE correo_electronico = :correo LIMIT 1";
            $stmtrevisar = $this->pdo->prepare($sqlrevisar);
            $stmtrevisar->execute([':correo' => $correo]);
            $usuarioExiste = $stmtrevisar->fetch(PDO::FETCH_ASSOC);

            if ($usuarioExiste) {
                //Esta activo no permite el registro del correo
                if ((int)$usuarioExiste['estatus'] === 1) {
                    echo "<script>alert('Este correo electronico ya se encuentra registrado');
                    window.history.back();</script>";
                    return false;
                }
                //Si elimino la cuenta y va a reactivar
                $sqlReactivar = "UPDATE usuarios SET contrasena = :contrasena, id_roles = :id_roles,
                creado = :creado, estatus = 1 WHERE id_usuario = :id";
                $stmtReactivar = $this->pdo->prepare($sqlReactivar);
                return $stmtReactivar->execute([
                    ':contrasena' => $contrasenaencriptada,
                    ':id_roles' => $id_rol,
                    ':creado' => $fechaActual,
                    ':id' => $usuarioExiste['id_usuario']
                ]);
            }

            //Insertar datos tabla usuarios
            $sqlUsuario = "INSERT INTO usuarios (correo_electronico, contrasena, id_roles, creado, estatus) 
            VALUES (:correo, :contrasena, :id_roles, :creado, 1)";
            $stmtUsuario = $this->pdo->prepare($sqlUsuario);
            
            return $stmtUsuario->execute([
                ':correo'     => $correo,
                ':contrasena' => $contrasenaencriptada,
                ':id_roles'      => $id_rol,
                ':creado'     => $fechaActual
            ]);
            
        } catch (PDOException $e) {
            //Registro de errores en un archivo de log
            error_log("Error al registrar el usuario: " . $e->getMessage());

            echo "Error al registrar el usuario: " . $e->getMessage();
            return false;
        }
    }
}
