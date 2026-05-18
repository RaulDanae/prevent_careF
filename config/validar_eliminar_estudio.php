<?php
require_once '../config/database1.php';

$conn = conn();

$id_estudio = $_POST['id_estudio'] ?? 0;
$id_paev = $_POST['id_paev'] ?? 0;

$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM resultados_estudios
    WHERE id_estudio = ?
    AND codigo_barra = ?
    AND resultado IS NOT NULL
");

$stmt->execute([$id_estudio, $id_paev]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "bloqueado" => $row['total'] > 0
]);