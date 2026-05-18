<?php
require_once '../config/database1.php';

$conn = conn();

$id_perfil = $_GET['id_perfil'] ?? 0;

$stmt = $conn->prepare("
    SELECT e.id, e.nombre
    FROM estudio_perfil pe
    INNER JOIN estudios e ON pe.id_estudio = e.id
    WHERE pe.id_perfil = ?
");

$stmt->execute([$id_perfil]);

$resultados = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $resultados[] = [
        "id" => (string)$row['id'],
        "nombre" => $row['nombre']
    ];
}

echo json_encode($resultados);