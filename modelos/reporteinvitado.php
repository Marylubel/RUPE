<?php

require_once __DIR__ . "/BasedeDatos.php";

class Reporteinvitado
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    // 1. Obtener catálogos de especies y sexos de mascota
    public function obtenerEspecies()
    {
        try {
            $stmt = $this->pdo->query("SELECT id_especie, nom_especie FROM especies ORDER BY nom_especie ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerRazasPorEspecie($id_especie)
    {
        try {
            $sql = "SELECT c.id_catalogo, r.nom_raza 
                    FROM catalogos c
                    INNER JOIN razas r ON c.id_razas = r.id_raza
                    WHERE r.id_especie = :id_especie
                    ORDER BY r.nom_raza ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_especie' => $id_especie]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerSexosMascota()
    {
        try {
            $stmt = $this->pdo->query("SELECT id_sexo_masc, sexos FROM sexos_mascota ORDER BY id_sexo_masc ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 2. Registrar Mascota sin cuenta (id_usuario = NULL) + Reporte de Extravío en transacción
    public function registrarReporteCompletoInvitado($datosMascota, $datosReporte)
    {
        try {
            $this->pdo->beginTransaction();

            // A. Insertar en tabla mascotas con id_usuario = NULL
            $sqlMascota = "INSERT INTO mascotas (
                               id_usuario, nombre, id_catalogos, id_sexos, edad, 
                               color, tamano, peso_aprox, sena_particular, foto, estatus
                           ) VALUES (
                               NULL, :nombre, :id_catalogos, :id_sexos, :edad, 
                               :color, :tamano, :peso_aprox, :sena_particular, :foto, 1
                           )";
            $stmtM = $this->pdo->prepare($sqlMascota);
            $stmtM->execute([
                ':nombre'          => $datosMascota['nombre'],
                ':id_catalogos'    => !empty($datosMascota['id_catalogos']) ? $datosMascota['id_catalogos'] : null,
                ':id_sexos'        => !empty($datosMascota['id_sexos']) ? $datosMascota['id_sexos'] : null,
                ':edad'            => $datosMascota['edad'] !== '' ? $datosMascota['edad'] : null,
                ':color'           => $datosMascota['color'],
                ':tamano'          => $datosMascota['tamano'],
                ':peso_aprox'      => $datosMascota['peso_aprox'] !== '' ? $datosMascota['peso_aprox'] : null,
                ':sena_particular' => $datosMascota['sena_particular'],
                ':foto'            => $datosMascota['foto']
            ]);

            $id_mascota = $this->pdo->lastInsertId();

            // B. Insertar en tabla reportes vinculado a esa mascota
            $sqlReporte = "INSERT INTO reportes (
                               tipo_reporte, id_usuario, id_mascota, id_colonia,
                               nombre_invitado, telefono_invitado,
                               calle, entre_calle, y_calle, foto, fecha, hora,
                               comentarios, estado_reporte, codigo_qr, estatus
                           ) VALUES (
                               'extraviado', NULL, :id_mascota, :id_colonia,
                               :nombre_invitado, :telefono_invitado,
                               :calle, :entre_calle, :y_calle, :foto, :fecha, :hora,
                               :comentarios, 'activo', '', 1
                           )";
            $stmtR = $this->pdo->prepare($sqlReporte);
            $stmtR->execute([
                ':id_mascota'        => $id_mascota,
                ':id_colonia'        => !empty($datosReporte['id_colonia']) ? $datosReporte['id_colonia'] : null,
                ':nombre_invitado'   => $datosReporte['nombre_invitado'],
                ':telefono_invitado' => $datosReporte['telefono_invitado'],
                ':calle'             => $datosReporte['calle'],
                ':entre_calle'       => $datosReporte['entre_calle'],
                ':y_calle'           => $datosReporte['y_calle'],
                ':foto'              => $datosMascota['foto'],
                ':fecha'             => $datosReporte['fecha'],
                ':hora'              => $datosReporte['hora'],
                ':comentarios'       => $datosReporte['comentarios']
            ]);

            $id_reporte = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return $id_reporte;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            die("Error al registrar reporte de invitado: " . $e->getMessage());
        }
    }

    // 3. Guardar ruta del QR generado
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

    // 4. Obtener detalle completo para generar el Cartel y el PDF
    public function obtenerDetalleReporte($id_reporte)
    {
        try {
            $sql = "SELECT r.*, 
                           m.nombre AS nombre_mascota, m.edad, m.color, m.tamano, m.peso_aprox, m.sena_particular, m.foto AS foto_mascota,
                           rz.nom_raza, esp.nom_especie, sm.sexos,
                           col.nombre_col AS nombre_colonia, cp.codigo_postal,
                           alc.alcaldia_mpio AS nombre_municipio, est.nombre_edo AS nombre_estado
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
            return null;
        }
    }
}
