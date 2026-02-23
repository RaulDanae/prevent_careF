<?php

    function conn(): mysqli{
        // Si existen variables de entorno 
        if (getenv('DB_HOST')){

            $servername = getenv('DB_HOST');
            $username   = getenv('DB_USER');
            $password   = getenv('DB_PASS');
            $database   = getenv('DB_NAME');

        } else {

            // SI no existen, usar archivo local
            $credentials = require 'C:\laragon\credentials11.php';

            
            $servername =    $credentials['servername'];
            $username   =    $credentials['username'];
            $password   =    $credentials['password'];
            $database   =    $credentials['database'];

        }

        $mysqli = mysqli_init();

        // Forzar SSL (Azure lo requiere)
        mysqli_ssl_set($mysqli, NULL, NULL, NULL, NULL, NULL);

        $mysqli -> real_connect(
            $servername,
            $username,
            $password,
            $database,
            3306,
            NULL,
            MYSQLI_CLIENT_SSL
        );

        // Certificar conexion
        if($mysqli->connect_errno) {
            throw new Exception("Error de conexion: " . $mysqli->connect_error);
        }

        $mysqli->set_charset("utf8mb4");
        // Echo "Conexion Exitosa";

        return $mysqli;
    }