<?php

class BasedeDatos {
    private static $servidor = "127.0.0.1";
    private static $usuariobd = "root";
    private static $contrasena = "";
    private static $nombrebd = "rupe";
    private static $charset = "utf8mb4";

    public static function conectar() {
        try {
            $dsn = "mysql:host=" . self::$servidor . ";dbname=" . self::$nombrebd . ";charset=" . self::$charset;
            // Se colocan opciones de configuración para la conexión PDO
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            //Se instancia la conexión PDO con los parámetros definidos
            $conexion = new PDO($dsn, self::$usuariobd, self::$contrasena, $opciones);
            return $conexion;
            
        } catch (Exception $e) {
            // Se define la ruta de archivo dentro de una carpeta segura en modelos para guardar los logs de error
            $rutalog = __DIR__ . '/logs/error_log.txt';

            // Se pide crear una estructura con fecha, hora y tipo de error
            $mensajeError = "[" . date('Y-m-d H:i:s') . "] [CONEXION FALLIDA]" . $e->getMessage() . PHP_EOL;
            
            // Guardar el archivo 
            error_log($mensajeError, 3, $rutalog);
            
            die("Servicio no disponible: ");
      
    }
    }
}