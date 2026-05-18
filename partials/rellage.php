<?php

    session_start();

    /* ===============================
        1. CONFIGURACIÓN Y CONEXIÓN
    ================================ */

    require_once __DIR__ . '/../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/config/database1.php';
    authorizeDataTable(['Adminis', 'Supervi', 'Avisual', 'Comodin']);

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

    // Filtro por Evento
    if (!empty($id_evento)) {
        $where[] = "pe.id_evento = ?";
        $params[] = $id_evento;
    }

    // Filtro de busqueda
    $searchValue = $_POST['search']['value'] ?? '';

    // Condificones variables
    if ($searchValue !== '') {
        $where[] = "(
            p.colaborador LIKE ? OR
            pe.id_paciente_evento LIKE ?
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
    $sqlTotal = "SELECT COUNT(*) FROM paciente_evento WHERE id_evento = ?";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute([$id_evento]);
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    /* ===============================
        8. TOTAL DE REGISTROS FILTRADOS
    ================================ */    
    $sqlFiltered = "
        SELECT COUNT(*)
        FROM paciente_evento pe
        INNER JOIN pacientes p ON pe.id_paciente = p.id
        $whereSQL
    ";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
        SELECT
            pe.id_paciente_evento,
            p.colaborador,
            p.RFC,
            pe.aprivacidad,
            pe.cinformado,
            pe.programa_htm,
            pe.hora_toma_muestra,
            pe.programa_he,
            pe.hora_evento,
            pe.observaciones,
            pe.fecha_creacion,
            pe.fecha_modificacion,
            pe.usuario
        FROM paciente_evento pe
        INNER JOIN pacientes p ON pe.id_paciente = p.id
        $whereSQL
        ORDER BY pe.id_paciente_evento DESC
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
        $btnImprim = '';
        $btnLabel = '';
        $btnEditar = '<button class="btn btn-warning btn-sm btnEditar" data-idpacienteevento="'.$id_paciente_evento.'"><i class="fa fa-pencil"></i></button>';
        $btnImprim = '<button class="btn btn-success btn-sm btnPrint" data-idpacienteevento="'.$id_paciente_evento.'"><i class="fa fa-barcode"></i></button>';
        $btnLabel =  '<button class="btn btn-success btn-sm btnLabel" data-idpacienteevento="'.$id_paciente_evento.'"><i class="fa fa-note-sticky"></i></button>';

        foreach ($fila as &$valor) {
            if (is_string($valor)) {
                $valor = preg_replace('/\s+/', ' ', trim($valor));
            }
        }
        unset($valor);


        $data[] = array_merge([$btnEditar, $btnImprim, $btnLabel], array_values($fila));

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