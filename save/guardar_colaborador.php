<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    require_once __DIR__ . '/../vendor/autoload.php';
    use Picqer\Barcode\BarcodeGeneratorPNG;

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

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        $required = [
            'cod_comp', 'clave', 'colaborador', 'fnacimiento', 'genero', 'curp', 'email', 'rfc',
            'edad', 'privacidad', 'consentimiento', 'fregistro', 'hregistro'
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

        // Id compuestos
        $claveform = str_pad($in['clave'], 5, '0', STR_PAD_LEFT);

        $id_compuesto = "{$in['cod_comp']}$claveform";

        // INSERT PRINCIPAL
        $stmt = $conn -> prepare("
            INSERT INTO pacientes (
                id_reg, cod_comp, clave, colaborador, fec_nac, genero, curp, email, celular, rfc, edad,
                aprivacidad, cinformado, hrtomamuestra, hrferia, obs_reg, fregistro, hregistro, usregistro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt -> execute([
            $id_compuesto,
            $in['cod_comp'],
            $in['clave'],
            $upper($in['colaborador']),
            validDate($in['fnacimiento']),
            $in['genero'],
            $upper($in['curp']),
            $lower($in['email']),
            $in['celular'],
            $upper($in['rfc']),
            $in['edad'],
            $in['privacidad'],
            $in['consentimiento'],
            nullIfEmpty($in['hrtomamuestra'] ?? null),
            nullIfEmpty($in['hrferia'] ?? null),
            nullIfEmpty($in['observaciones'] ?? null),
            validDate($in['fregistro']),
            $in['hregistro'],
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