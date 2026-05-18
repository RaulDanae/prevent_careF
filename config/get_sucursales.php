<?php
require_once '../config/database1.php';

$conn = conn();

$id_comp = $_POST['id_comp'] ?? 0;

$stmt = $conn->prepare("
    SELECT id_sucursal, nombre_sucursal
    FROM sucursal
    WHERE id_comp = ?
");

$stmt->execute([$id_comp]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));