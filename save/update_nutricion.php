<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    
    try {

        // Validaciones basicas

        if (!isset($_SESSION['usuario'])) {
            throw new Exception('Sesión no válida');
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
            INSERT INTO tnutricional (
                curp, acudiosn, obs_nutr, fnutr, hnutr, usnutr        
                ) VALUES (?,?,?,?,?,?) AS new
            ON DUPLICATE KEY UPDATE
                acudiosn = new.acudiosn,
                obs_nutr = new.obs_nutr,
                fnutr = new.fnutr,
                hnutr = new.hnutr,
                usnutr = new.usnutr 
        ");

        $stmt -> execute([
            $curp,
            $in['asistencia'],
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

?>
