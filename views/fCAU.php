<?php

    require_once '../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/controllers/AfiliadosController.php';
    require_once ROOT_PATH . '/controllers/MenuController.php';
    authorize(['Adminis', 'Supervi']);

    $perfil  = $_SESSION['perfil'] ?? null;
    $nombre  = $_SESSION['nombre'] ?? null;   // Nombre
    $usuario = $_SESSION['usuario'] ?? null; // Usuario
    $modulo = 'Capacidad Auditiva';

    $registros = AfiliadosController::getRegistros(
        $perfil,
        $nombre,
        $usuario
    );

    $menuItems = MenuController::getMenuByPerfil($perfil);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <!-- Logo -->
    <link rel="shortcut icon" href="/prevent_care/assets/img/preventcare_icon1.png">
    <!-- /Logo -->
    <!-- CSS -->
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/bootstrap.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/fontawesome/css/all.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/jquery-ui.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/alertify.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/default.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/libs/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/css/maincau.css">
    <!-- <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/css/menu.css"> -->
    <!-- /CSS -->
    <!-- JS -->
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/jquery-3.7.1.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/jquery-ui.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/jquery.dataTables.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/dataTables.buttons.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/jszip.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/buttons.html5.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/alertify.min.js"></script>
    <script type = "text/javascript" src = "/prevent_care/assets/libs/js/bootstrap.bundle.min.js"></script>
    <!-- /JS -->


    <title>Capacidad Auditiva</title>
    
    </head>

    <body>
        
        <?php include '../partials/navbar_m.php'; ?>

        <div class="container-fluid px-4 my-4">
            <div class="card shadow-sm w-100">
                <div class="card-body">

                    <?php include '../partials/busquedas.php'; ?>

                    <?php include '../partials/taud.php'; ?>

                </div>
            </div>
        </div>

        <?php include '../partials/nuevo_aud.php'; ?>

        <?php include '../partials/footer.php'; ?>

        <script>
            const PERFIL_USUARIO = "<?= $_SESSION['perfil'] ?? '' ?>";
            const GRUPO_NOMBRE = "<?= $_SESSION['nombre'] ?? '' ?>";
            const GRUPO_USUARIO = "<?= $_SESSION['usuario'] ?? '' ?>";
        </script>

        <script src= '../assets/js/aud.js'></script>

    </body>

</html>