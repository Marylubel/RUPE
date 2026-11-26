<?php

require_once __DIR__ . "/BasedeDatos.php";

class Personal
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = BasedeDatos::conectar();
    }

    //Para obtener los datos del estado, de la tabla estados y su identificador id_estado, 
    //con IN se filtra los estados que deseamos mostrar en el formulario de registro, 
    //en este caso es Ciudad de México y México.
    public function obtenerEstados()
    {
        try {
            $sql = "SELECT id_estado AS id, nombre_edo AS nombre 
            FROM estados
            WHERE id_estado IN (9, 15)               
            ORDER BY nombre_edo ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // Obtener el dato de Municipios/Alcaldías 
    public function obtenerMunicipiosPorEstado($estado_id)
    {
        try {
            $sql = "SELECT id_alcaldia AS id, alcaldia_mpio AS nombre 
                    FROM alcaldias  
                    WHERE id_estados = :estado_id 
                    ORDER BY alcaldia_mpio ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':estado_id' => $estado_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    //Obtener los datos de la tabla sepomex_abril_2016_1, que contiene los datos de colonias
    // y códigos postales, depuradas.
    public function obtenerColoniasPorCP($cp)
    {
        try {
            $sql = "SELECT id_estado, nombre_edo, id_alcaldia, alcaldia_mpio, LPAD(TRIM(codigo_postal), 5, '0') 
            AS codigo_postal, nombre_col 
                    FROM sepomex_abril_2016_1
                    WHERE codigo_postal = :cp  
                    ORDER BY nombre_col ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':cp' => (int)$cp]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die($e->getMessage());
            //return [];
        }
    }
    public function obtenerColoniasPorAlcaldias($nombre_alcaldia)
    {
        try{  //Como no coincide el id con la tabla de sepomex, se procede a buscar por nombre
            $sql = "SELECT DISTINCT nombre_col, LPAD(TRIM(codigo_postal), 5, '0') AS codigo_postal 
            FROM sepomex_abril_2016_1
            WHERE LOWER(TRIM(alcaldia_mpio)) = LOWER(TRIM(:nombre_alcaldia))
            ORDER BY nombre_col ASC";
            $stmt = $this ->pdo->prepare($sql);
            $stmt->execute([':nombre_alcaldia' => $nombre_alcaldia]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            return[];
        }
    }
    //Obtener el id_codigo para la tabla perfiles_personal de acuerdo al codigo postal a insertar
    public function obtenerIdCodigoPorCP($codigo_postal)
    {
        if(empty($codigo_postal)) return null;

        try {
            //Se comprueba que exista el codigo postal
            $sql = "SELECT id_codigo 
                    FROM codigos_postales 
                    WHERE LPAD(TRIM(codigo_postal), 5, '0') = :cp LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':cp' => str_pad(trim($codigo_postal), 5, '0', STR_PAD_LEFT)]);
            
            $id_codigo = $stmt->fetchColumn();
            if($id_codigo){
                return $id_codigo;
            }            
        //en caso de no existir, se busca la alcaldia en sepomex para registrar el codigo
        $sql2 = "SELECT a.id_alcaldia 
        FROM sepomex_abril_2016_1 s
        INNER JOIN alcaldias a ON TRIM(s.alcaldia_mpio) = TRIM(a.alcaldia_mpio)
        WHERE s.codigo_postal = :codigo_postal LIMIT 1";
        $stmt2 =$this->pdo->prepare ($sql2);
        $stmt2->execute([':codigo_postal' => (int)$codigo_postal]);
        $id_alcaldia = $stmt2->fetchColumn();

        if($id_alcaldia){
            $sql3 = "INSERT INTO codigos_postales (codigo_postal, id_alcaldias)
            VALUES (:codigo_postal, :id_alcaldia)";
            $stmt3 = $this->pdo->prepare($sql3);
            $stmt3->execute([':codigo_postal' => str_pad(trim($codigo_postal), 5, '0', STR_PAD_LEFT),
            ':id_alcaldia' => $id_alcaldia
            ]);
            return $this->pdo->lastInsertId();        
        }
            return null;

        }catch (PDOException $e) {
            return null;
        }
    }
        
    //Obtener el id_colonia para la tabla perfiles_personal de acuerdo al nombre de la colonia a insertar
    public function obtenerColoniaPorNombre($nombre_col, $id_codigo)
    {
         if(empty($nombre_col) || empty($id_codigo)) return null;

        try { //Se busca si existe la colonia en la tabla 
            $sql = "SELECT id_colonia 
                    FROM colonias 
                    WHERE TRIM(nombre_col) = TRIM(:nombre_col) 
                    AND id_codigos = :id_codigo LIMIT 1";                 
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nombre_col' => $nombre_col, ':id_codigo' => $id_codigo]);
            $id_colonia = $stmt->fetchColumn();

            if($id_colonia){
            return $id_colonia;}

        //En caso de no existir la colonia, se inserta en la tabla
        $sql4 = "INSERT INTO colonias (nombre_col, id_codigos) VALUES (:nombre_col, :id_codigo)";
        $stmt4 = $this->pdo->prepare($sql4);
        $stmt4->execute([':nombre_col' => trim($nombre_col), ':id_codigo' => $id_codigo]);
        return $this->pdo->lastInsertId();
        }catch (PDOException $e) {
            return null;}
    }
      
    public function obtenerIdPerfilPorUsuario($id_usuario)
    {
        try {
            $sql = "SELECT id_perfil 
                    FROM perfiles_personal 
                    WHERE id_usuario = :id_usuario
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return null;
        }
    }
    public function obtenerDatosPerfil($id_usuario)
    {
        try {
            $sql = "SELECT p.*, u.correo_electronico, c.nombre_col, LPAD(TRIM(cp.codigo_postal), 5, '0') 
            AS codigo_postal, a.id_alcaldia, a.alcaldia_mpio, e.id_estado, e.nombre_edo                    
                    FROM perfiles_personal p
                    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                    LEFT JOIN colonias c ON p.id_colonia = c.id_colonia
                    LEFT JOIN codigos_postales cp ON c.id_codigos = cp.id_codigo
                    LEFT JOIN alcaldias a ON cp.id_alcaldias = a.id_alcaldia
                    LEFT JOIN estados e ON a.id_estados = e.id_estado 
                    WHERE p.id_usuario = :id_usuario AND p.estatus = 1
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([':id_usuario' => $id_usuario]);
            
            $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
            //convierte el correo electronico a minusculas antes de enviarlo al formulario
            if($perfil && !empty($perfil['correo_electronico'])) {
                $perfil['correo_electronico'] = mb_strtolower(trim($perfil['correo_electronico']), 'UTF-8');
            }
            return $perfil;            
            //return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Ingreso de datos tabla personal
    public function guardarModificarDatos($id_usuario, $nombre, $ap_pat, $ap_mat, $curp, $telefono, 
    $calle, $numint, $numext, $edad, $genero, $ocupacion, $foto, $colonia_nombre, $codigo_postal)
    {
        $id_codigo = $this->obtenerIdCodigoPorCP($codigo_postal);
        $id_colonia = $this->obtenerColoniaPorNombre($colonia_nombre, $id_codigo);
        $id_perfil = $this->obtenerIdPerfilPorUsuario($id_usuario);
        
       
        //Obtener del dato del perfil del usuario, si existe, para actualizarlo, 
        //de lo contrario se inserta un nuevo registro
       $parametros = [':id_usuario' => $id_usuario, ':nombre' => $nombre, ':ap_pat' => $ap_pat,
       ':ap_mat' => $ap_mat, ':curp' => !empty(trim($curp)) ? strtoupper(trim($curp)) : null, ':telefono' => $telefono, ':calle' => $calle,
       ':numint' => $numint, ':numext' => $numext, ':edad' => !empty($edad) ? $edad : null, ':genero' => $genero,
       ':ocupacion' => $ocupacion, ':foto' => $foto, ':id_colonia' => !empty($id_colonia) ? $id_colonia : null];

        try {
            if (!empty($id_perfil)) {
                //se agrega la variable parametros con la finalidad de evitar errores de captura de datos
                //porque no se estaban leyendo todos los campos
                $parametros[':id'] = $id_perfil;
                $sql = "UPDATE perfiles_personal SET
                        id_usuario = :id_usuario, 
                        nombre_usuario = :nombre,
                        ap_paterno = :ap_pat,
                        ap_materno = :ap_mat,
                        curp = :curp,
                        num_telefono = :telefono,
                        calle = :calle,
                        num_interior = :numint,
                        num_exterior = :numext,
                        edad = :edad,
                        genero = :genero,
                        ocupacion = :ocupacion,
                        foto = :foto,
                        id_colonia = :id_colonia,
                        estatus = 1                     
                        WHERE id_perfil = :id";
            } else {
                $sql = "INSERT INTO perfiles_personal (id_usuario, nombre_usuario, ap_paterno, ap_materno, curp, 
                num_telefono, calle, num_interior, num_exterior, edad, genero, ocupacion, foto, id_colonia) 
                    VALUES (:id_usuario, :nombre, :ap_pat, :ap_mat, :curp, :telefono, :calle, :numint, :numext, 
                    :edad, :genero, :ocupacion, :foto, :id_colonia)";
            }
            $stmt = $this->pdo->prepare($sql);

            //se ejecuta el parametro
            return $stmt->execute($parametros);            
            
        } catch (PDOException $e) {
            if(isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062){
                echo "<script> alert('La CURP ya se encuentra registrada en el sistema. 
                Verifique e ingrese la CURP correcta.');
                if (window.history.length > 1){
                window.history.back();
                }else{
                    window.location.href = '/RUPE/controladores/personalusu.php';
                } 
                </script>";
                exit();
            }
            
            die("Error al guardar o modificar los datos: " . $e->getMessage());
            
        }
    }
    public function borradoLogicoPerfil($id_usuario)
    {
        try{
            $sql = "UPDATE perfiles_personal 
            SET estatus = 0 
            WHERE id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id_usuario' => $id_usuario]);
        } catch (PDOException $e){
            return false;
        }
    }
}
