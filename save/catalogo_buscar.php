<?php

    header('Content-Type: application/json');
    session_start();

    require_once __DIR__ . '/../config/database1.php';

    $conn = conn();  

    $tabla = $_GET['tabla'];
    $q = $_GET['q'] ?? '';

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

$sql="SELECT id,nombre
FROM $tabla
WHERE nombre LIKE ?
ORDER BY nombre
LIMIT 20";

$stmt=$conn->prepare($sql);
$stmt->execute(["%$q%"]);

$data=[];

while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

$data[]=[
"id"=>$row['id'],
"text"=>$row['nombre']
];

}

echo json_encode($data);