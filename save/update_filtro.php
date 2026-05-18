<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    // Funciones utilitarias

    function nullIfEmpty($v) {
        return ($v === '' || $v === null) ? null : $v;
    }

    // Valida que las fechas sean correctas
    function validDate($d) {
        if (!$d) return null;
        $dt = DateTime::createFromFormat('Y-m-d', $d);
        return $dt && $dt->format('Y-m-d') === $d ? $d : null;
    }

    $upper = fn($v) => mb_strtoupper(trim($v), 'UTF-8');

    $lower = fn($v) => mb_strtolower(trim($v), 'UTF-8');
    
    try {

        // Validaciones basicas

        if (!isset($_SESSION['usuario'])) {
            throw new Exception('Sesión no válida');
        }

        $in = $_POST;

        if (empty($in['id'])) {
            throw new Exception('ID del registro no recibido');
        }

        $id = (int)$in['id'];

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        $required = [
            'evento'
        ];

        foreach ($required as $f) {
            if (empty($in[$f])) {
                throw new Exception("El campo $f es obligatorio");
            }
        }

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        $stmt = $conn -> prepare("
            UPDATE staff SET
                id_evento = ?
            WHERE id = ?
        ");

        $stmt -> execute([
            $upper($in['evento']),
            $id
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