<?php
require_once __DIR__ . '/../config/database1.php';

$id = $_GET['id'] ?? null;
if (!$id) exit('ID no recibido');

$conn = conn();
$stmt = $conn->prepare("
    SELECT colaborador, id_reg, fec_nac, curp
    FROM pacientes
    WHERE id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) exit('Paciente no encontrado');

echo json_encode([
    'nombre' => strtoupper($p['colaborador']),
    'id' => $p['id_reg'],
    'fecha'  => date('d/m/Y', strtotime($p['fec_nac'])),
    'curp'   => strtoupper($p['curp'])
]);