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
        "SELECT t1.id, t1.cod_comp, t2.compania, t1.clave, t1.colaborador, t1.fec_nac, t1.genero, t1.curp, t1.email, t1.rfc,
                t1.edad, t1.aprivacidad, t1.cinformado, t1.hrtomamuestra, t1.hrferia, t1.obs_reg, t1.fregistro, t1.hregistro 
        FROM pacientes t1
        LEFT JOIN compania t2 ON t1.cod_comp = t2.id_comp
        WHERE t1.id = ?"
    );

    $stmt->execute([$id]);

    echo json_encode($stmt->fetch());

?>