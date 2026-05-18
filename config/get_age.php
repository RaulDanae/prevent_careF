<?php
    require_once '../config/database1.php';

    header('Content-Type: application/json; charset=utf-8');

    $editidpaceven = $_POST['editidpaceven'] ?? '';

    if (!$editidpaceven) {
        http_response_code(400);
        echo json_encode(['error' => 'Id de paciente por evento requerido']);
        exit;
    }

    $conn = conn(); // aquí obtienes el PDO

    $stmt = $conn->prepare(
        "SELECT
            pe.id_paciente_evento,
            p.colaborador,
            IFNULL(TIMESTAMPDIFF(YEAR, p.fec_nac, CURDATE()), '') AS edad,
            c.nomcom,
            s.nombre_sucursal,
            e.nomevento,
            p.curp,
            p.rfc,
            pe.aprivacidad,
            pe.cinformado,
            pe.programa_htm,
            pe.hora_toma_muestra,
            pe.programa_he,
            pe.hora_evento,
            pe.observaciones
        FROM paciente_evento pe
        INNER JOIN pacientes p ON pe.id_paciente = p.id
        INNER JOIN eventos e ON pe.id_evento = e.id_evento
        INNER JOIN compania c ON e.id_comp = c.id_comp
        INNER JOIN sucursal s ON p.id_sucursal = s.id_sucursal
        WHERE pe.id_paciente_evento = ?"
    );

    $stmt->execute([$editidpaceven]);

    echo json_encode($stmt->fetch());

?>