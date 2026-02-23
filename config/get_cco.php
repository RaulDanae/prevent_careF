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
        "SELECT t1.curp, t1.colaborador, t1.genero, t1.fec_nac, t1.email, t1.edad, t2.celular, t2.peso, t2.peso, 
                t2.talla, t2.marcapasos, t2.obs_corpo
         FROM pacientes t1
         LEFT JOIN tcorporal t2 ON t1.curp = t2.curp
         WHERE t1.curp = ?"
    );

    $stmt->execute([$curp]);

    echo json_encode($stmt->fetch());

?>