<?php

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/database1.php';

try {

    if (!isset($_SESSION['usuario'])) {
        throw new Exception('Sesión no válida');
    }

    $id_evento = isset($_POST['id_evento']) ? (int)$_POST['id_evento'] : 0;

    if ($id_evento <= 0) {
        throw new Exception('ID de evento inválido');
    }

    $usuario = $_SESSION['usuario'];

    $conn = conn();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();

    // ==============================
    // 1. INSERTAR NUEVOS PACIENTES
    // ==============================
    $stmtInsert = $conn->prepare("
        INSERT IGNORE INTO paciente_evento (id_paciente, id_evento, usuario, origen)
        SELECT p.id, ?, ?, 'AUTO'
        FROM pacientes p
        WHERE p.id_sucursal IN (
            SELECT es.id_sucursal
            FROM evento_sucursal es
            WHERE es.id_evento = ?
        )
    ");

    $stmtInsert->execute([
        $id_evento,
        $usuario,
        $id_evento
    ]);

    // ==============================
    // 2. ELIMINAR PACIENTES (CONTROLADO)
    // ==============================
    // ⚠️ SOLO elimina si NO tienen datos clínicos

    $stmtDelete = $conn->prepare("
        DELETE pe
        FROM paciente_evento pe
        LEFT JOIN pacientes p ON pe.id_paciente = p.id
        WHERE pe.id_evento = ?
        AND p.id_sucursal NOT IN (
            SELECT es.id_sucursal
            FROM evento_sucursal es
            WHERE es.id_evento = ?
        )
        AND pe.hora_evento IS NULL
        AND pe.hora_toma_muestra IS NULL
        AND pe.origen = 'AUTO'
        AND NOT EXISTS (
            SELECT 1
            FROM resultados_estudios r
            WHERE r.codigo_barra = pe.id_paciente_evento
        )
    ");

    $stmtDelete->execute([$id_evento, $id_evento]);

    $conn->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}