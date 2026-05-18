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
            INSERT INTO trelax (
                id_paciente_evento, acudiorel, obs_relax, usrelax  
                ) VALUES (?,?,?,?)
            ON DUPLICATE KEY UPDATE
                acudiorel = VALUES(acudiorel),
                obs_relax = VALUES(obs_relax),
                usrelax = VALUES(usrelax) 

        ");

        $stmt -> execute([
            $idpaeven,
            $in['asistencia'],
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