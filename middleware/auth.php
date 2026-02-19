<?php

    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    function authorizeApi(array $rolesPermitidos = [])
    {

        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario'], $_SESSION['perfil'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'code' => 401,
                'message' => 'No autenticado'
            ]); 
            exit;
        }

        if (!empty($rolesPermitidos) && !in_array($_SESSION['perfil'], $rolesPermitidos, true)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'code' => 403,
                'message' => 'No autorizado'
            ]);
            exit;
        }
    }


    function authorize(array $rolesPermitidos = [])
    {
        if (!isset($_SESSION['usuario'], $_SESSION['perfil'])) {
            header('Location: /prevent_care/login.php');
            exit;
        }

        if (!empty($rolesPermitidos) && !in_array($_SESSION['perfil'], $rolesPermitidos, true)) {
            header('Location: /prevent_care/views/menu.php');
            exit;
        }
    }
    
    
    function authorizeDataTable(array $rolesPermitidos = [])
    {
        if (!isset($_SESSION['usuario'], $_SESSION['perfil']) ||
            (!empty($rolesPermitidos) && !in_array($_SESSION['perfil'], $rolesPermitidos))
        ) {

            http_response_code(200); // DataTables NO acepta 401/403

            echo json_encode([
                "draw" => intval($_POST['draw'] ?? 0),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "No autorizado"
            ]);
            exit;
        }
    }


    
?>


