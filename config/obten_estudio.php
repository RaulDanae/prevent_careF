<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database1.php';

$conn = conn();

// término de búsqueda (Select2 manda "term")
$term = $_GET['term'] ?? '';
$term = trim($term);

// query base
$sql = "SELECT id, nombre 
        FROM estudios
        WHERE nombre LIKE ?";

// 🔍 si hay búsqueda, filtrar
$params[] = "%$term%";

$sql .= " ORDER BY nombre LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$resultados = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $resultados[] = [
        "id" => (string)$row['id'],   // importante: string
        "text" => $row['nombre']
    ];
}

// 👉 formato que Select2 espera
echo json_encode([
    "results" => $resultados
]);