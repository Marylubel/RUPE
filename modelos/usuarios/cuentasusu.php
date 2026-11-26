<?php

require_once __DIR__ . "/../BasedeDatos.php";

class Cuentausu
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    // 1. Verificar si la contraseña actual ingresada coincide con la de la BD
    public function verificarContrasenaActual($id_usuario, $contrasena_actual)
    {
        try {
            // Ajusta 'password' si tu columna en la tabla usuarios se llama 'contrasena'
            $sql = "SELECT contrasena FROM usuarios WHERE id_usuario = :id_usuario LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            $hash = $stmt->fetchColumn();

            if (!$hash) {
                return false;
            }

            // Soporta tanto contraseñas encriptadas con password_hash como texto plano (por si hay usuarios de prueba)
            return password_verify($contrasena_actual, $hash) || $contrasena_actual === $hash;
        } catch (PDOException $e) {
            return false;
        }
    }

    // 2. Actualizar la contraseña encriptada
    public function actualizarContrasena($id_usuario, $contrasena_nueva)
    {
        try {
            $hashNuevo = password_hash($contrasena_nueva, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':contrasena'   => $hashNuevo,
                ':id_usuario' => $id_usuario
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 3. Borrado lógico de la cuenta del usuario (cambia estatus = 0)
    public function eliminarCuentaLogica($id_usuario)
    {
        try {
            $sql = "UPDATE usuarios SET estatus = 0 WHERE id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id_usuario' => $id_usuario]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
