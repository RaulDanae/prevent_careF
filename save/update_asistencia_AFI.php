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
            INSERT INTO tfisica (id_paciente_evento, acudiofis, usfis)
            VALUES (?, 'SI', ?)
            ON DUPLICATE KEY UPDATE
                acudiofis = VALUES(acudiofis),
                usfis = VALUES(usfis)
        ");

        $stmt->execute([$id_paciente_evento, $usuario]);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor'
        ]);
    }