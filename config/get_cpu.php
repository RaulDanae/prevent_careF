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
        "SELECT t1.curp, t1.colaborador, t1.genero, t1.fec_nac, t3.peso, t3.talla, t1.edad, 
                t2.fvc, t2.fev1, t2.fev1_fvc, t2.consultaneum, t2.obs_pul
         FROM pacientes t1
         LEFT JOIN fpulmonar t2 ON t1.curp = t2.curp
         LEFT JOIN tcorporal t3 ON t1.curp = t3.curp
         WHERE t1.curp = ?"
    );

    $stmt->execute([$curp]);

    echo json_encode($stmt->fetch());

?>