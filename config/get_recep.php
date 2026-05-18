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

    $stmt = $conn->prepare("
        SELECT 
            pe.id_paciente_evento,
            p.colaborador,
            p.fec_nac,
            p.curp,
            c.nomcom,
            s.nombre_sucursal,
            pe.id_evento
        FROM paciente_evento pe
        INNER JOIN pacientes p ON pe.id_paciente = p.id
        INNER JOIN compania c ON p.cod_comp = c.id_comp
        INNER JOIN sucursal s ON p.id_sucursal = s.id_sucursal
        WHERE pe.id_paciente_evento = ?
    ");
    $stmt->execute([$id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT pp.id_perfil, p.nombre
        FROM paciente_perfiles pp
        INNER JOIN perfilestudios p ON pp.id_perfil = p.id
        WHERE pp.id_paciente_evento = ?
    ");
    $stmt->execute([$id]);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT r.id_estudio, e.nombre
        FROM resultados_estudios r
        INNER JOIN estudios e ON r.id_estudio = e.id
        WHERE r.codigo_barra = ?
    ");
    $stmt->execute([$id]);
    $estudios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "paciente" => $paciente,
        "perfiles" => $perfiles,
        "estudios" => $estudios
    ]);
?>