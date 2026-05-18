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
            'comp', 'rescomp', 'diremp', 'telemp', 'nomcon', 'gencon', 'telcon', 'mailcon'
        ];

        foreach ($required as $f) {
            if (empty($in[$f])) {
                throw new Exception("El campo $f es obligatorio");
            }
        }

        if (empty(array_filter($in['suc_nombre'] ?? []))) {
            throw new Exception("Debe agregar al menos una sucursal");
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
            INSERT INTO compania (
                compania, nomcom, direccion_emp, razon_social_emp, rfc_emp, telefono_emp,
                nombre_contacto, genero_contacto, telefono_contacto, mail_contacto,
                usuario
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt -> execute([
            $upper($in['comp']),
            $upper($in['rescomp']),
            nullIfEmpty($upper($in['diremp'])),
            nullIfEmpty($upper($in['razemp'])),
            nullIfEmpty($upper($in['rfcemp'])),
            $in['telemp'],
            $upper($in['nomcon']),
            $upper($in['gencon']),
            $in['telcon'],
            $upper($in['mailcon']),
            $_SESSION['usuario']
        ]);

        // Obtener Id de la compañia
        $id_comp = $conn->lastInsertId();

        if (!empty($in['suc_nombre']) && is_array($in['suc_nombre'])) {

            $nombres = array_unique($in['suc_nombre']);

            $stmtSuc = $conn->prepare("
                INSERT INTO sucursal (id_comp, nombre_sucursal, usuario)
                VALUES (?, ?, ?)
            ");

            $sucursalesValidas = 0;

            foreach ($nombres as $nombre) {

                $nombre = mb_strtoupper(trim($nombre), 'UTF-8');

                // Evitar vacios
                if ($nombre === '') continue;

                $stmtSuc->execute([
                    $id_comp,
                    $nombre,
                    $_SESSION['usuario']
                ]);

                $sucursalesValidas++;

            }

            // Validacion real (no solo array vacio)
            if ($sucursalesValidas === 0) {
                throw new Exception("Debe agregar almenos una sucursal valida");
            }

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
//            'message' => $e->getMessage() // 🔥 VER ERROR REAL
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