<?php
    /**
     * cliente_service.php
     * Centraliza TODA la lógica de negocio de clientes
     */
    require_once __DIR__ . '/../vendor/autoload.php';

    if (!function_exists('nullIfEmpty')) {
        function nullIfEmpty($v) {
            return ($v === '' || $v === null) ? null : $v;
        }
    }

    if (!function_exists('validDate')) {
        function validDate($d) {
            if (!$d) return null;
            $dt = DateTime::createFromFormat('Y-m-d', $d);
            return ($dt && $dt->format('Y-m-d') === $d) ? $d : null;
        }
    }

    if (!function_exists('upper')) {
        function upper($v) {
            return mb_strtoupper(trim($v), 'UTF-8');
        }
    }

    function errorResultado(string $msg): array {
        return ['ok' => false, 'error' => $msg];
    }
    
    /* ============================
    FUNCIÓN PRINCIPAL DE ENTRADA
    ============================ */

    function procesarCliente(PDO $conn, array $data, array &$resumen, int $idcomp, string $idreg, string $fechafinal)
    {
        // Detectar si existe ya un registro
        $stmt = $conn->prepare("
            SELECT t1.id_reg
            FROM pacientes t1
            WHERE t1.id_reg = ?
            LIMIT 1
        ");

        $stmt->execute([
            $idreg
        ]);

        $compa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($compa) {
            actualizarCliente($conn, $data, $resumen, $idcomp, $idreg, $fechafinal);
        } else {
            guardarCliente($conn, $data, $resumen, $idcomp, $idreg, $fechafinal);
        }

        return ['ok' => true];

    }

    /* ============================
    GUARDAR (ALTA)
    ============================ */    

    function guardarCliente(PDO $conn, array $data, array &$resumen, int $idcomp, string $idreg, string $fechafinal) 
    {
        $stmt = $conn->prepare("
            INSERT INTO pacientes (
                id_reg, cod_comp, clave, colaborador, 
                fec_nac, genero, curp, email, celular
            ) VALUES (
                ?,?,?,?,?,?,?,?,?
            )
        ");

        $stmt->execute([
            $idreg,
            $idcomp,
            $data['clave'],
            $data['colaborador'],
            $fechafinal,
            $data['genero'],
            $data['curp'],
            $data['email'],
            $data['celular']
        ]);

        $resumen['altas']++;

    }

    /* ============================
    ACTUALIZAR
    ============================ */

    function actualizarCliente(PDO $conn, array $data, array &$resumen, int $idcomp, string $idreg, string $fechafinal)
    {

        $stmt = $conn -> prepare("
            UPDATE pacientes SET
                colaborador = ?, fec_nac = ?, genero = ?, curp = ?, email = ?, celular = ?
            WHERE id_reg = ? AND cod_comp = ? AND clave = ?
        ");

        $stmt->execute([
            $data['colaborador'],
            $fechafinal,
            $data['genero'],
            $data['curp'],
            $data['email'],
            $data['celular'],
            $idreg,
            $idcomp,
            $data['clave']
        ]);

        $resumen['actualizados']++;
    }      
