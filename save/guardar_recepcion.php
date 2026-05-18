<?php


    require_once __DIR__ . '/../config/database1.php';

    header('Content-Type: application/json');
    session_start();

    try{

        $input = json_decode(
            file_get_contents("php://input"),
            true
        );

        if(!$input){
            throw new Exception("Datos inválidos");
        }

        $id_paev = $input['id_paciente_evento'];

        $perfiles_agregados =
            $input['perfiles_agregados'] ?? [];

        $perfiles_eliminados =
            $input['perfiles_eliminados'] ?? [];

        $estudios_agregados =
            $input['estudios_agregados'] ?? [];

        $estudios_eliminados =
            $input['estudios_eliminados'] ?? [];

        // ============================================
        // INICIAR TRANSACCION
        // ============================================

        $conn = conn();

        // ACTIVAR ERRORES PDO COMO EXCEPCIONES
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Iniciar transaccion
        $conn -> beginTransaction();

        // ==================================================
        // AGREGAR PERFILES
        // ==================================================

        foreach($perfiles_agregados as $id_perfil){

            $stmt = $conn->prepare("
                INSERT INTO paciente_perfiles
                (
                    id_paciente_evento,
                    id_perfil,
                    usuario
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $id_paev,
                $id_perfil,
                $_SESSION['usuario']]
            );

        }

        // ==================================================
        // ELIMINAR PERFILES
        // ==================================================

        foreach($perfiles_eliminados as $id_perfil){

            $stmt = $conn->prepare("
                DELETE FROM paciente_perfiles
                WHERE id_paciente_evento = ?
                AND id_perfil = ?
            ");

            $stmt->execute([
                $id_paev,
                $id_perfil]
            );

        }

        // ==================================================
        // AGREGAR ESTUDIOS
        // ==================================================

        foreach($estudios_agregados as $id_estudio){

            $stmt = $conn->prepare("
                INSERT INTO resultados_estudios
                (
                    codigo_barra,
                    id_estudio,
                    usuario
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $id_paev,
                $id_estudio,
                $_SESSION['usuario']]
            );

        }

        // ==================================================
        // ELIMINAR ESTUDIOS
        // ==================================================

        foreach($estudios_eliminados as $id_estudio){

            // Validar resultados
            $stmt = $conn->prepare("
                SELECT COUNT(*) total
                FROM resultados_estudios
                WHERE codigo_barra = ?
                AND id_estudio = ?
                AND resultado IS NOT NULL
            ");

            $stmt->execute([
                $id_paev,
                $id_estudio]
            );

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            // Tiene resultados -> NO eliminar
            if($res['total'] > 0){
                continue;
            }

            // Eliminar estudio
            $stmt = $conn->prepare("
                DELETE FROM resultados_estudios
                WHERE codigo_barra = ?
                AND id_estudio = ?
            ");

            $stmt->execute([
                $id_paev,
                $id_estudio]
            );

        }

        // ============================================
        // COMMIT
        // ============================================

        $conn->commit();

        echo json_encode([
            'success' => true
        ]);

    }catch(Exception $e){

        if(isset($conn) && $conn->inTransaction()){
            $conn->rollBack();
        }

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }