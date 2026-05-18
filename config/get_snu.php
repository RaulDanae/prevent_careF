<?php
    require_once '../config/database1.php';

    header('Content-Type: application/json; charset=utf-8');

    $editidpaceven = $_POST['editidpaceven'] ?? '';

    if (!$editidpaceven) {
        http_response_code(400);
        echo json_encode(['error' => 'Id de paciente por evento requerido']);
        exit;
    }

    $conn = conn(); // 👈 aquí obtienes el PDO

    $stmt = $conn->prepare(
        "SELECT t2.id_paciente_evento, t1.colaborador, t1.genero, t1.fec_nac, t3.acudiosn, t3.obs_nutr
         FROM pacientes t1
         LEFT JOIN paciente_evento t2 ON t1.id = t2.id_paciente
         LEFT JOIN tnutricional t3 ON t2.id_paciente_evento = t3.id_paciente_evento
         WHERE t2.id_paciente_evento = ?"
    );

    $stmt->execute([$editidpaceven]);

    echo json_encode($stmt->fetch());

?>