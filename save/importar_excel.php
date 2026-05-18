<?php
    header('Content-Type: application/json; charset=utf-8');
    session_start();

    $usuario = $_SESSION['usuario'] ?? 'system';
    $usuarioLog = preg_replace('/[^a-zA-Z0-9_-]/', '_', $usuario);

    require_once "../config/database1.php";
    require_once "../vendor/autoload.php";
    require_once "../save/cliente_service.php";
    
    use PhpOffice\PhpSpreadsheet\IOFactory;

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
                strtoupper(trim((string)($f['A'] ?? ''))) === 'Compania' ||
                strtoupper(trim((string)($f['B'] ?? ''))) === 'Sucursal' ||
                strtoupper(trim((string)($f['D'] ?? ''))) === 'Colaborador' ||
                strtoupper(trim((string)($f['F'] ?? ''))) === 'Género' ||
                strtoupper(trim((string)($f['G'] ?? ''))) === 'CURP'
            ) {
                continue;
            }
            
            // Saltar filas completamente vacías
            if (
                trim((string)($f['A'] ?? '')) === '' &&
                trim((string)($f['B'] ?? '')) === '' &&
                trim((string)($f['D'] ?? '')) === '' &&
                trim((string)($f['F'] ?? '')) === '' &&
                trim((string)($f['G'] ?? '')) === ''
            ) {
                continue;
            }

            try {

                // -------- MAPEO DE COLUMNAS --------
                $data = [
                    'compania'      => strtoupper(trim((string)($f['A'] ?? ''))),
                    'sucursal'      => strtoupper(trim((string)($f['B'] ?? ''))),
                    'clave'         => trim((string)($f['C'] ?? '')),
                    'colaborador'   => strtoupper(trim((string)($f['D'] ?? ''))),
                    'fnacimiento'   => trim((string)($f['E'] ?? '')),
                    'genero'        => trim((string)($f['F'] ?? '')),
                    'curp'          => strtoupper(trim((string)($f['G'] ?? ''))),
                    'email'         => trim((string)($f['H'] ?? '')),
                    'celular'       => trim((string)($f['I'] ?? '')),
                    'activo'        => trim((string)($f['J'] ?? ''))
                ];

                // Validación mínima
                if (!$data['compania'] || !$data['sucursal'] || !$data['clave'] || !$data['colaborador'] || !$data['fnacimiento'] || !$data['genero'] || !$data['curp'] || !$data['email'] || !$data['celular'] || !$data['activo']) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Campos obligatorios vacios'
                    ];
                    continue;
                }

                // Consulta de compañias
                $stmt = $conn->prepare("
                    SELECT t1.id_comp
                    FROM compania t1
                    WHERE t1.compania = ?");

                $stmt->execute([
                    $data['compania']
                ]);

                // Consulta de sucursales
                $stmtsuc = $conn->prepare("
                    SELECT t1.id_sucursal
                    FROM sucursal t1
                    WHERE t1.nombre_sucursal = ?");

                $stmtsuc->execute([
                    $data['sucursal']
                ]);

                // Obtenemos resuesta de sucursal y compañia
                $rowComp = $stmt->fetch(PDO::FETCH_ASSOC);
                $rowSuc = $stmtsuc->fetch(PDO::FETCH_ASSOC);

                $activotxt = $data['activo'];

                $activotxt = strtoupper(trim($data['activo']));

                // quitar acentos
                $activotxt = str_replace(['Á','É','Í','Ó','Ú'], ['A','E','I','O','U'], $activotxt);

                // quitar espacios invisibles
                $activotxt = preg_replace('/\s+/', '', $activotxt);

                // Convertir activo
                if (in_array($activotxt, ['SI', '1'])) {
                    $data['activo'] = 1;
                } elseif (in_array($activotxt, ['NO', '0'])) {
                    $data['activo'] = 0;
                } else {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Valor Activo invalido: ['.$data['activotxt'].']'
                    ];
                    continue;
                }

                // Verificamos si encontro compañia
                if (!$rowComp) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'La compañia no existe'
                    ];
                    continue;
                }

                // Verificamos si encontro sucursal
                if (!$rowSuc) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'La sucursal no existe'
                    ];
                    continue;
                }

                $data['compania'] = $rowComp['id_comp'];
                $data['sucursal'] = $rowSuc['id_sucursal'];

                // Validar fecha de nacimiento
                $meses = [
                    'enero'      => '01',
                    'febrero'    => '02',
                    'marzo'      => '03',
                    'abril'      => '04',
                    'mayo'       => '05',
                    'junio'      => '06',
                    'julio'      => '07',
                    'agosto'     => '08',
                    'septiembre' => '09',
                    'octubre'    => '10',
                    'noviembre'  => '11',
                    'diciembre'  => '12'
                ];

                // Normalizar
                $fnacimiento = mb_strtolower(trim($data['fnacimiento']), 'UTF-8');

                // Extraer partes
                preg_match('/(\d{1,2})\s+de\s+([a-zñ]+)\s+(\d{4})/', $fnacimiento, $m);

                if (!$m) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Formato de fecha de nacimiento inválido'
                    ];
                    continue;
                }

                $dia = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $mes = $meses[$m[2]] ?? null;
                $anio = $m[3];

                if (!$mes) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Mes de nacimiento no reconocido'
                    ];
                    continue;
                }

                // Resultado final
                $fechafinal = "$anio-$mes-$dia";

                if (!checkdate((int)$mes, (int)$dia, (int)$anio)) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'Fecha de nacimiento inválida'
                    ];
                    continue;
                }

                if (strtotime($fechafinal) > time()) {
                    $resumen['errores']++;
                    $logErrores[] = [
                        'fila' => $i,
                        'razon' => 'La fecha de nacimiento no puede ser futura'
                    ];
                    continue;
                }

                // SERVICIO 
                $resultado = procesarCliente($conn, $data, $resumen, $fechafinal, $usuario);

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