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

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        $required = [
            'compa', 'tevento', 'fevento'
        ];

        foreach ($required as $f) {
            if (empty($in[$f])) {
                throw new Exception("El campo $f es obligatorio");
            }
        }

        // Revisar sucursales
        $sucursales = $_POST['sucursales'] ?? [];

        if (empty($sucursales)) {
            throw new Exception("Debe seleccionar almenos una sucursal");
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
            INSERT INTO eventos (
                nomevento, id_comp, tipo_evento, global, fecha_evento, nombre_corto, usuario
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt -> execute([
            $upper($in['descrip']),
            $in['compa'],
            $in['tevento'],
            $in['global'],
            validDate($in['fevento']),
            $upper($in['nomcorto']),
            $_SESSION['usuario']
        ]);

        // Obtener Id del evento
        $id_evento = $conn->lastInsertId();

        // Si el evento es global se guarda el total de sucursales que participan
        if ($_POST['global'] === 'SI') {

            // Ignoras seleccion y tomas TODAS las sucursales
            $stmt = $conn->prepare("SELECT id_sucursal FROM sucursal WHERE id_comp = ?");
            $stmt->execute([$in['compa']]);
            $sucursales = $stmt->fetchALL(PDO::FETCH_COLUMN);

        }

        // Insertar detalle
        foreach ($sucursales as $id_suc) {

            $stmt = $conn->prepare("
                INSERT INTO evento_sucursal (id_evento, id_sucursal)
                VALUES (?, ?)
            ");

            $stmt->execute([$id_evento, $id_suc]);

        }

        // ================= AUTO-INSERT PACIENTES =================

        // Insertar todos los pacientes de las sucursales del evento
        $stmtAuto = $conn->prepare("
            INSERT IGNORE INTO paciente_evento (id_paciente, id_evento, usuario)
            SELECT p.id, ?, ?
            FROM pacientes p
            WHERE p.id_sucursal IN (
                SELECT es.id_sucursal
                FROM evento_sucursal es
                WHERE es.id_evento = ?
            )
        ");

        $stmtAuto->execute([
            $id_evento,
            $_SESSION['usuario'],
            $id_evento
        ]); 
        
        
  // ==================== Seccion Perfiles =========================//
        $perfiles = $in['perfiles'] ?? [];

        if (empty($perfiles)) {
            throw new Exception("Debe seleccionar al menos un perfil");
        }

        foreach ($perfiles as $id_perfil) {

            if (!$id_perfil) continue;

            $stmt = $conn->prepare("
                INSERT IGNORE INTO evento_perfiles (id_evento, id_perfil)
                VALUES (?, ?)
            ");

            $stmt->execute([$id_evento, $id_perfil]);
        }

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

//        echo json_encode([
//            'success' => false,
//            'message' => $e->getMessage() // VER ERROR REAL
//        ]);
//        exit;

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