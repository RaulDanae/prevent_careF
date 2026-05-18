<?php

    session_start();

    /* ===============================
        1. CONFIGURACIÓN Y CONEXIÓN
    ================================ */

    require_once __DIR__ . '/../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/config/database1.php';
    authorizeDataTable(['Adminis', 'Supervi', 'Relajacion']);

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
    $where[] = "t1.rfc <> '' AND t2.hora_evento IS NOT NULL";

    // Filtro por Evneto
    if (!empty($id_evento)) {
        $where[] = "t2.id_evento = ?";
        $params[] = $id_evento;
    }

    // Filtro de busqueda
    $searchValue = $_POST['search']['value'] ?? '';

    if ($searchValue !== '') {
        $where[] = "(
            t1.colaborador LIKE ? OR
            t2.id_paciente_evento LIKE ?
        )";

        $params[] = "%$searchValue%";
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
    $sqlFiltered = "SELECT COUNT(*) FROM pacientes t1 
                    LEFT JOIN paciente_evento t2 ON t1.id = t2.id_paciente
                    $whereSQL";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
    SELECT
        t2.id_paciente_evento,
        t1.colaborador,
        t1.genero,
        t1.fec_nac,
        t3.acudiorel,
        t3.obs_relax,
        t3.f_creacion,
        t3.f_modifica,
        t3.usrelax
    FROM pacientes t1
    LEFT JOIN paciente_evento t2 ON t1.id = t2.id_paciente
    LEFT JOIN trelax t3 ON t2.id_paciente_evento = t3.id_paciente_evento
    $whereSQL
    ORDER BY t2.id_paciente_evento DESC
    LIMIT $start, $length
    ";

    $stmt = $conn->prepare($sqlData);
    $stmt->execute($params);

    /* ===============================
        10. ARMADO DE DATA
    ================================ */

    $data = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id_paciente_evento = $fila['id_paciente_evento'];
        unset($fila['id_paciente_evento']);

        $btnEditar = '';
        $btnEditar = '<button class="btn btn-warning btn-sm btnEditar" data-idpacienteevento="'.$id_paciente_evento.'"><i class="fa fa-pencil"></i></button>';

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