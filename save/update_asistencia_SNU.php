<?php
    session_start();
    require_once '../config/database1.php';

    header('Content-Type: application/json');

    $conn = conn();

    $id_reg = $_POST['codigo'] ?? '';

    if (!$id_reg) {
        echo json_encode(['success' => false, 'message' => 'ID vacío']);
        exit;
    }

    try {

        // Busqueda paciente por ir_red
        $stmt = $conn->prepare("SELECT curp FROM pacientes WHERE id_reg = ?");
        $stmt->execute([$id_reg]);
        $paciente = $stmt->fetch();

        if (!$paciente) {
            echo json_encode(['success' => false, 'message' => 'No encontrado']);
            exit;
        }

        $curp = $paciente['curp'];

        // Actualizar
        $stmt = $conn->prepare("
            INSERT INTO tnutricional (curp, acudiosn, fnutr, hnutr, usnutr)
            VALUES (?, 'SI', CURDATE(), CURTIME(), ?)
            ON DUPLICATE KEY UPDATE
                acudiosn = 'SI',
                fnutr = CURDATE(),
                hnutr = CURTIME(),
                usnutr = VALUES(usnutr)
        ");

        $stmt->execute([$curp, $_SESSION['usuario']]);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor'
        ]);
    }