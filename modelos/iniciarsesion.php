<?php

class IniciarSesion {
    private $pdo;
    //private $usuarios;

    public function __construct() { //metodo para ingresar a las tablas de la base de datos
        $this->pdo = BasedeDatos::conectar();
        //$this->usuarios = $this->pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function iniciarSesion($correo, $contrasena) {
        try {
            // Se prepara la consulta SQL para verificar el correo y la contraseña
            $sql = "SELECT id_usuario, id_roles, correo_electronico, contrasena FROM usuarios WHERE correo_electronico = :correo";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            //revisar que existe usuario y contraseña
            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                unset($usuario['contrasena']);
                return $usuario;          
            } else {
                // Credenciales incorrectas
                return false;
            }
        } catch (PDOException $e) {
            // Registrar logs
            error_log("Error al iniciar sesión:" . $e->getMessage());
            return false;
        }
    }
}





    //public function __construct() { //metodo para ingresar a las tablas de la base de datos
    //    $this->pdo = BasedeDatos::conectar();
    //    $this->usuarios = $this->pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    //}

    //public function iniciarSesion($correo, $contrasena) {
    //    try {
    //        // Se prepara la consulta SQL para verificar el correo y la contraseña
    //        $sql = "SELECT * FROM usuarios WHERE correo_electronico = :correo";
    //        $stmt = $this->pdo->prepare($sql);
    //        $stmt->bindParam(':correo', $correo);
    //        $stmt->execute();
    //        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    //        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
    //            // Inicio de sesión exitoso
    //            return true;
    //        } else {
    //            // Credenciales incorrectas
    //            return false;
    //        }
    //    } catch (PDOException $e) {
    //        // Manejar el error de la base de datos
    //        echo "Error al iniciar sesión: " . $e->getMessage();
    //        return false;
    //    }
  //  }
//}