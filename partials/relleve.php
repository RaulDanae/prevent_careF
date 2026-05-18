<?php

    session_start();

    /* ===============================
        1. CONFIGURACIÓN Y CONEXIÓN
    ================================ */

    require_once __DIR__ . '/../config/config.php';
    require_once ROOT_PATH . '/config/database1.php';

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

    /* ===============================
        4. FILTROS BASE POR PERFIL
    ================================ */    
    $where = [];
    $params = [];

    /* ===============================
        5. FILTROS DE BÚSQUEDA
    ================================ */
    $searchValue       = $_POST['search']['value'] ?? '';

    if ($searchValue !== '') {
        $where[] = "(
            t2.nomcom LIKE ? OR 
            t1.fecha_evento LIKE ? OR
            t1.tipo_evento LIKE ? OR
            t1.nombre_corto LIKE ?
        )";

        for ($i = 0; $i < 4; $i++){
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
    $orderColumn = 't1.id_evento';
    $orderDir = 'DESC';

    /* ===============================
        7. TOTAL DE REGISTROS (SIN FILTRO)
    ================================ */
    $stmtTotal = $conn->query("SELECT COUNT(*) FROM eventos");
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    /* ===============================
        8. TOTAL DE REGISTROS FILTRADOS
    ================================ */    
    $sqlFiltered = "SELECT COUNT(*) FROM eventos t1 $whereSQL";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
    SELECT 
        t1.id_evento, 
        t1.nomevento,
        t2.nomcom, 
        t1.tipo_evento,
        t1.global, 
        t1.fecha_evento,
        t1.nombre_corto, 
        t1.f_creacion, 
        t1.f_modifica, 
        t1.usuario
    FROM `eventos` t1
    LEFT JOIN compania t2 on t1.id_comp = t2.id_comp
    $whereSQL
    ORDER BY t1.id_evento DESC
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

        $id = $fila['id_evento'];
        unset($fila['id_evento']);

        $btnEditar = '';
        $btnImprim = '';
        $btnEditar = '<button class="btn btn-warning btn-sm btnEditar" data-id="'.$id.'"><i class="fa fa-pencil"></i></button>';
        $btnImprim = '<button class="btn btn-success btn-sm btnPrint" data-id="'.$id.'"><i class="fa fa-print"></i></button>';

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