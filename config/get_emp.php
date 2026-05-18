<?php

    header('Content-Type: application/json');

    require_once '../config/database1.php';

    $input = json_decode(file_get_contents("php://input"), true);

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $conn = conn(); // 👈 aquí obtienes el PDO

    // Datos Principales
    $stmt = $conn->prepare("
         SELECT t1.id_comp, t1.compania, t1.nomcom, t1.direccion_emp, t1.razon_social_emp, t1.rfc_emp,
                t1.telefono_emp, t1.nombre_contacto, t1.genero_contacto, t1.telefono_contacto, 
                t1.mail_contacto
         FROM compania t1
         WHERE t1.id_comp = ?
    ");

    $stmt->execute([$id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$company){
        http_response_code(404);
        echo json_encode(["error"=>"No encontrado"]);
        exit;
    }

    // Sucursales
    $stmt = $conn->prepare("
        SELECT
            t2.id_sucursal,
            t2.nombre_sucursal
        FROM sucursal t2
        WHERE t2.id_comp = ?
    ");

    $stmt->execute([$id]);
    $sucur = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // REspuesta
    echo json_encode([
        "id"              => $company['id_comp'],
        "compania"        => $company['compania'],
        "nombre"          => $company['nomcom'],
        "direccion"       => $company['direccion_emp'],
        "razon"           => $company['razon_social_emp'],
        "rfc"             => $company['rfc_emp'],
        "telefono"        => $company['telefono_emp'],
        "contacto"        => $company['nombre_contacto'],
        "genero"          => $company['genero_contacto'],
        "telefonoc"       => $company['telefono_contacto'],
        "mailc"           => $company['mail_contacto'],
        "sucursal"        => $sucur
    ]);

?>