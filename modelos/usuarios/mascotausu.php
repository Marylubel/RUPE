<?php

require_once __DIR__ . "/../BasedeDatos.php";

class Mascotausu
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    public function obtenerEspecies()
    {
        try {
            $sql = "SELECT id_especie, nom_especie FROM especies ORDER BY nom_especie ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerSexos()
    {
        try {
            $sql = "SELECT id_sexo_masc, sexos FROM sexos_mascota ORDER BY sexos ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Trae las razas de la especie elegida junto con la foto de referencia si existe en 'catalogo'
    public function obtenerRazasPorEspecie($id_especie)
    {
        try {
            $sql = "SELECT r.id_raza, r.nom_raza, c.id_catalogo, c.foto
                    FROM razas r
                    LEFT JOIN catalogos c ON r.id_raza = c.id_razas
                    WHERE r.id_especie = :id_especie
                    ORDER BY r.nom_raza ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_especie' => $id_especie]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Busca el id_catalogo de esa raza o lo crea automáticamente si el admin aún no sube foto
    public function obtenerOCrearIdCatalogo($id_raza)
    {
        if (empty($id_raza)) {
            return null;
        }
        try {
            $sql = "SELECT id_catalogo FROM catalogos WHERE id_razas = :id_raza LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_raza' => $id_raza]);
            $id_catalogo = $stmt->fetchColumn();

            if ($id_catalogo) {
                return $id_catalogo;
            }

            $sqlInsert = "INSERT INTO catalogos (id_razas, foto) VALUES (:id_raza, '/RUPE/assets/fotos/Mascota.jpg')";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([':id_raza' => $id_raza]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function registrarMascota($id_usuario, $id_raza, $id_sexo, $nombre, $edad, $color, $foto, $sena_particular, $tamano, $peso_aprox)
    {
        $id_catalogos = $this->obtenerOCrearIdCatalogo($id_raza);

        try {
            $sql = "INSERT INTO mascotas (
                        id_catalogos, id_usuario, id_sexos, nombre, edad, 
                        color, foto, sena_particular, tamano, peso_aprox
                    ) VALUES (
                        :id_catalogos, :id_usuario, :id_sexos, :nombre, :edad, 
                        :color, :foto, :sena_particular, :tamano, :peso_aprox
                    )";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_catalogos'    => $id_catalogos,
                ':id_usuario'      => !empty($id_usuario) ? $id_usuario : null, //es null para cuando ingresen invitados
                ':id_sexos'        => $id_sexo,
                ':nombre'          => $nombre,
                ':edad'            => $edad,
                ':color'           => $color,
                ':foto'            => $foto,
                ':sena_particular' => $sena_particular,
                ':tamano'          => $tamano,
                ':peso_aprox'      => $peso_aprox
            ]);
        } catch (PDOException $e) {
            die("Error al registrar la mascota: " . $e->getMessage());
        }
    }
    //Consulta de las mascotas registradas por usuario en caso de tener varias
    public function obtenerMascotasPorUsuario($id_usuario)
    {
        try {
            $sql = "SELECT m.*, s.sexos, r.nom_raza, e.nom_especie
                    FROM mascotas m
                    LEFT JOIN sexos_mascota s ON m.id_sexos = s.id_sexo_masc
                    LEFT JOIN catalogos c ON m.id_catalogos = c.id_catalogo
                    LEFT JOIN razas r ON c.id_razas = r.id_raza
                    LEFT JOIN especies e ON r.id_especie = e.id_especie
                    WHERE m.id_usuario = :id_usuario AND m.estatus = 1
                    ORDER BY m.id_mascota DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    //Para obtener los datos de una mascota en especifico
    public function obtenerMascotaPorId($id_mascota, $id_usuario)
    {
        try {
            $sql = "SELECT m.*, c.id_razas AS id_raza, r.id_especie
                    FROM mascotas m
                    LEFT JOIN catalogos c ON m.id_catalogos = c.id_catalogo
                    LEFT JOIN razas r ON c.id_razas = r.id_raza
                    WHERE m.id_mascota = :id_mascota AND m.id_usuario = :id_usuario AND m.estatus = 1
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_mascota' => $id_mascota, ':id_usuario' => $id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    //Para modificar los datos de una mascota existente
    public function modificarMascota($id_mascota, $id_usuario, $id_raza, $id_sexo, $nombre, $edad, $color, $foto, $sena_particular, $tamano, $peso_aprox)
    {
        //al elegir raza, se busca o crea catalogo, si llega si dato, conserva el dato que ya tenía
        //$id_catalogos = $this->obtenerOCrearIdCatalogo($id_raza);
        $id_catalogos = !empty($id_raza) ? $this->obtenerOCrearIdCatalogo($id_raza) : null;
        if (empty($id_catalogos)) {
            $stmtCat = $this->pdo->prepare("SELECT id_catalogos FROM mascotas WHERE id_mascota = :id LIMIT 1");
            $stmtCat->execute([':id' => $id_mascota]);
            $id_catalogos = $stmtCat->fetchColumn();
        }

        try {
            $sql = "UPDATE mascotas SET 
                        id_catalogos    = :id_catalogos,
                        id_sexos        = :id_sexos,
                        nombre          = :nombre,
                        edad            = :edad,
                        color           = :color,
                        foto            = :foto,
                        sena_particular = :sena_particular,
                        tamano          = :tamano,
                        peso_aprox      = :peso_aprox
                    WHERE id_mascota = :id_mascota AND id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_catalogos'    => $id_catalogos,
                ':id_sexos'        => !empty($id_sexo) ? $id_sexo : null,
                ':nombre'          => $nombre,
                ':edad'            => $edad,
                ':color'           => $color,
                ':foto'            => $foto,
                ':sena_particular' => $sena_particular,
                ':tamano'          => $tamano,
                ':peso_aprox'      => $peso_aprox,
                ':id_mascota'      => $id_mascota,
                ':id_usuario'      => $id_usuario
            ]);
        } catch (PDOException $e) {
            die("Error al modificar la mascota: " . $e->getMessage());
        }
    }
    public function eliminarMascota($id_mascota, $id_usuario) 
    {
        try{
            $sql = "UPDATE mascotas SET estatus = 0
            WHERE id_mascota = :id_mascota AND id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id_mascota' => $id_mascota,
                ':id_usuario' => $id_usuario]);
        } catch (PDOException $e){
            return false;
        }
    }
}
