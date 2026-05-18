<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database1.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['error' => 'ID invalido']);
    exit;    
} 

$conn = conn();

$stmt = $conn->prepare("
    SELECT t1.id_paciente_evento, t2.colaborador, t2.fec_nac, t2.curp
    FROM `paciente_evento` t1 
    LEFT JOIN pacientes t2 ON t1.id_paciente = t2.id
    WHERE t1.id_paciente_evento = ?
");

$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    echo json_encode(['error' => 'Paciente no encontrado']);
    exit;    
}

$codigo = 'EV' . str_pad($p['id_paciente_evento'], 10, "0", STR_PAD_LEFT);

$fecha = !empty($p['fec_nac'])
    ? date('d/m/Y', strtotime($p['fec_nac']))
    : '';

echo json_encode([
    'id'     => $codigo,
    'nombre' => strtoupper($p['colaborador'] ?? ''),
    'fecha'  => $fecha,
    'curp'   => strtoupper($p['curp'] ?? '')
]);