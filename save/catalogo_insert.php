<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database.php';

    $conn = conn();  

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

    $sql="INSERT INTO $tabla(nombre) VALUES(?)";

    $stmt=$conn->prepare($sql);
    $stmt->execute([$nombre]);

    $id=$conn->lastInsertId();

    echo json_encode([
        "id"=>$id
    ]);