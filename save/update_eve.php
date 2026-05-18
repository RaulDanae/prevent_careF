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
            throw new Exception("Debe seleccionar al menos una sucursal");
        }

        // Establecer zona horaria
        date_default_timezone_set('America/Mexico_City');

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        // 1 Pbtener sucursales actuales
        $stmt = $conn->prepare("
            SELECT id_sucursal
            FROM evento_sucursal
            WHERE id_evento = ?
        ");

        $stmt->execute([$id]);
        $sucursalesAntes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Actualizar Evento

        $stmt = $conn -> prepare("
            UPDATE eventos SET
                nomevento = ?, id_comp = ?, tipo_evento = ?, global = ?, fecha_evento = ?, nombre_corto = ?, usuario = ?
            WHERE id_evento = ?
        ");

        $stmt -> execute([
            $upper($in['descrip']),
            $in['compa'],
            $in['tevento'],
            $in['global'],
            validDate($in['fevento']),
            $upper($in['nomcorto']),
            $_SESSION['usuario'],
            $id
        ]);

        // 3 Si es GLOBAL -> Todas las sucursales
        if ($in['global'] === 'SI') {

            $stmt = $conn->prepare("
                SELECT id_sucursal
                FROM sucursal
                WHERE id_comp = ?
            ");

            $stmt->execute([$in['compa']]);
            $sucursales = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $sucursalesDespues = $sucursales;

        // 4 Detecta cambios
        $cambioSucursales = (
            array_diff($sucursalesAntes, $sucursalesDespues) ||
            array_diff($sucursalesDespues, $sucursalesAntes)
        );

        // Identificar si hay sucursales que ya tengan pacientes atendidos
        $stmt = $conn->prepare("
            SELECT DISTINCT p.id_sucursal
            FROM paciente_evento pe
            INNER JOIN pacientes p ON pe.id_paciente = p.id
            WHERE pe.id_evento = ?
            AND (
                pe.hora_evento IS NOT NULL
                OR pe.hora_toma_muestra IS NOT NULL
            )
        ");

        $stmt->execute([$id]);
        $sucursalesBloqueadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $sucursalesAntes = array_map('strval', $sucursalesAntes);
        $sucursalesDespues = array_map('strval', $sucursalesDespues);
        $sucursalesBloqueadas = array_map('strval', $sucursalesBloqueadas);

        $insertarSuc = array_diff($sucursalesDespues, $sucursalesAntes);
        $eliminarSuc = array_diff($sucursalesAntes, $sucursalesDespues);

        $intentoEliminar = $eliminarSuc;

        $bloqueadasIntento = array_intersect($intentoEliminar, $sucursalesBloqueadas);

        if (!empty($bloqueadasIntento)) {

            // Convertir IDs a nombres
            $placeholders = implode(',', array_fill(0, count($bloqueadasIntento), '?'));

            $stmt = $conn->prepare("
                SELECT nombre_sucursal 
                FROM sucursal 
                WHERE id_sucursal IN ($placeholders)
            ");

            $stmt->execute(array_values($bloqueadasIntento));

            $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);

            throw new Exception(
                "No puedes eliminar sucursales con pacientes atendidos: " . implode(', ', $nombres)
            );
        }

        $noEliminadasSuc = [];

        $stmtDelSuc = $conn->prepare("
            DELETE FROM evento_sucursal
            WHERE id_evento = ? AND id_sucursal = ?
        ");

        $stmtDelPac = $conn->prepare("
            DELETE pe 
            FROM paciente_evento pe
            INNER JOIN pacientes p ON pe.id_paciente = p.id
            WHERE pe.id_evento = ? 
            AND p.id_sucursal = ? 
            AND pe.hora_evento IS NULL 
            AND pe.hora_toma_muestra IS NULL
        ");

        foreach ($eliminarSuc as $id_suc) {

            if (in_array($id_suc, $sucursalesBloqueadas)) {
                $noEliminadasSuc[] = $id_suc;
                continue;
            }

            // 1. borrar pacientes del evento (sin atención)
            $stmtDelPac->execute([$id, $id_suc]);

            // 2. borrar relación sucursal-evento
            $stmtDelSuc->execute([$id, $id_suc]);
        }




        $stmtInsSuc = $conn->prepare("
            INSERT INTO evento_sucursal (id_evento, id_sucursal)
            VALUES (?, ?)
        ");

        foreach ($insertarSuc as $id_suc) {
            $stmtInsSuc->execute([$id, $id_suc]);
        }

// =================================== Perfiles ===================================== //
        $perfiles = $_POST['perfiles'] ?? [];

        if (!isset($_POST['perfiles'])) {
            $perfiles = $perfilesAntes; // mantener actuales
        }

        // Obtener perfiles Actuales
        $stmt = $conn->prepare("
            SELECT id_perfil
            FROM evento_perfiles
            WHERE id_evento = ?
        ");
        $stmt->execute([$id]);
        $perfilesAntes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Obtener los perfiles bloqueados
        $stmt = $conn->prepare("
            SELECT DISTINCT ep.id_perfil
            FROM evento_perfiles ep
            WHERE ep.id_evento = ?
                  AND EXISTS (
                              SELECT 1
                              FROM estudio_perfil esp
                              INNER JOIN resultados_estudios r ON r.id_estudio = esp.id_estudio
                              INNER JOIN paciente_evento pe ON pe.id_paciente_evento = r.codigo_barra
                              WHERE esp.id_perfil = ep.id_perfil
                              AND pe.id_evento = ?
                             )
        ");

        $stmt->execute([$id, $id]);
        $bloqueados = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Normalizar
        $perfilesAntes = array_map('strval', $perfilesAntes);
        $perfiles = array_map('strval', $perfiles);
        $bloqueados = array_map('strval', $bloqueados);

        // Calcular DIferencias
        $insertar = array_diff($perfiles, $perfilesAntes);
        $eliminar = array_diff($perfilesAntes, $perfiles);

        // Eliminar perfiles respetando bloqueados
        $stmtDel = $conn->prepare("
            DELETE FROM evento_perfiles
            WHERE id_evento = ? AND id_perfil = ?
        ");
        
        // Insertar nuevos
        $stmtInsPerfil = $conn->prepare("
            INSERT IGNORE INTO evento_perfiles (id_evento, id_perfil)
            VALUES (?, ?)
        ");

        // 1. Eliminar
        $noEliminados = [];

        foreach ($eliminar as $id_perfil) {
            if (in_array($id_perfil, $bloqueados)) {
                $noEliminados[] = $id_perfil;
                continue;
            }
            $stmtDel->execute([$id, $id_perfil]);
        }

        if (!empty($noEliminados)) {

            $placeholders = implode(',', array_fill(0, count($noEliminados), '?'));

            $stmt = $conn->prepare("
                SELECT nombre 
                FROM perfilestudios 
                WHERE id IN ($placeholders)
            ");

            $stmt->execute(array_values($noEliminados));

            $nombresPerfiles = $stmt->fetchAll(PDO::FETCH_COLUMN);

            throw new Exception(
                "No puedes eliminar perfiles con datos capturados: " . implode(', ', $nombresPerfiles)
            );
        }
        
        // 2. Insertar
        foreach ($insertar as $id_perfil) {
            $stmtInsPerfil->execute([$id, $id_perfil]);
        }



// ================================ GENERAR ESTUDIOS PARA PERFILES NUEVOS ==========================================

        if (!empty($insertar)) {

            $ids = implode(',', array_map('intval', $insertar));

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
                INNER JOIN estudio_perfil epf ON epf.id_perfil IN ($ids)
                WHERE pe.id_evento = ? AND EXISTS (
                                                    SELECT 1
                                                    FROM resultados_estudios r
                                                    WHERE r.codigo_barra = pe.id_paciente_evento
                                                  )
            ");

            $stmtEstudios->execute([
                $_SESSION['usuario'],
                $id
            ]);
        }


        // COMMIT
        //  Confirma cambios
        $conn -> commit();

        echo json_encode([
            'success' => true,
            'cambio_sucursales' => $cambioSucursales ? true : false,
            'perfiles_bloqueados' => $noEliminados,
            'sucursales_bloqueadas' => $noEliminadasSuc
        ]);

    } catch (PDOException $e) {

        // Si hay transaccion activa, deshacer cambios
        if (isset($conn) && $conn -> inTransaction()) {
            $conn -> rollBack();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage() // 🔥 VER ERROR REAL
        ]);
        exit;

        http_response_code(500);

        echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
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