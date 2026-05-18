<?php
    require_once '../config/database1.php';

    header('Content-Type: application/json; charset=utf-8');

    $id = $_POST['id'] ?? '';

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID requerido']);
        exit;
    }

    $conn = conn(); // 👈 aquí obtienes el PDO

    $stmt = $conn->prepare(
        "SELECT t1.id_evento, t1.nomevento, t1.id_comp, t1.tipo_evento, t1.global, t1.fecha_evento, t1.nombre_corto  
         FROM eventos t1
         WHERE t1.id_evento = ?"
    );

    $stmt->execute([$id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$evento){
        echo json_encode(["error"=>"No encontrado"]);
        exit;
    }

    // Sucursales en el evento
    $stmt = $conn->prepare("
        SELECT t1.id_sucursal, t2.nombre_sucursal
        FROM evento_sucursal t1
        LEFT JOIN sucursal t2 ON t1.id_sucursal = t2.id_sucursal
        WHERE t1.id_evento = ?
    ");

    $stmt->execute([$id]);
    $sucurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Contar el total de pacientes
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM paciente_evento 
        WHERE id_evento = ?
    ");
    $stmt->execute([$id]);
    $totalPacientes = (int)$stmt->fetchColumn();

    // Revision de que evento ya tiene registro capturados
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM paciente_evento 
        WHERE id_evento = ?
        AND (
            hora_evento IS NOT NULL 
            OR hora_toma_muestra IS NOT NULL
        )
    ");
    $stmt->execute([$id]);
    $tieneCaptura = (int)$stmt->fetchColumn() > 0;

    // Perfiles del evento
    $stmt = $conn->prepare("
        SELECT 
            ep.id_perfil, 
            p.nombre,
            EXISTS(
                SELECT 1 
                FROM paciente_evento pe
                WHERE pe.id_evento = ep.id_evento
                AND (
                    pe.hora_evento IS NOT NULL 
                    OR pe.hora_toma_muestra IS NOT NULL
                )
            ) AS bloqueado
        FROM evento_perfiles ep
        INNER JOIN perfilestudios p ON ep.id_perfil = p.id
        WHERE ep.id_evento = ?
    ");
    $stmt->execute([$id]);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Respuesta
    echo json_encode([
        "id"                  => $evento['id_evento'],
        "nombreevento"        => $evento['nomevento'],
        "compania"            => $evento['id_comp'],
        "tevento"             => $evento['tipo_evento'],
        "global"              => $evento['global'],
        "fevento"             => $evento['fecha_evento'],
        "ncorto"              => $evento['nombre_corto'],
        "sucursal"            => $sucurs,
        "perfiles"            => $perfiles,
        "total_perfiles"      => count($perfiles),
        "total_pacientes"     => $totalPacientes,
        "tiene_captura"       => $tieneCaptura
    ]);

?>