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
    SELECT  t1.id_paciente_evento, 
            t2.colaborador, 
            t2.fec_nac, 
            IFNULL(TIMESTAMPDIFF(YEAR, t2.fec_nac, CURDATE()), '') AS edad, 
            t2.genero,
            (t5.nombre) AS Recipiente,
            NOW() AS fecha_hora
    FROM `paciente_evento` t1 
    INNER JOIN pacientes t2 ON t1.id_paciente = t2.id
    INNER JOIN resultados_estudios t3 ON t1.id_paciente_evento = t3.codigo_barra
    INNER JOIN estudio_config t4 ON t3.id_estudio = t4.id_estudio
    INNER JOIN trecipientes t5 ON t4.id_recipiente = t5.id
    WHERE t1.id_paciente_evento = ? AND t5.nombre <> 'NO APLICA'
    GROUP BY t1.id_paciente_evento, t2.colaborador, t2.fec_nac, t2.genero, t5.id;
");

$stmt->execute([$id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];

foreach ($rows as $p) {

    $codigo = 'EV' . str_pad($p['id_paciente_evento'], 10, "0", STR_PAD_LEFT);

    $data[] = [
        'id'             => $codigo,
        'nombre'         => strtoupper($p['colaborador'] ?? ''),
        'edad'           => $p['edad'],
        'genero'         => $p['genero'],
        'recipiente'     => $p['Recipiente'],
        'fechaN'         => !empty($p['fec_nac']) 
                            ? date('d/m/Y', strtotime($p['fec_nac'])) 
                            : '',
        'fechaE' => date('d/m/Y', strtotime($p['fecha_hora'])),
        'horaE'  => date('H:i', strtotime($p['fecha_hora']))
    ];
};

echo json_encode($data);