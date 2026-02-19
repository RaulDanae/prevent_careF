<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    // Funciones utilitarias

    $upper = fn($v) => mb_strtoupper(trim($v), 'UTF-8');

    $lower = fn($v) => mb_strtolower(trim($v), 'UTF-8');

    try {

        // Validaciones basicas

        if (!isset($_SESSION['usuario'])) {
            throw new Exception('Sesión no válida');
        }

        $in = $_POST;

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        $required = [
            'nom', 'uss', 'perfil', 'estatus'
        ];

        foreach ($required as $f) {
            if (!isset($in[$f]) || $in[$f] === '') {
                throw new Exception("El campo $f es obligatorio");
            }
        }

        // La contraseña se obtiene sin sanitizar para poder generar el hash
        $contrasena_input = $in['pas1'] ?? '';
        $clave_hash = null;

        // Generar el Hash si se proporcionó una contraseña
        if (!empty($contrasena_input)) {

            // Validar que los 2 campos de contraseña coinciden
            if ($contrasena_input !== ($in['pas2'] ?? '')) {
                throw new Exception('Las contraseñas no coinciden');
            } else {
                // Generamos el hash para el campo 'hash' de la instruccion SQL
                $clave_hash = password_hash($contrasena_input, PASSWORD_DEFAULT);
            }

        } else {
            throw new Exception("Error: Contraseña no proporcionada.");
        }

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        // INSERT PRINCIPAL
        $stmt = $conn -> prepare("
            INSERT INTO staff (
                nombre, usuario, password, perfil, estatus, fec_reg
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");


        $stmt -> execute([
            $upper($in['nom']),
            $upper($in['uss']),
            $clave_hash,
            $in['perfil'],
            $in['estatus']
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

            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => $msg
            ]);
            exit;
        }

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar la informacion'
        ]);

   } catch (Exception $e) {

        if (isset($conn) && $conn -> inTransaction()) {
            $conn->rollBack();
        } 

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
   }


?>