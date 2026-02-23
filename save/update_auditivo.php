<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    
    try {

        // Validaciones basicas

        if (!isset($_SESSION['usuario'])) {
            throw new Exception('Sesión no válida');
        }

        // PAra cuando lleguen valores '' a caja de texto numerico se convierta en null
        function n($v) {
            return ($v === '' || $v === null) ? null : $v;
        }

        $in = $_POST;

        file_put_contents('debug_curp.txt', print_r($_POST, true));
        if (empty($in['curp'])) {
            throw new Exception('CURP del registro no recibido');
        }

        $curp = trim($in['curp']);

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        $stmt = $conn -> prepare("
            INSERT INTO tauditivo (
                curp, od_500, od_1000, od_2000, od_4000, oi_500, oi_1000, oi_2000, oi_4000, consultaaud, obs_aud, faud, haud, usaud      
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) AS new
            ON DUPLICATE KEY UPDATE
                od_500 = new.od_500,
                od_1000 = new.od_1000,
                od_2000 = new.od_2000,
                od_4000 = new.od_4000,
                oi_500 = new.oi_500,
                oi_1000 = new.oi_1000,
                oi_2000 = new.oi_2000,
                oi_4000 = new.oi_4000,
                consultaaud = new.consultaaud,
                obs_aud = new.obs_aud,
                faud = new.faud,
                haud = new.haud,
                usaud = new.usaud
        ");

        $stmt -> execute([
            $curp,
            n($in['od_500']),
            n($in['od_1000']),
            n($in['od_2000']),
            n($in['od_4000']),
            n($in['oi_500']),
            n($in['oi_1000']),
            n($in['oi_2000']),
            n($in['oi_4000']),
            $in['consulta'],
            $in['observaciones'] ?? null,
            date('Y-m-d'),
            date('H:i:s'),
            $_SESSION['usuario']
        ]);

        // COMMIT

        //  Confirma cambios
        $conn -> commit();

        echo json_encode([
            'success' => true
        ]);

    } catch (PDOException $e) {

//        die($e->getMessage());

        // Si hay transaccion activa, deshacer cambios
        if (isset($conn) && $conn -> inTransaction()) {
            $conn -> rollBack();
        }

        // Error de clave duplicada
        if($e -> getCode() == 23000) {

            $msg = 'Registro duplicado';

            http_response_code(409); // Conflicto
            echo json_encode([
                'success' => false,
                'message' => $msg
            ]);
            exit;
        }

        // Otro error en BD
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la informacion'
        ]);

    } catch (Exception $e) {

        if (isset($conn) && $conn -> inTransaction()) {
            $conn->rollBack();
        }
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e -> getMessage()
        ]);
    }