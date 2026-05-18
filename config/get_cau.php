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
        "SELECT t2.id_paciente_evento, t1.colaborador, t1.genero, t1.fec_nac, t3.od_500, t3.od_1000, t3.od_2000, t3.od_4000, 
                t3.oi_500, t3.oi_1000, t3.oi_2000, t3.oi_4000, t3.consultaaud, t3.obs_aud
         FROM pacientes t1
         LEFT JOIN paciente_evento t2 ON t1.id = t2.id_paciente
         LEFT JOIN tauditivo t3 ON t2.id_paciente_evento = t3.id_paciente_evento
         WHERE t2.id_paciente_evento = ?"
    );

    $stmt->execute([$editidpaceven]);

    echo json_encode($stmt->fetch());

?>