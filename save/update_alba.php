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

        if (empty($in['id'])) {
            throw new Exception('ID del registro no recibido');
        }

        $id = (int)$in['id'];

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        if (!in_array($_SESSION['perfil'], ['Adminis', 'Supervi'])){

            $stmtCheck = $conn->prepare("SELECT usuario FROM staff WHERE id = ?");
            $stmtCheck->execute([$id]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            // Si no es admi/superfisor, solo puede editar su propio usuario');
            if (!$row || $row['usuario'] !== $_SESSION['usuario']) {
                throw new Exception('No tiene permiso para editar este usuario');
            }

            // Validar si se edito la contraseña
            if (empty($in['pas1'])) {
                throw new Exception("Debe ingresar una nueva contraseña");
            }

            if ($in['pas1'] !== $in['pas2']) {
                throw new Exception("Las contraseñas no coinciden");
            }

            $nuevoHash = password_hash($in['pas1'], PASSWORD_DEFAULT);

            // Y solo permitir cambio de password
            $stmt = $conn->prepare("
                UPDATE staff SET 
                    password = ?, fec_reg = NOW() 
                WHERE id = ?
            ");

                $stmt->execute([$nuevoHash, $id]);

        } else {

            // VALIDACIÓN DE CAMPOS OBLIGATORIOS
            $required = [
                'nom', 'uss', 'perfil', 'estatus'
            ];

            foreach ($required as $f) {
                if (!isset($in[$f]) || $in[$f] === '') {
                    throw new Exception("El campo $f es obligatorio");
                }
            }

            // Validar si se edito la contraseña
            if (!empty($in['pas1'])) {

                if ($in['pas1'] !== $in['pas2']) {
                    throw new Exception("Las contraseñas no coinciden");
                }

                $nuevoHash = password_hash($in['pas1'], PASSWORD_DEFAULT);

                $stmt = $conn->prepare("
                    UPDATE staff SET
                        nombre = ?, usuario = ?, password = ?, perfil = ?, estatus = ?, fec_reg = NOW()
                    WHERE id = ?
                ");

                $stmt -> execute([
                    $upper($in['nom']),
                    $upper($in['uss']),
                    $nuevoHash,
                    $in['perfil'],
                    $in['estatus'],
                    $in['id']
                ]);

            } else {

                $stmt = $conn->prepare("
                    UPDATE staff SET
                        nombre = ?, usuario = ?, perfil = ?, estatus = ?, fec_reg = NOW()
                    WHERE id = ?
                ");

                $stmt -> execute([
                    $upper($in['nom']),
                    $upper($in['uss']),
                    $in['perfil'],
                    $in['estatus'],
                    $in['id']
                ]);

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