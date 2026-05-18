<?php
    header('Content-Type: application/json; charset=utf-8');
    session_start();

    $usuario = $_SESSION['usuario'] ?? 'system';
    $usuarioLog = preg_replace('/[^a-zA-Z0-9_-]/', '_', $usuario);

    require_once "../config/database1.php";
    require_once "../vendor/autoload.php";
    require_once "../save/agenda_service.php";
    
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Shared\Date;

    /* ===============================
    VALIDACIONES INICIALES
    ================================ */

    if (!isset($_FILES['excel'])) {
        echo json_encode(['success'=>false,'message'=>'No se recibió archivo']);
        exit;
    }

    $archivo = $_FILES['excel'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['xls','xlsx'])) {
        echo json_encode(['success'=>false,'message'=>'Formato inválido']);
        exit;
    }
    
    /* ===============================
    GUARDAR ARCHIVO
    ================================ */

    $ruta = __DIR__.'/uploads/';
    if (!is_dir($ruta)) mkdir($ruta,0755,true);

    $nombreArchivo = 'carga_'.date('Ymd_His').'.'.$ext;
    $destino = $ruta.$nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        echo json_encode(['success'=>false,'message'=>'Error al guardar archivo']);
        exit;
    }

    /* ===============================
    CONEXIÓN
    ================================ */

    $conn = conn();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    date_default_timezone_set('America/Mexico_City');
    
    /* ===============================
    CONTADORES
    ================================ */

    $resumen = [
        'altas' => 0,
        'actualizados' => 0,
        'errores' => 0
    ];
    
    /* ===============================
    LEER EXCEL
    ================================ */

    try {
        $spreadsheet = IOFactory::load($destino);
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray(null,true,true,true);
    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>'Excel ilegible']);
        exit;
    }

    /* ===============================
    PROCESAR
    ================================ */

    $conn->beginTransaction();

    // Inicializar el log
    $ownersBloqueados = [];
    $logErrores = [];

    try {
        
        foreach ($filas as $i => $f) {

            // Saltar encabezados reales
            if (
                strtoupper(trim((string)($f['A'] ?? ''))) === 'ID' ||
                strtoupper(trim((string)($f['B'] ?? ''))) === 'Colaborador'
            ) {
                continue;
            }
            
            // Saltar filas completamente vacías
            if (
                trim((string)($f['A'] ?? '')) === '' &&
                trim((string)($f['B'] ?? '')) === ''
            ) {
                continue;
            }

            try {

                // -------- MAPEO DE COLUMNAS --------
                $data = [
                    'id_paciente_evento'      => trim((string)($f['A'] ?? '')),
                    'colaborador'             => strtoupper(trim((string)($f['B'] ?? ''))),
                    'programa_htm'            => trim((string)($f['C'] ?? '')),
                    'programa_he'             => trim((string)($f['D'] ?? '')),
                    'observaciones'           => trim((string)($f['E'] ?? ''))
                ];

                // Validación mínima
                if (!$data['id_paciente_evento'] || !$data['colaborador'] || !$data['programa_htm'] || !$data['programa_he']) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Campos obligatorios vacios'
                    ];
                    continue;
                }

                // Validar fechas DATETIME
                $programaHTM = convertirFechaExcel($data['programa_htm']);
                $programaHE  = convertirFechaExcel($data['programa_he']);

                if ($programaHTM === false) {

                    $resumen['errores']++;

                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Formato inválido en Programa HTM'
                    ];

                    continue;
                }

                if ($programaHE === false) {

                    $resumen['errores']++;

                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Formato inválido en Programa HE'
                    ];

                    continue;
                }

                // Reemplazar por formato MySQL
                $data['programa_htm'] = $programaHTM;
                $data['programa_he']  = $programaHE;

                // SERVICIO 
                $resultado = procesarAgenda($conn, $data, $resumen, $usuario);

                if (!is_array($resultado) || !isset($resultado['ok'])) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila'=>$i,
                        'razon'=> $resultado['error']
                    ];
                    continue;
                }                    
                
                if ($resultado['ok'] === false) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => $resultado['error'] ?? 'Error desconocido'
                    ];
                    continue;
                }                
                
            } catch (Throwable $e) {

                $resumen['errores']++;
                $logErrores[] = [
                    'fila' => $i,
                    'razon' => $e->getMessage()
                ];

                continue;
                
            }

        }       

        $conn->commit();   

        // Guardar log de errores en archivo
        if (!empty($logErrores)) {

            $baseDir = __DIR__ . '/logs';

            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0755, true);
            }

            $userDir = $baseDir . '/' . $usuarioLog;

            if (!is_dir($userDir)) {
                mkdir($userDir, 0755, true);
            }

            $archivoLog = $userDir . '/' . date('Y-m-d_H-i-s') . '.log';

            $contenido = "USUARIO: $usuario" . PHP_EOL;
            $contenido .= "FECHA : " . date('Y-m-d H:i:s') . PHP_EOL;
            $contenido .= str_repeat('=', 60) . PHP_EOL;

            foreach ($logErrores as $err) {
                $contenido .= "Fila {$err['fila']} | {$err['razon']}" . PHP_EOL;
            }

            file_put_contents($archivoLog, $contenido, LOCK_EX);

        }
        
    } catch (Exception $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        echo json_encode([
            'success' => false,
            'message' => 'Error en procesamiento',
            'detalle' => $e->getMessage()
        ]);
        exit;
    }

    //   RESPUESTA  //

    echo json_encode([
        'success' => true,
        'message' => 'Archivo procesado correctamente',
        'resumen' => $resumen,
        'log' => $logErrores
    ], JSON_UNESCAPED_UNICODE);

    exit;