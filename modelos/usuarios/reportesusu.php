<?php

require_once __DIR__ . "/../BasedeDatos.php";

class Reporteusu
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    // 1. Obtener mascotas activas del usuario con todos sus detalles
    public function obtenerMascotasUsuario($id_usuario)
    {
        try {
            $sql = "SELECT m.*, s.sexos, r.nom_raza, e.nom_especie
                    FROM mascotas m
                    LEFT JOIN sexos_mascota s ON m.id_sexos = s.id_sexo_masc
                    LEFT JOIN catalogos c ON m.id_catalogos = c.id_catalogo
                    LEFT JOIN razas r ON c.id_razas = r.id_raza
                    LEFT JOIN especies e ON r.id_especie = e.id_especie
                    WHERE m.id_usuario = :id_usuario AND m.estatus = 1
                    ORDER BY m.nombre ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 2. Registrar reporte de extravío y devolver el id_reporte generado (Folio único)
    public function registrarReporteExtravio($id_usuario, $id_mascota, $id_colonia, $calle, $entre_calle, $y_calle, $foto, $fecha, $hora, $comentarios)
    {
        try {
            $sql = "INSERT INTO reportes (
                        tipo_reporte, id_usuario, id_mascota, id_colonia, 
                        calle, entre_calle, y_calle, foto, fecha, hora, 
                        comentarios, estado_reporte, codigo_qr
                    ) VALUES (
                        'extraviado', :id_usuario, :id_mascota, :id_colonia, 
                        :calle, :entre_calle, :y_calle, :foto, :fecha, :hora, 
                        :comentarios, 'activo', ''
                    )";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_usuario'  => $id_usuario,
                ':id_mascota'  => $id_mascota,
                ':id_colonia'  => !empty($id_colonia) ? $id_colonia : null,
                ':calle'       => $calle,
                ':entre_calle' => $entre_calle,
                ':y_calle'     => $y_calle,
                ':foto'        => $foto,
                ':fecha'       => $fecha,
                ':hora'        => $hora,
                ':comentarios' => $comentarios
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Error al registrar reporte de extravío: " . $e->getMessage());
        }
    }

    // 3. Guardar la URL del Código QR generada para ese folio
    public function actualizarCodigoQR($id_reporte, $ruta_qr)
    {
        try {
            $sql = "UPDATE reportes SET codigo_qr = :codigo_qr WHERE id_reporte = :id_reporte";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':codigo_qr' => $ruta_qr, ':id_reporte' => $id_reporte]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 4. Obtener los datos completos del reporte usando los nombres reales de tus tablas
    public function obtenerDetalleReporte($id_reporte)
    {
        try {
            $sql = "SELECT r.*, 
                           m.nombre AS nombre_mascota, m.edad, m.color, m.tamano, m.peso_aprox, m.sena_particular, m.foto AS foto_mascota,
                           rz.nom_raza, esp.nom_especie, sm.sexos,
                           col.nombre_col AS nombre_colonia, 
                           cp.codigo_postal,
                           alc.id_alcaldia, 
                           alc.alcaldia_mpio AS nombre_municipio,
                           est.id_estado, 
                           est.nombre_edo AS nombre_estado                           
                    FROM reportes r
                    LEFT JOIN mascotas m ON r.id_mascota = m.id_mascota
                    LEFT JOIN catalogos cat ON m.id_catalogos = cat.id_catalogo
                    LEFT JOIN razas rz ON cat.id_razas = rz.id_raza
                    LEFT JOIN especies esp ON rz.id_especie = esp.id_especie
                    LEFT JOIN sexos_mascota sm ON m.id_sexos = sm.id_sexo_masc
                    LEFT JOIN colonias col ON r.id_colonia = col.id_colonia
                    LEFT JOIN codigos_postales cp ON col.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias alc ON cp.id_alcaldias = alc.id_alcaldia
                    LEFT JOIN estados est ON alc.id_estados = est.id_estado                   
                    WHERE r.id_reporte = :id_reporte AND r.estatus = 1
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_reporte' => $id_reporte]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener detalle del reporte: " . $e->getMessage());
        }
    }
        // 5. Listar todos los reportes activos del usuario (para la galería Ver/Seguimiento)
    public function obtenerReportesUsuario($id_usuario)
    {
        try {
            $sql = "SELECT r.*, 
                           m.nombre AS nombre_mascota, m.foto AS foto_mascota,
                           rz.nom_raza, esp.nom_especie,
                           col.nombre_col AS nombre_colonia, alc.alcaldia_mpio AS nombre_municipio
                    FROM reportes r
                    LEFT JOIN mascotas m ON r.id_mascota = m.id_mascota
                    LEFT JOIN catalogos cat ON m.id_catalogos = cat.id_catalogo
                    LEFT JOIN razas rz ON cat.id_razas = rz.id_raza
                    LEFT JOIN especies esp ON rz.id_especie = esp.id_especie
                    LEFT JOIN colonias col ON r.id_colonia = col.id_colonia
                    LEFT JOIN codigos_postales cp ON col.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias alc ON cp.id_alcaldias = alc.id_alcaldia
                    WHERE r.id_usuario = :id_usuario AND r.tipo_reporte = 'extraviado' AND r.estatus = 1
                    ORDER BY r.id_reporte DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 6. Modificar los datos de un reporte existente (fecha, hora, lugar, comentarios y foto)
    public function modificarReporteExtravio($id_reporte, $id_usuario, $id_mascota, $id_colonia, $calle, $entre_calle, $y_calle, $foto, $fecha, $hora, $comentarios)
    {
        try {
            $sql = "UPDATE reportes SET 
                        id_mascota  = :id_mascota,
                        id_colonia  = :id_colonia,
                        calle       = :calle,
                        entre_calle = :entre_calle,
                        y_calle     = :y_calle,
                        foto        = :foto,
                        fecha       = :fecha,
                        hora        = :hora,
                        comentarios = :comentarios
                    WHERE id_reporte = :id_reporte AND id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_mascota'  => $id_mascota,
                ':id_colonia'  => !empty($id_colonia) ? $id_colonia : null,
                ':calle'       => $calle,
                ':entre_calle' => $entre_calle,
                ':y_calle'     => $y_calle,
                ':foto'        => $foto,
                ':fecha'       => $fecha,
                ':hora'        => $hora,
                ':comentarios' => $comentarios,
                ':id_reporte'  => $id_reporte,
                ':id_usuario'  => $id_usuario
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 7. Cambiar estatus entre 'activo' (extraviado) y 'recuperado' (localizado)
    public function cambiarEstadoReporte($id_reporte, $id_usuario, $nuevo_estado)
    {
        try {
            $sql = "UPDATE reportes SET estado_reporte = :estado 
                    WHERE id_reporte = :id_reporte AND id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':estado'     => $nuevo_estado,
                ':id_reporte' => $id_reporte,
                ':id_usuario' => $id_usuario
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // 8. Eliminar reporte (y sus fichas de avistamiento hijas si las hubiera)
    public function eliminarReporte($id_reporte, $id_usuario)
    {
        try {
            $sql = "DELETE FROM reportes WHERE id_reporte = :id_reporte AND id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id_reporte' => $id_reporte, ':id_usuario' => $id_usuario]);
        } catch (PDOException $e) {
            return false;
        }
    }
        // 9. Obtener todos los reportes de localización enviados por ciudadanos para un folio específico
    public function obtenerAvisosLocalizacion($id_reporte_padre)
    {
        try {
            $sql = "SELECT id_reporte, nombre_invitado, telefono_invitado, en_resguardo, 
                           calle, foto, fecha, hora, comentarios
                    FROM reportes
                    WHERE id_reporte_padre = :id_reporte_padre 
                      AND tipo_reporte = 'localizado' 
                      AND estatus = 1
                    ORDER BY id_reporte DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_reporte_padre' => $id_reporte_padre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


}
