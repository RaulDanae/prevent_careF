<?php
    require_once '../config/database1.php';

    header('Content-Type: application/json; charset=utf-8');

    $curp = $_POST['curp'] ?? '';

    if (!$curp) {
        http_response_code(400);
        echo json_encode(['error' => 'CURP requerido']);
        exit;
    }

    $conn = conn(); // 👈 aquí obtienes el PDO

    $stmt = $conn->prepare(
        "SELECT t1.curp, t1.colaborador, t1.genero, t1.fec_nac, t2.od_500, t2.od_1000, t2.od_2000, t2.od_4000, 
                t2.oi_500, t2.oi_1000, t2.oi_2000, t2.oi_4000, t2.consultaaud, t2.obs_aud
         FROM pacientes t1
         LEFT JOIN tauditivo t2 ON t1.curp = t2.curp
         WHERE t1.curp = ?"
    );

    $stmt->execute([$curp]);

    echo json_encode($stmt->fetch());

?>