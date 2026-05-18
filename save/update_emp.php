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

        $stmt = $conn -> prepare("
            UPDATE compania SET
                compania = ?, nomcom = ?, direccion_emp = ?, razon_social_emp = ?, rfc_emp = ?, telefono_emp = ?,
                nombre_contacto = ?, genero_contacto = ?, telefono_contacto = ?, mail_contacto = ?,
                usuario = ?
            WHERE id_comp = ?
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
            $_SESSION['usuario'],
            $id
        ]);

        if (!empty($in['suc_nombre']) && is_array($in['suc_nombre'])) {

            $ids = $in['suc_id'];
            $nombres = $in['suc_nombre'];

            // 1. Obtener sucursales actuales
            $stmt = $conn->prepare("
                SELECT id_sucursal FROM sucursal WHERE id_comp = ?
            ");
            $stmt->execute([$id]);
            $existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $idsProcesados = [];

            // 2. INSERAR nuevas sucursales
            $stmtInsert = $conn->prepare("
                INSERT INTO sucursal (id_comp, nombre_sucursal, usuario)
                VALUES (?, ?, ?)
            ");

            $stmtUpdate = $conn->prepare("
                UPDATE sucursal
                SET nombre_sucursal = ?, usuario = ?
                WHERE id_sucursal = ?
            ");

            foreach ($nombres as $i => $nombre) {

                $nombre = mb_strtoupper(trim($nombre), 'UTF-8');
                if ($nombre === '') continue;

                $idSucursal = $ids[$i];

                if (!empty($idSucursal)) {
                    // UPDATE
                    $stmtUpdate->execute([
                        $nombre,
                        $_SESSION['usuario'],
                        $idSucursal
                    ]);

                    $idsProcesados[] = $idSucursal;

                } else {

                    // INSERT
                    $stmtInsert->execute([
                        $id,
                        $nombre,
                        $_SESSION['usuario']
                    ]);

                    $idsProcesados[] = $conn->lastInsertId();

                }
            }

            // 3. Eliminar las que ya no vienen
            $paraEliminar = array_diff($existentes, $idsProcesados);

            if (!empty($paraEliminar)) {

                // Valida relaciones
                $stmtCheck = $conn->prepare("
                    SELECT COUNT(*) FROM evento_sucursal WHERE id_sucursal = ?
                ");

                $stmtDelete = $conn->prepare("
                    DELETE FROM sucursal WHERE id_sucursal = ?
                ");

                foreach ($paraEliminar as $idSucursal) {

                    // Validar si tiene eventos
                    $stmtCheck->execute([$idSucursal]);
                    $tieneEventos = $stmtCheck->fetchColumn();

                    if ($tieneEventos == 0) {
                        // Se puede eliminar
                        $stmtDelete->execute([$idSucursal]);
                    } else {
                        throw new Exception("No puedes eliminar sucursales con eventos");
                    }
                }
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

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;

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