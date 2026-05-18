<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database1.php';

try {

    $conn = conn();

    $id_evento = $_POST['id_evento'] ?? null;

    $sql = "
        SELECT 
            p.id,
            p.nombre,
            CASE 
                WHEN ep.id_perfil IS NOT NULL THEN 1 
                ELSE 0 
            END AS seleccionado
        FROM perfilestudios p
        LEFT JOIN evento_perfiles ep 
            ON p.id = ep.id_perfil 
            AND ep.id_evento = ?
        ORDER BY p.nombre ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_evento]);

    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($perfiles, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}