<?php
   // Ruta física del proyecto (filesystem)
    define('ROOT_PATH', realpath(__DIR__ . '/..'));

    // Detectar si estamos en Azure
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'azurewebsites.net') !== false) {
    
        define('BASE_URL', '');

    } else {

        define('BASE_URL', '/prevent-care');

    }

?>