<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';
    $conn = conn(); 

    $upper = fn($v) => mb_strtoupper(trim($v), 'UTF-8');

    try {
    
        if(!isset($_SESSION['perfil'])){
            exit;
        }
        $perfil = $_SESSION['perfil'];
        $permitidos = ['Adminis', 'Supervi'];

        if(!in_array($perfil, $permitidos)){
            http_response_code(403);
            echo json_encode([
                "error" => "No tienes permisos para crear registros"
            ]);
            exit;
        }

        $tabla=$_POST['tabla'];
        $nombre=$_POST['nombre'];

        $permitidas = [
            "trecipientes",
            "tipomuestras",
            "tmetodologias",
            "tunidades",
            "perfilestudios"
        ];

        if(!in_array($tabla,$permitidas)){
            exit;
        }

        $sqlCheck = "SELECT id FROM $tabla WHERE nombre = ?";
        $stmt = $conn->prepare($sqlCheck);
        $stmt->execute([$nombre]);

        $existente = $stmt->fetchColumn();

        if($existente){
            echo json_encode(["id"=>$existente]);
            exit;
        }

        # Aqui se pueden agregar las tablas a las que decida agregarles una columna de activo
        $tablasConActivo = ['perfilestudios'];

        if (in_array($tabla, $tablasConActivo)) {
            $sql = "INSERT INTO $tabla(nombre, activo) VALUES (?, 1)";
        } else {
            $sql="INSERT INTO $tabla(nombre) VALUES(?)";
        }

        

        $stmt=$conn->prepare($sql);
        $stmt->execute([$upper($nombre)]);

        $id=$conn->lastInsertId();

        echo json_encode([
            "id"=>$id
        ]);

    } catch(Exception $e){

        echo json_encode([
            "error" => $e -> getMessage()
        ]);

    }