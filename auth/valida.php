<?php

    session_start();

    require_once '../config/database.php';
    require_once '../config/config.php';
    require_once '../helpers/flash.php';


    // Valida que la sesion sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }

    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['contrasena'] ?? '';

    if ($username === '' || $password === '') {
        setFlash('error', 'Todos los campos son obligatorios');
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }

    $conn = conn();

    $sql = "SELECT t1.nombre, t1.usuario, t2.perfil, t1.password
            FROM staff t1
            LEFT JOIN perfiles t2 ON t1.perfil = t2.Id
            WHERE t1.usuario = ? AND t1.estatus = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        setFlash('error', 'Usuario o contraseña incorrectos');
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }

    /* Login exitoso */
    $_SESSION['nombre']     = $user['nombre'];
    $_SESSION['usuario']    = $user['usuario'];
    $_SESSION['perfil']     = $user['perfil'];

    header("Location: " . BASE_URL . "/views/menu.php");
    exit();

?>