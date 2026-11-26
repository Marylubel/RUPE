<?php

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
            
            //Insertar datos tabla usuarios
            $sqlUsuario = "INSERT INTO usuarios (correo_electronico, contrasena, id_roles, creado) VALUES (:correo, :contrasena, :roles, :creado)";
            $stmtUsuario = $this->pdo->prepare($sqlUsuario);
            
            // Obtener la fecha y hora actual en formato MySQL
            $fechaActual = date("Y-m-d H:i:s"); 

            $stmtUsuario->bindParam(':correo', $correo);
            $stmtUsuario->bindParam(':contrasena', $contrasenaencriptada);
            $stmtUsuario->bindParam(':roles', $id_rol);
            $stmtUsuario->bindParam(':creado', $fechaActual);
            return $stmtUsuario->execute();

            //Confirma cambios en base de datos si todo esta correcto
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            //Registro de errores en un archivo de log
            error_log("Error al registrar el usuario: " . $e->getMessage());
            
            echo "Error al registrar el usuario: " . $e->getMessage();
            return false;
        }
    }
}
