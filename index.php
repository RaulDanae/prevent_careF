<?php
    session_start();

    require_once __DIR__ . '/config/config.php';
    require_once 'helpers/flash.php';

    if (isset($_SESSION['no_empleado'])) {
        header("Location: " . BASE_URL . "/views/menu.php");
        exit();
    }

    $error = getFlash('error');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/preventcare_icon1.png">
    <title>Prevent Care</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body>

    <?php if ($error): ?>
    <div class="login-error">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="login-layout">

        <!-- Columna izquierda -->
        <div class="left" id="bgSlider"></div>

        <!-- Columna derecha -->
        <div class="right">
            <div class="card login-card">
                <div class="logo-area">
                    <img src="<?= BASE_URL ?>/assets/img/preventcare_icon2.png" alt="Logo PreventCare">
                    <h1>Bienvenid@</h1>
                    <p>Accede a tu plataforma de trabajo</p>
                </div>

                <form action="<?= BASE_URL ?>/auth/valida.php" method="POST" role="form">
                    <label>Usuario</label>
                    <input type="text" name="usuario" required>

                    <label>Contraseña</label>
                    <input type="password" name="contrasena" autocomplete="username" required>

                    <button type="submit">Iniciar sesión</button>
                </form>

                <div class="domain-info">
                    <span>🌐 www.prevent-care.mx</span>
                </div>

            </div>
        </div>

    </div>


    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/assets/js/login.js"></script>
</body>
</html>