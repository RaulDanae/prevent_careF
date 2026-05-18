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

        file_put_contents('debug_evento.txt', print_r($_POST, true));
        if (empty($in['idpaeven'])) {
            throw new Exception('Id del evento no recibido');
        }

        $idpaeven = trim($in['idpaeven']);

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        $stmt = $conn -> prepare("
            INSERT INTO tauditivo (
                id_paciente_evento, od_500, od_1000, od_2000, od_4000, oi_500, oi_1000, oi_2000, oi_4000, consultaaud, obs_aud, usaud      
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
                od_500 = VALUES(od_500),
                od_1000 = VALUES(od_1000),
                od_2000 = VALUES(od_2000),
                od_4000 = VALUES(od_4000),
                oi_500 = VALUES(oi_500),
                oi_1000 = VALUES(oi_1000),
                oi_2000 = VALUES(oi_2000),
                oi_4000 = VALUES(oi_4000),
                consultaaud = VALUES(consultaaud),
                obs_aud = VALUES(obs_aud),
                usaud = VALUES(usaud)
        ");

        $stmt -> execute([
            $idpaeven,
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