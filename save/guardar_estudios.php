<?php

    header('Content-Type: application/json');
    require_once __DIR__ . '/../config/database1.php';

    $conn = conn();

    $data = json_decode(file_get_contents("php://input"), true);

    $conn->beginTransaction();

    try{

        // Guardar estudio
        $sql = "INSERT INTO estudios(clave, nombre, usuario) VALUES (?,?,?)";

        $stmt = $conn -> prepare($sql);
        $stmt -> execute([$data['clave'], 
                          $data['nombre'], 
                          $data['usuario']]);

        $estudio_id = $conn -> lastInsertId();

        // Guardar perfiles
        $perfiles = $data['perfiles'] ?? [];

        if(empty($perfiles)){
            throw new Exception("Debes seleccionar al menos un perfil");
        }

        foreach($perfiles as $perfil){

            $sql = "INSERT IGNORE INTO estudio_perfil (id_estudio, id_perfil) VALUES (?, ?)";
            $stmt = $conn -> prepare($sql);
            $stmt -> execute([$estudio_id, $perfil]);

        }

        // Configuraciones
        $orden_config = 1;

        foreach($data['configuraciones'] as $config){

            if(
                empty($config['metodologia']) &&
                empty($config['unidad']) &&
                empty($config['muestra']) &&
                empty($config['recipiente'])
            ){

                continue;

            }

            $sql = "INSERT INTO estudio_config (id_estudio, id_metodologia, id_unidad, id_muestra, id_recipiente, orden_configuracion)
                         VALUES (?,?,?,?,?,?)";

            $stmt = $conn -> prepare($sql);
            $stmt -> execute([
                $estudio_id,
                $config['metodologia'],
                $config['unidad'],
                $config['muestra'],
                $config['recipiente'],
                $orden_config
            ]);

            $config_id =  $conn -> lastInsertId();

            $orden_rango = 1;

            foreach($config['rangos'] as $r){

                if(empty($r['genero']) || empty($r['edad_min']) || empty($r['edad_max'])){

                    continue;

                }

                $sql = "INSERT INTO rango_estudio (id_estudio_config, genero, edad_min, edad_max, valor_bajo, limite_inf, limite_sup, valor_alto, valor_critico, orden_rango)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn -> prepare($sql);
                $stmt -> execute([
                    $config_id,
                    $r['genero'],
                    $r['edad_min'],
                    $r['edad_max'],
                    $r['valor_bajo'],
                    $r['lim_inf'],
                    $r['lim_sup'],
                    $r['valor_alto'],
                    $r['valor_critico'],
                    $orden_rango
                ]);

                $orden_rango++;

            }

            $orden_config++;

        }

        $conn -> commit();

        echo json_encode(["success"=>true]);

    } catch(Exception $e){

        $conn -> rollBack();

        echo json_encode([
            "success"=>false,
            "message"=>$e -> getMessage()
        ]);
    }

