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

        file_put_contents('debug_agenda.txt', print_r($_POST, true));
        if (empty($in['id'])) {
            throw new Exception('Id del evento no recibido');
        }

        $idpaeven = trim($in['id']);

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        $required = [
            'rfc'
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
            UPDATE paciente_evento SET
                aprivacidad = ?,
                cinformado = ?,
                programa_htm = ?,
                hora_toma_muestra = ?,
                programa_he = ?,
                hora_evento = ?,
                observaciones = ?,
                usuario = ?
            WHERE id_paciente_evento = ?
        ");

        $stmt -> execute([
            $in['privacidad'],
            $in['consentimiento'],
            n($in['agehmuestra']),
            n($in['hmuestra']),
            n($in['agehevento']),
            n($in['hevento']),
            $in['obs'] ?? null,
            $_SESSION['usuario'],
            $idpaeven
        ]);

        $stmtp = $conn->prepare("
            UPDATE pacientes p
            INNER JOIN paciente_evento pe ON p.id = pe.id_paciente
            SET p.rfc = ?
            WHERE pe.id_paciente_evento = ?
        ");

        $stmtp->execute([
            $in['rfc'],
            $idpaeven
        ]);

// ================================== GENERAR ESTUDIOS SOLO SI ASISTE ==========================================

        // Solo si hay hora_evento o hora_toma_muestra (asistencia confirmada)

        // Estudios
        if (!empty($in['hmuestra'])) {

            $stmtEstudios = $conn->prepare("
                INSERT IGNORE INTO resultados_estudios (
                    codigo_barra,
                    id_estudio,
                    usuario
                )
                SELECT DISTINCT
                    pe.id_paciente_evento,
                    epf.id_estudio,
                    ?
                FROM paciente_evento pe
                INNER JOIN evento_perfiles evp 
                    ON pe.id_evento = evp.id_evento
                INNER JOIN estudio_perfil epf 
                    ON evp.id_perfil = epf.id_perfil
                WHERE pe.id_paciente_evento = ?
            ");

            $stmtEstudios->execute([
                $_SESSION['usuario'],
                $idpaeven
            ]);

            // Perfiles
            $stmtPerfiles = $conn->prepare("
                INSERT IGNORE INTO paciente_perfiles (
                    id_paciente_evento,
                    id_perfil,
                    usuario
                )
                SELECT DISTINCT
                    pe.id_paciente_evento,
                    evp.id_perfil,
                    ?
                FROM paciente_evento pe
                INNER JOIN evento_perfiles evp
                    ON pe.id_evento = evp.id_evento
                WHERE pe.id_paciente_evento = ?
            ");

            $stmtPerfiles->execute([
                $_SESSION['usuario'],
                $idpaeven
            ]);

        }

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

//        http_response_code(500);
//        echo json_encode([
//            'success' => false,
//            'message' => $e->getMessage(),
//            'line' => $e->getLine(),
//            'file' => $e->getFile()
//        ]);

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