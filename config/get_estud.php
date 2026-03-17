<?php
    require_once '../config/database1.php';

    $input = json_decode(file_get_contents("php://input"), true);

    $clave = $_POST['clave'] ?? $input['clave'] ?? '';

    if (!$clave) {
        http_response_code(400);
        echo json_encode(['error' => 'Clave requerida']);
        exit;
    }

    $conn = conn(); // 👈 aquí obtienes el PDO

    // Datos Principales
    $stmt = $conn->prepare("
        SELECT id, clave, nombre, usuario 
        FROM estudios 
        WHERE clave = ?
    ");
    $stmt->execute([$clave]);
    $estudio = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$estudio){
        echo json_encode(["error"=>"No encontrado"]);
        exit;
    }

    // Perfiles
    $stmt = $conn->prepare("
        SELECT p.id, p.nombre
        FROM estudio_perfil ep
        JOIN perfilestudios p ON ep.id_perfil = p.id
        WHERE ep.id_estudio = ?
    ");
    $stmt->execute([$estudio['id']]);

    $perfiles = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $perfiles[] = [
            "id"=>$row['id'],
            "text"=>$row['nombre']
        ];
    }
    
    // Configuraciones
    $stmt = $conn->prepare("
        SELECT 
            c.id,
            c.id_metodologia,
            c.id_unidad,
            c.id_muestra,
            c.id_recipiente,
            m.nombre metodologia,
            u.nombre unidad,
            mu.nombre muestra,
            r.nombre recipiente
        FROM estudio_config c
        LEFT JOIN tmetodologias m ON c.id_metodologia = m.id
        LEFT JOIN tunidades u ON c.id_unidad = u.id
        LEFT JOIN tipomuestras mu ON c.id_muestra = mu.id
        LEFT JOIN trecipientes r ON c.id_recipiente = r.id
        WHERE c.id_estudio = ?
        ORDER BY c.orden_configuracion
    ");
    $stmt->execute([$estudio['id']]);

    $configuraciones = [];

    while($config = $stmt->fetch(PDO::FETCH_ASSOC)){

        // Rangos por configuración
        $stmt2 = $conn->prepare("
            SELECT *
            FROM rango_estudio
            WHERE id_estudio_config = ?
            ORDER BY orden_rango
        ");
        $stmt2->execute([$config['id']]);

        $rangos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $configuraciones[] = [
                "metodologia" => [
                    "id" => $config['id_metodologia'],
                    "text" => $config['metodologia']
                ],
                "unidad" => [
                    "id" => $config['id_unidad'],
                    "text" => $config['unidad']
                ],
                "muestra" => [
                    "id" => $config['id_muestra'],
                    "text" => $config['muestra']
                ],
                "recipiente" => [
                    "id" => $config['id_recipiente'],
                    "text" => $config['recipiente']
                ],
                "rangos" => $rangos
        ];
    }    


    // Datos finales
    echo json_encode([
        "clave" => $estudio['clave'],
        "nombre" => $estudio['nombre'],
        "perfiles" => $perfiles,
        "configuraciones" => $configuraciones
    ]);

?>

