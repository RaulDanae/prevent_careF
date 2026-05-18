<?php

    session_start();

    /* ===============================
        1. CONFIGURACIÓN Y CONEXIÓN
    ================================ */

    require_once __DIR__ . '/../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/config/database1.php';
    authorizeDataTable(['Adminis', 'Supervi', 'Laboratorio', 'Caplab']);

    $conn = conn(); // Es obligatorio

    if (!$conn) {
        http_response_code(500);
        exit('Error de conexión a la base de datos');
    }

    /* ===============================
        2. PARÁMETROS DE DATATABLES
    ================================ */    
    $draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
    $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;

    $start  = max(0, $start);
    $length = ($length > 0 && $length <= 100) ? $length : 10;

    /* ===============================
        3. SESIÓN / PERFIL
    ================================ */
    $perfil = $_SESSION['perfil'] ?? '';
    $nombre = $_SESSION['nombre'] ?? '';
    $usuario = $_SESSION['usuario'] ?? '';
    $id_evento = $_SESSION['id_evento'] ?? '';

    /* ===============================
        4. FILTROS BASE POR PERFIL
    ================================ */    
    $where = [];
    $params = [];

    /* ===============================
        5. FILTROS DE BÚSQUEDA
    ================================ */
    $searchValue       = $_POST['search']['value'] ?? '';

    // COndificones variables
    if ($searchValue !== '') {
        $where[] = "(
            t1.clave LIKE ? OR
            t1.nombre LIKE ? OR
            t4.nombre LIKE ?
        )";

        for ($i = 0; $i < 3; $i++){
            $params[] = "%$searchValue%";
        }
    }

    $whereSQL = '';

    if (!empty($where)) {
        $whereSQL = 'WHERE ' . implode(' AND ', $where);
    }


    /* ===============================
        6. ORDENAMIENTO
    ================================ */
    $orderColumn = 't1.id';
    $orderDir = 'DESC';

    /* ===============================
        7. TOTAL DE REGISTROS (SIN FILTRO)
    ================================ */
    $stmtTotal = $conn->query("SELECT COUNT(*) FROM estudios");
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    /* ===============================
        8. TOTAL DE REGISTROS FILTRADOS
    ================================ */    
    $sqlFiltered = "SELECT COUNT(*)
                    FROM estudios t1
                    LEFT JOIN estudio_perfil t2 ON t1.id = t2.id_estudio
                    LEFT JOIN estudio_config t3 ON t1.id = t3.id_estudio
                    LEFT JOIN perfilestudios t4 ON t2.id_perfil = t3.Id
                    LEFT JOIN rango_estudio t5 ON t3.id = t5.id_estudio_config
                    LEFT JOIN tunidades t6 ON t3.id_unidad = t6.id
                    LEFT JOIN trecipientes t7 ON t3.id_recipiente = t7.id
                    LEFT JOIN tmetodologias t8 ON t3.id_metodologia = t8.id
                    LEFT JOIN tipomuestras t9 ON t3.id_muestra = t9.id 
                    $whereSQL";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
    SELECT
        t1.id,
        t1.clave,
        t1.nombre,
        (t6.nombre) unidades,
        t1.f_creacion,
        t1.f_modifica,
        t1.usuario
    FROM estudios t1
    LEFT JOIN estudio_perfil t2 ON t1.id = t2.id_estudio
    LEFT JOIN estudio_config t3 ON t1.id = t3.id_estudio
    LEFT JOIN perfilestudios t4 ON t2.id_perfil = t4.id
    LEFT JOIN rango_estudio t5 ON t3.id = t5.id_estudio_config
    LEFT JOIN tunidades t6 ON t3.id_unidad = t6.id
    LEFT JOIN trecipientes t7 ON t3.id_recipiente = t7.id
    LEFT JOIN tmetodologias t8 ON t3.id_metodologia = t8.id
    LEFT JOIN tipomuestras t9 ON t3.id_muestra = t9.id 
    GROUP BY t1.id, t1.clave, t1.nombre, (t6.nombre), t1.f_creacion, t1.f_modifica, t1.usuario
    $whereSQL
    ORDER BY t1.id DESC
    LIMIT $start, $length
    ";

    $searchParams = [];

    if ($searchValue !== '') {
        for ($i = 0; $i < 10; $i++) {
            $searchParams[] = "%$searchValue%";
        }
    }

    $stmt = $conn->prepare($sqlData);
    $stmt->execute($params);
    $bindIndex = 1;

    /* ===============================
        10. ARMADO DE DATA
    ================================ */

    $data = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = $fila['id'];
        unset($fila['id']);

        $btnEditar = '';
        $btnEditar = '<button class="btn btn-warning btn-sm btnEditar" data-id="'.$id.'"><i class="fa fa-pencil"></i></button>';

        foreach ($fila as &$valor) {
            if (is_string($valor)) {
                $valor = preg_replace('/\s+/', ' ', trim($valor));
            }
        }
        unset($valor);


        $data[] = array_merge([$btnEditar], array_values($fila));

    }

    /* ===============================
        11. RESPUESTA JSON
    ================================ */
    echo json_encode([
        "draw"            => $draw,
        "recordsTotal"    => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data"            => $data
    ], JSON_UNESCAPED_UNICODE);