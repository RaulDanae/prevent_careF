<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database1.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = conn();

$id_evento = $_SESSION['id_evento'] ?? '';

$sql = "
    SELECT
        pe.id_paciente_evento,
        p.colaborador,
        pe.programa_htm,
        pe.programa_he,
        pe.observaciones
    FROM paciente_evento pe
    INNER JOIN pacientes p ON pe.id_paciente = p.id
    WHERE pe.id_evento = ?
    ORDER BY pe.id_paciente_evento DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute([$id_evento]);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'ID',
    'Colaborador',
    'Programa HTM',
    'Programa HE',
    'Observaciones',
];

$col = 'A';

foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

$filaExcel = 2;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Para pintar agenda Toma Muestra
    $sheet->getStyle('C2:C'.$filaExcel)->applyFromArray([
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC']
        ]
    ]);

    // PAra pinta agenda Evento
    $sheet->getStyle('D2:D'.$filaExcel)->applyFromArray([
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC']
        ]
    ]);

    $sheet->setCellValue('A'.$filaExcel, $row['id_paciente_evento']);
    $sheet->setCellValue('B'.$filaExcel, $row['colaborador']);
    $sheet->setCellValue('F'.$filaExcel, $row['programa_htm']);
    $sheet->setCellValue('H'.$filaExcel, $row['programa_he']);
    $sheet->setCellValue('J'.$filaExcel, $row['observaciones']);

    $filaExcel++;
}

$writer = new Xlsx($spreadsheet);

$filename = 'agenda.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;