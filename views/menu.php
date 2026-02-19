<?php

    require_once '../config/config.php';
    require_once ROOT_PATH . '/middleware/auth.php';
    require_once ROOT_PATH . '/controllers/AfiliadosController.php';
    require_once ROOT_PATH . '/controllers/MenuController.php';

    $perfil  = $_SESSION['perfil'] ?? null;
    $nombre  = $_SESSION['nombre'] ?? null;   // Nombre
    $usuario = $_SESSION['usuario'] ?? null; // Usuario

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
   <link rel="shortcut icon" href="/prevent-care/assets/img/preventcare_icon1.png">
  <!-- /Logo -->
  <!-- CSS -->
   <link rel="stylesheet" type = "text/css" href="/prevent-care/assets/libs/css/bootstrap.min.css">
   <link rel="stylesheet" type = "text/css" href="/prevent-care/assets/libs/fontawesome/css/all.min.css">
   <link rel="stylesheet" type = "text/css" href="/prevent-care/assets/libs/css/jquery-ui.css">
   <link rel="stylesheet" type = "text/css" href="/prevent-care/assets/css/main.css">
   <link rel="stylesheet" type = "text/css" href="/prevent-care/assets/css/menu.css">
  <!-- /CSS -->
  <!-- JS -->
   <script type = "text/javascript" src = "/prevent-care/assets/libs/js/jquery-3.7.1.min.js"></script>
   <script type = "text/javascript" src = "/prevent-care/assets/libs/js/jquery-ui.min.js"></script>
   <script type = "text/javascript" src = "/prevent-care/assets/libs/js/bootstrap.bundle.min.js"></script>
  <!-- /JS -->


  <title>Menú</title>
  

</head>

<body>

    <?php include '../partials/navbar.php'; ?>

    <main class="menu-container">

        <?php foreach ($menuItems as $item): ?>

            <?php $enabled = $item['enabled'] ?? false; ?>

            <?php if ($enabled): ?>
                <a href="<?= $item['url'] ?>" class="menu-card <?= $item['color'] ?>">
                
            <?php else: ?>
                <div class="menu-card <?= $item['color'] ?> disabled">
            <?php endif; ?>

                <i class="fa-solid <?= $item['icon'] ?>"></i>
                <span><?= htmlspecialchars($item['title']) ?></span>

            <?php if ($enabled): ?>
                </a>
            <?php else: ?>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>

    </main>
    <script src = "<?= BASE_URL ?>/assets/js/main.js"></script>
    <script src = "<?= BASE_URL ?>/assets/js/menu.js"></script>
    <script src = "<?= BASE_URL ?>/assets/js/comun.js"></script>
    
    <?php include '../partials/footer.php'; ?>
    <?php include '../partials/modal_info.php'; ?>

</body>
</html>

<script>
    const INFO_MODULO = `
        <p><strong>Menu</strong></p>
        <p>Aqui se accede a los diferentes modulos del aplicativo:</p>
        <p>Registro.</p>
        <p>Signos Vitales.</p>
        <p>Toma de Muestras.</p>
        <p>Composicion Corporal.</p>
        <p>Salud Nutricional.</p>
        <p>Capacidad Auditiva.</p>
        <p>Capacidad Pulmonar.</p>
        <p>Agudeza Visual.</p>
        <p>Activacion Fisica.</p>
        <p>Relajacion.</p>
    `;
</script>
