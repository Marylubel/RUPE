<?php

class Perfil {
    private $pdo;

    public function __construct() { 
        $this->pdo = BasedeDatos::conectar();
    }


    public function obtenerPerfil($correo) {
        try {
            $sql = "SELECT * FROM usuarios WHERE correo_electronico = :correo";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            
            //FETCH_OBJ para que funcione de forma nativa con los controladores anteriores
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}