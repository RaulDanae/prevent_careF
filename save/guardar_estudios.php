<?php

    header('Content-Type: application/json');
    require_once __DIR__ . '/../config/database1.php';

    // Funciones utilitarias
    function nullIfEmpty($v) {
        return ($v === '' || $v === null) ? null : $v;
    }

    $upper = fn($v) => mb_strtoupper(trim($v), 'UTF-8');

    $conn = conn();

    $data = json_decode(file_get_contents("php://input"), true);

    $conn->beginTransaction();

    try{

        // Guardar estudio
        $sql = "INSERT INTO estudios(clave, nombre, usuario) VALUES (?,?,?)";

        $stmt = $conn -> prepare($sql);
        $stmt -> execute([$upper($data['clave']), 
                          $upper($data['nombre']), 
                          $data['usuario']
                          ]);

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

                if(empty($r['genero']) && empty($r['edad_min']) && empty($r['edad_max'])){

                    continue;

                }

                $sql = "INSERT INTO rango_estudio (id_estudio_config, genero, edad_min, edad_max, valor_bajo, limite_inf, limite_sup, valor_alto, valor_critico, orden_rango)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn -> prepare($sql);
                $stmt -> execute([
                    $config_id,
                    $r['genero'],
                    nullIfEmpty($r['edad_min']),
                    nullIfEmpty($r['edad_max']),
                    nullIfEmpty($r['valor_bajo']),
                    nullIfEmpty($r['lim_inf']),
                    nullIfEmpty($r['lim_sup']),
                    nullIfEmpty($r['valor_alto']),
                    nullIfEmpty($r['valor_critico']),
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

