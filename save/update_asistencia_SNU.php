<?php
    session_start();
    require_once '../config/database1.php';

    header('Content-Type: application/json');

    $conn = conn();

    $id_paciente_evento = $_POST['codigo'] ?? '';
    $usuario = $_SESSION['usuario'] ?? 'SYSTEM';

    if (!$id_paciente_evento) {
        echo json_encode(['success' => false, 'message' => 'ID vacío']);
        exit;
    }

    try {

        // Actualizar
        $stmt = $conn->prepare("
            INSERT INTO tnutricional (id_paciente_evento, acudiosn, usnutr)
            VALUES (?, 'SI', ?)
            ON DUPLICATE KEY UPDATE
                acudiosn = VALUES(acudiosn),
                usnutr = VALUES(usnutr)
        ");

        $stmt->execute([$id_paciente_evento, $usuario]);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor'
        ]);
    }