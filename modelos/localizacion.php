<?php

require_once __DIR__ . "/BasedeDatos.php";

class Localizacion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    // 1. Obtener los datos básicos de la mascota del reporte escaneado (sin exponer datos privados del dueño)
    public function obtenerReportePorFolio($id_reporte)
    {
        try {
            $sql = "SELECT r.id_reporte, r.id_mascota, r.id_colonia, r.estado_reporte, r.foto AS foto_reporte,
                           m.nombre AS nombre_mascota, m.color, m.tamano, m.sena_particular, m.foto AS foto_mascota,
                           rz.nom_raza, esp.nom_especie, sm.sexos,
                           col.nombre_col AS nombre_colonia, alc.alcaldia_mpio AS nombre_municipio
                    FROM reportes r
                    LEFT JOIN mascotas m ON r.id_mascota = m.id_mascota
                    LEFT JOIN catalogos cat ON m.id_catalogos = cat.id_catalogo
                    LEFT JOIN razas rz ON cat.id_razas = rz.id_raza
                    LEFT JOIN especies esp ON rz.id_especie = esp.id_especie
                    LEFT JOIN sexos_mascota sm ON m.id_sexos = sm.id_sexo_masc
                    LEFT JOIN colonias col ON r.id_colonia = col.id_colonia
                    LEFT JOIN codigos_postales cp ON col.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias alc ON cp.id_alcaldias = alc.id_alcaldia
                    WHERE r.id_reporte = :id_reporte AND r.tipo_reporte = 'extraviado' AND r.estatus = 1
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_reporte' => $id_reporte]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // 2. Registrar la ficha de localización enviada por el ciudadano invitado
    public function registrarLocalizacionInvitado($id_reporte_padre, $id_mascota, $id_colonia, $nombre_invitado, $telefono_invitado, $calle, $en_resguardo, $foto, $comentarios)
    {
        try {
            $sql = "INSERT INTO reportes (
                        tipo_reporte, id_reporte_padre, id_usuario, id_mascota, id_colonia,
                        nombre_invitado, telefono_invitado, en_resguardo,
                        calle, foto, fecha, hora, comentarios, estado_reporte, codigo_qr
                    ) VALUES (
                        'localizado', :id_reporte_padre, NULL, :id_mascota, :id_colonia,
                        :nombre_invitado, :telefono_invitado, :en_resguardo,
                        :calle, :foto, :fecha, :hora, :comentarios, 'activo', ''
                    )";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_reporte_padre'  => $id_reporte_padre,
                ':id_mascota'        => $id_mascota,
                ':id_colonia'        => $id_colonia,
                ':nombre_invitado'   => $nombre_invitado,
                ':telefono_invitado' => $telefono_invitado,
                ':en_resguardo'      => $en_resguardo,
                ':calle'             => $calle,
                ':foto'              => $foto,
                ':fecha'             => date('Y-m-d'),
                ':hora'              => date('H:i:s'),
                ':comentarios'       => $comentarios
            ]);
        } catch (PDOException $e) {
            die("Error al registrar localización: " . $e->getMessage());
        }
    }
    // 3. Listar todas las mascotas reportadas como localizadas en el muro público
    public function obtenerMascotasLocalizadas()
    {
        try {
            $sql = "SELECT r.id_reporte, r.id_reporte_padre, r.calle, r.en_resguardo, 
                           r.foto AS foto_avistamiento, r.fecha, r.hora, r.comentarios,
                           rp.estado_reporte AS estado_padre,
                           m.nombre AS nombre_mascota, m.color, m.tamano, m.sena_particular, m.foto AS foto_mascota,
                           rz.nom_raza, esp.nom_especie, sm.sexos,
                           col.nombre_col AS nombre_colonia, alc.alcaldia_mpio AS nombre_municipio
                    FROM reportes r
                    LEFT JOIN reportes rp ON r.id_reporte_padre = rp.id_reporte
                    LEFT JOIN mascotas m ON r.id_mascota = m.id_mascota
                    LEFT JOIN catalogos cat ON m.id_catalogos = cat.id_catalogo
                    LEFT JOIN razas rz ON cat.id_razas = rz.id_raza
                    LEFT JOIN especies esp ON rz.id_especie = esp.id_especie
                    LEFT JOIN sexos_mascota sm ON m.id_sexos = sm.id_sexo_masc
                    LEFT JOIN colonias col ON r.id_colonia = col.id_colonia
                    LEFT JOIN codigos_postales cp ON col.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias alc ON cp.id_alcaldias = alc.id_alcaldia
                    WHERE r.tipo_reporte = 'localizado' AND r.estatus = 1
                    ORDER BY r.id_reporte DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 4. Validar el Folio ingresado por un dueño para revelar teléfono y datos exactos de contacto
    public function validarFolioParaContacto($id_avistamiento, $folio_ingresado)
    {
        try {
            // Extraer solo los dígitos (por si el dueño escribe "RUPE-0003" o solo "3")
            $numeroFolio = (int) preg_replace('/[^0-9]/', '', $folio_ingresado);
            if ($numeroFolio <= 0) {
                return null;
            }

            // Verifica que el folio exista como reporte de extravío activo
            $sqlCheck = "SELECT id_reporte FROM reportes 
                         WHERE id_reporte = :folio AND tipo_reporte = 'extraviado' AND estatus = 1 LIMIT 1";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':folio' => $numeroFolio]);

            if (!$stmtCheck->fetchColumn()) {
                return null; // El folio no existe
            }

            // Devuelve los datos privados de contacto del ciudadano que localizó a la mascota
            $sql = "SELECT r.id_reporte, r.nombre_invitado, r.telefono_invitado, r.calle, r.en_resguardo, r.comentarios, r.fecha, r.hora,
                           col.nombre_col AS nombre_colonia, alc.alcaldia_mpio AS nombre_municipio
                    FROM reportes r
                    LEFT JOIN colonias col ON r.id_colonia = col.id_colonia
                    LEFT JOIN codigos_postales cp ON col.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias alc ON cp.id_alcaldias = alc.id_alcaldia
                    WHERE r.id_reporte = :id_avistamiento AND r.tipo_reporte = 'localizado' AND r.estatus = 1
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_avistamiento' => $id_avistamiento]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
        // 5. Marcar como recuperado tanto el reporte padre (folio del dueño) como vincular el avistamiento
    public function confirmarRecuperacionPorFolio($id_avistamiento, $folio_ingresado)
    {
        try {
            $numeroFolio = (int) preg_replace('/[^0-9]/', '', $folio_ingresado);
            if ($numeroFolio <= 0 || !$id_avistamiento) {
                return false;
            }

            // 1. Verificar que el folio exista como reporte de extravío activo
            $sqlCheck = "SELECT id_reporte FROM reportes 
                         WHERE id_reporte = :folio AND tipo_reporte = 'extraviado' AND estatus = 1 LIMIT 1";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':folio' => $numeroFolio]);
            if (!$stmtCheck->fetchColumn()) {
                return false;
            }

            // 2. Actualizar el reporte original del dueño a 'recuperado'
            $sqlPadre = "UPDATE reportes SET estado_reporte = 'recuperado' 
                         WHERE id_reporte = :folio AND tipo_reporte = 'extraviado'";
            $stmtPadre = $this->pdo->prepare($sqlPadre);
            $stmtPadre->execute([':folio' => $numeroFolio]);

            // 3. Vincular el avistamiento al reporte del dueño para que en el muro salga 'RECUPERADA POR SU DUEÑO'
            $sqlHijo = "UPDATE reportes SET id_reporte_padre = :folio, estado_reporte = 'recuperado' 
                        WHERE id_reporte = :id_avistamiento AND tipo_reporte = 'localizado'";
            $stmtHijo = $this->pdo->prepare($sqlHijo);
            return $stmtHijo->execute([
                ':folio'           => $numeroFolio,
                ':id_avistamiento' => $id_avistamiento
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

}
