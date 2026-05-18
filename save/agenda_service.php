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

    if (!function_exists('convertirFechaExcel')) {
        function convertirFEchaExcel($valor)
        {
            try {

                if ($valor === null || trim((string)$valor) === '') {
                    return null;
                }

                // Si viene serial Excel
                if (is_numeric($valor)) {

                    $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);

                    return $fecha->format('Y-m-d H:i:s');
                }

                $valor = trim((string)$valor);

                $formatos = [
                    'm/d/Y H:i',
                    'm/d/Y H:i:s',
                    'd/m/Y H:i',
                    'd/m/Y H:i:s',
                    'Y-m-d H:i:s',
                    'Y-m-d H:i'
                ];

                foreach ($formatos as $formato) {

                    $fecha = DateTime::createFromFormat($formato, $valor);

                    if ($fecha instanceof DateTime) {

                        return $fecha->format('Y-m-d H:i:s');
                    }
                }

                return false;

            } catch (Throwable $e) {

                return false;
            }
        }
    }

    
    /* ============================
    FUNCIÓN PRINCIPAL DE ENTRADA
    ============================ */

    function procesarAgenda(PDO $conn, array $data, array &$resumen, string $usuario)
    {
        // Detectar si existe ya un registro
        $stmt = $conn->prepare("
            SELECT t1.id_paciente_evento
            FROM paciente_evento t1
            WHERE t1.id_paciente_evento = ?
            LIMIT 1
        ");

        $stmt->execute([
            $data['id_paciente_evento']
        ]);

        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si NO existe -> error
        if (!$registro) {

            $resumen['errores']++;

            return [
                'ok' => false,
                'error' => 'No se encontró el registro para actualizar'
            ];
        }

        // Actualizar
        actualizarAgenda($conn, $data, $resumen, $usuario);

        return [
            'ok' => true
        ];
    }

    /* ============================
    ACTUALIZAR
    ============================ */

    function actualizarAgenda(PDO $conn, array $data, array &$resumen, string $usuario)
    {

        $stmt = $conn -> prepare("
            UPDATE paciente_evento SET
                programa_htm = ?, programa_he = ?, usuario = ?
            WHERE id_paciente_evento = ?
        ");

        $stmt->execute([
            $data['programa_htm'],
            $data['programa_he'],
            $usuario,
            $data['id_paciente_evento'],

        ]);

        $resumen['actualizados']++;
    }      
