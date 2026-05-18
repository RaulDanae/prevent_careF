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

    function procesarCliente(PDO $conn, array $data, array &$resumen, string $fechafinal, string $usuario)
    {
        // Detectar si existe ya un registro
        $stmt = $conn->prepare("
            SELECT t1.id
            FROM pacientes t1
            WHERE t1.clave = ? AND cod_comp = ?
            LIMIT 1
        ");

        $stmt->execute([
            $data['clave'],
            $data['compania']
        ]);

        $compa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($compa) {
            actualizarCliente($conn, $data, $resumen, $fechafinal, $usuario);
        } else {
            guardarCliente($conn, $data, $resumen, $fechafinal, $usuario);
        }

        return ['ok' => true];

    }

    /* ============================
    GUARDAR (ALTA)
    ============================ */    

    function guardarCliente(PDO $conn, array $data, array &$resumen, string $fechafinal, string $usuario) 
    {
        $stmt = $conn->prepare("
            INSERT INTO pacientes (
                cod_comp, id_sucursal, clave, colaborador, 
                fec_nac, genero, curp, email, celular, activo, usregistro
            ) VALUES (
                ?,?,?,?,?,?,?,?,?,?,?
            )
        ");

        $stmt->execute([
            $data['compania'],
            $data['sucursal'],
            $data['clave'],
            $data['colaborador'],
            $fechafinal, // para fecha de nacimiento
            $data['genero'],
            $data['curp'],
            $data['email'],
            $data['celular'],
            $data['activo'],
            $usuario
        ]);

        $resumen['altas']++;

    }

    /* ============================
    ACTUALIZAR
    ============================ */

    function actualizarCliente(PDO $conn, array $data, array &$resumen, string $fechafinal, string $usuario)
    {

        $stmt = $conn -> prepare("
            UPDATE pacientes SET
                colaborador = ?, fec_nac = ?, genero = ?, curp = ?, email = ?, celular = ?, activo = ?, usregistro = ?
            WHERE clave = ? AND cod_comp = ?
        ");

        $stmt->execute([
            $data['colaborador'],
            $fechafinal,
            $data['genero'],
            $data['curp'],
            $data['email'],
            $data['celular'],
            $data['activo'],
            $usuario,
            $data['clave'],
            $data['compania']
        ]);

        $resumen['actualizados']++;
    }      
