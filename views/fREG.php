<?php

    require_once '../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/controllers/AfiliadosController.php';
    require_once ROOT_PATH . '/controllers/MenuController.php';
    authorize(['Adminis', 'Supervi']);

    $perfil  = $_SESSION['perfil'] ?? null;
    $nombre  = $_SESSION['nombre'] ?? null;   // Nombre
    $usuario = $_SESSION['usuario'] ?? null; // Usuario
    $modulo = 'Registro';

    $registros = AfiliadosController::getRegistros(
        $perfil,
        $nombre,
        $usuario
    );

    $menuItems = MenuController::getMenuByPerfil($perfil);

    // Cargamos las variables de MySQL
    require_once "../config/database.php";
    $conn = conn();
    $query = "SELECT t1.id_comp, t1.compania FROM compania t1 ORDER BY t1.id_comp ASC";
    $compania = $conn -> query($query);

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


    <title>Registro</title>
    
    </head>

    <body>
        
        <?php include '../partials/navbar_m.php'; ?>

        <div class="container-fluid px-4 my-4">
            <div class="card shadow-sm w-100">
                <div class="card-body">

                    <?php include '../partials/busquedas.php'; ?>

                    <?php include '../partials/treg.php'; ?>

                </div>
            </div>
        </div>

        <?php include '../partials/nuevo_reg.php'; ?>

        <?php include '../partials/footer.php'; ?>
        <?php include '../partials/modal_info.php'; ?>

        <script>
            const PERFIL_USUARIO = "<?= $_SESSION['perfil'] ?? '' ?>";
            const GRUPO_NOMBRE = "<?= $_SESSION['nombre'] ?? '' ?>";
            const GRUPO_USUARIO = "<?= $_SESSION['usuario'] ?? '' ?>";
        </script>

        <script src= '../assets/js/reg.js'></script>
        <script src = "<?= BASE_URL ?>/assets/js/comun.js"></script>

    </body>

</html>

<script>
    const INFO_MODULO = `
        <p><strong>Registros</strong></p>
        <p>Se registran los datos del paciente.</p>
        <p>Compañia, Clave del Empleado, Nombre del colaborador, Fecha Nacimiento, Genero, CURP, Email, RFC, Edad, Aviso Privacidad, Consentimiento Informado, Observaciones.</p>
        <p>De estos campos ya vienen prellenados de compañia a Email</p>
        <p>Aqui se puede dar de alta y editar registros</p>
        <p>Se imprimen los codigos de barras.</p>
        <p>Se importan archivos de excel.</p>
        <p>Se exporta datos de las tablas.</p>
    `;
</script>