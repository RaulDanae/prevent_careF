<?php

    session_start();

    /* ===============================
        1. CONFIGURACIÓN Y CONEXIÓN
    ================================ */

    require_once __DIR__ . '/../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/config/database1.php';
    authorizeDataTable(['Adminis', 'Supervi']);

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

    // Condición fija
    $where[] = "t2.rfc <> '' AND t1.hora_evento IS NOT NULL";

    // Filtro por Evento
    if (!empty($id_evento)) {
        $where[] = "t1.id_evento = ?";
        $params[] = $id_evento;
    }

    $searchValue = $_POST['search']['value'] ?? '';

    if ($searchValue !== '') {
        $where[] = "(
            t2.colaborador LIKE ?
        )";

        $params[] = "%$searchValue%";

    }

    $whereSQL = '';

    if (!empty($where)) {
        $whereSQL = 'WHERE ' . implode(' AND ', $where);
    }

    /* ===============================
        7. TOTAL DE REGISTROS (SIN FILTRO)
    ================================ */
    $sqlTotal = "SELECT COUNT(DISTINCT t2.id_paciente) FROM paciente_evento t2 WHERE t2.id_evento = ?";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute([$id_evento]);
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    /* ===============================
        8. TOTAL DE REGISTROS FILTRADOS
    ================================ */    
    $sqlFiltered = "SELECT COUNT(*) FROM paciente_evento t1 
                    INNER JOIN pacientes t2 ON t1.id_paciente = t2.id
                    $whereSQL";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
    SELECT 
        t1.id_paciente_evento, 
        t2.colaborador,
        t3.nomcom, 
        t4.nombre_sucursal,
        t1.usuario
    FROM `paciente_evento` t1
    INNER JOIN pacientes t2 ON t1.id_paciente = t2.id
    INNER JOIN compania t3 ON t2.cod_comp = t3.id_comp
    INNER JOIN sucursal t4 ON t2.id_sucursal = t4.id_sucursal
    $whereSQL
    ORDER BY t1.id_paciente_evento DESC
    LIMIT $start, $length
    ";

    $stmt = $conn->prepare($sqlData);
    $stmt->execute($params);

    /* ===============================
        10. ARMADO DE DATA
    ================================ */

    $data = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = $fila['id_paciente_evento'];
        unset($fila['id_paciente_evento']);

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