<?php

    require_once '../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/controllers/AfiliadosController.php';
    require_once ROOT_PATH . '/controllers/MenuController.php';
    authorize(['Adminis', 'Supervi', 'Avisual', 'Snutric', 'Afisica', 'Ccorpor', 'Tmuestr', 'Svitale', 'Pulmvit']);

    $perfil  = $_SESSION['perfil'] ?? null;
    $nombre  = $_SESSION['nombre'] ?? null;   // Nombre
    $usuario = $_SESSION['usuario'] ?? null; // Usuario
    $modulo = 'Altas o Bajas';

    $registros = AfiliadosController::getRegistros(
        $perfil,
        $nombre,
        $usuario
    );

    $menuItems = MenuController::getMenuByPerfil($perfil);

    // Cargamos las variables de MySQL
    require_once "../config/database.php";
    $conn = conn();
    $query = "SELECT t1.Id, t1.perfil FROM perfiles t1 ORDER BY t1.Id ASC";
    $perfile = $conn -> query($query);

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
    <link rel="stylesheet" type = "text/css" href="/prevent_care/assets/css/main.css">
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


    <title>Altas o Bajas</title>
    
    </head>

    <body>
        
        <?php include '../partials/navbar_m.php'; ?>

        <div class="container-fluid px-4 my-4">
            <div class="card shadow-sm w-100">
                <div class="card-body">

                    <?php include '../partials/busquedas.php'; ?>

                    <?php include '../partials/talba.php'; ?>

                </div>
            </div>
        </div>

        <?php include '../partials/nuevo_alb.php'; ?>

        <?php include '../partials/footer.php'; ?>
        <?php include '../partials/modal_info.php'; ?>

        <script>
            const PERFIL_USUARIO = "<?= $_SESSION['perfil'] ?? '' ?>";
            const GRUPO_NOMBRE = "<?= $_SESSION['nombre'] ?? '' ?>";
            const GRUPO_USUARIO = "<?= $_SESSION['usuario'] ?? '' ?>";
        </script>

        <script src= '../assets/js/alb.js'></script>
        <script src = "<?= BASE_URL ?>/assets/js/comun.js"></script>

    </body>

</html>

<script>
    const INFO_MODULO = `
        <p><strong>Alta o Baja de Usuario</strong></p>
        <p>Aqui se dan de alta a los Usuarios y los permisos que tendran en la aplicacion.</p>
        <p>NOmbre, Usuario, Contraseña, Perfil, y Estatus.</p>
        <p>Se exporta datos de las tablas.</p>
    `;
</script>