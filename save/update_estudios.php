<?php

/// Para evitar bugs lo que haremos es eliminar sub registros y volver a agregarlos

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database1.php';

$conn = conn();
$data = json_decode(file_get_contents("php://input"), true);

$conn->beginTransaction();

try {

    if (empty($data['id_estudio'])) {
        throw new Exception("ID de estudio requerido");
    }

    $estudio_id = $data['id_estudio'];

    // 🔹 1. ACTUALIZAR ESTUDIO
    $sql = "UPDATE estudios 
            SET clave = ?, nombre = ?, usuario = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $data['clave'],
        $data['nombre'],
        $data['usuario'],
        $estudio_id
    ]);

    // 🔹 2. ELIMINAR PERFILES ANTERIORES
    $conn->prepare("DELETE FROM estudio_perfil WHERE id_estudio = ?")
         ->execute([$estudio_id]);

    // 🔹 3. INSERTAR PERFILES NUEVOS
    $perfiles = $data['perfiles'] ?? [];

    if (empty($perfiles)) {
        throw new Exception("Debes seleccionar al menos un perfil");
    }

    foreach ($perfiles as $perfil) {
        $stmt = $conn->prepare("
            INSERT INTO estudio_perfil (id_estudio, id_perfil)
            VALUES (?, ?)
        ");
        $stmt->execute([$estudio_id, $perfil]);
    }

    // 🔹 4. ELIMINAR CONFIGURACIONES Y RANGOS
    // (si tienes FK con CASCADE, solo borras config)
    $configs = $conn->prepare("SELECT id FROM estudio_config WHERE id_estudio = ?");
    $configs->execute([$estudio_id]);

    $ids = $configs->fetchAll(PDO::FETCH_COLUMN);

    if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));

        $conn->prepare("DELETE FROM rango_estudio WHERE id_estudio_config IN ($in)")
             ->execute($ids);
    }

    $conn->prepare("DELETE FROM estudio_config WHERE id_estudio = ?")
         ->execute([$estudio_id]);

    // 🔹 5. INSERTAR CONFIGURACIONES NUEVAS
    $orden_config = 1;

    foreach ($data['configuraciones'] as $config) {

        if (
            empty($config['metodologia']) &&
            empty($config['unidad']) &&
            empty($config['muestra']) &&
            empty($config['recipiente'])
        ) {
            continue;
        }

        $stmt = $conn->prepare("
            INSERT INTO estudio_config 
            (id_estudio, id_metodologia, id_unidad, id_muestra, id_recipiente, orden_configuracion)
            VALUES (?,?,?,?,?,?)
        ");

        $stmt->execute([
            $estudio_id,
            $config['metodologia'],
            $config['unidad'],
            $config['muestra'],
            $config['recipiente'],
            $orden_config
        ]);

        $config_id = $conn->lastInsertId();

        // 🔹 6. INSERTAR RANGOS
        $orden_rango = 1;

        foreach ($config['rangos'] as $r) {

            if (
                empty($r['genero']) ||
                empty($r['edad_min']) ||
                empty($r['edad_max'])
            ) {
                continue;
            }

            $stmt = $conn->prepare("
                INSERT INTO rango_estudio
                (id_estudio_config, genero, edad_min, edad_max, valor_bajo, limite_inf, limite_sup, valor_alto, valor_critico, orden_rango)
                VALUES (?,?,?,?,?,?,?,?,?,?)
            ");

            $stmt->execute([
                $config_id,
                $r['genero'],
                $r['edad_min'],
                $r['edad_max'],
                $r['valor_bajo'],
                $r['lim_inf'],
                $r['lim_sup'],
                $r['valor_alto'],
                $r['valor_critico'],
                $orden_rango
            ]);

            $orden_rango++;
        }

        $orden_config++;
    }

    $conn->commit();

    echo json_encode(["success" => true]);

} catch (Exception $e) {

    $conn->rollBack();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}