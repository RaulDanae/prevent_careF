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
        "SELECT t1.id, t1.nombre, t1.usuario, t1.perfil, t1.estatus
        FROM staff t1
        WHERE t1.id = ?"
    );

    $stmt->execute([$id]);

    echo json_encode($stmt->fetch());

?>