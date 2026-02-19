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

    if (!in_array($perfil, ['Adminis', 'Supervi'])) {

        $where[] = "t1.usuario = ?";
        $params[] = $usuario;
    }

    /* ===============================
        5. FILTROS DE BÚSQUEDA
    ================================ */
    $searchValue       = $_POST['search']['value'] ?? '';

    if ($searchValue !== '') {
        $where[] = "(
            t1.nombre LIKE ? OR 
            t1.usuario LIKE ? OR
            t2.perfil LIKE ? OR 
            t1.estatus LIKE ?  
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
    $orderColumn = 't1.id';
    $orderDir = 'DESC';

    /* ===============================
        7. TOTAL DE REGISTROS (SIN FILTRO)
    ================================ */
    $stmtTotal = $conn->query("SELECT COUNT(*) FROM staff");
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    /* ===============================
        8. TOTAL DE REGISTROS FILTRADOS
    ================================ */    
    $sqlFiltered = "SELECT COUNT(*) FROM staff t1 LEFT JOIN perfiles t2 ON t1.perfil = t2.id $whereSQL";
    $stmtFiltered = $conn->prepare($sqlFiltered);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    /* ===============================
        9. QUERY DE DATOS
    ================================ */    
    $sqlData = "
    SELECT
        t1.id,
        t1.nombre,
        t1.usuario,
        t2.perfil,
        t1.estatus,
        t1.fec_reg
    FROM staff t1
    LEFT JOIN perfiles t2 ON t1.perfil = t2.id
    $whereSQL
    ORDER BY t1.id DESC
    LIMIT $start, $length
    ";

    $stmt = $conn->prepare($sqlData);
    $stmt->execute($params);

    /* ===============================
        10. ARMADO DE DATA
    ================================ */

    $data = [];

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = $fila['id'];

        // Guardamos el usuario antes de modificar el array
        $usuarioFila = $fila['usuario'];

        unset($fila['id']);

        $btnEditar = '';

        // SOlo mostrar boton si: Es administrador o supervisor, caso contrario solo puede con su usuario
        if (in_array($perfil, ['Adminis', 'Supervi']) || $usuarioFila === $usuario) {

            $btnEditar = '<button class="btn btn-warning btn-sm btnEditar" data-id="'.$id.'">
                            <i class="fa fa-pencil"></i>
                        </button>';

        }        

        // Limpieza de espacios 
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

?>