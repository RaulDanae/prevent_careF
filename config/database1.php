<?php

    function conn(): PDO
    {

        // Si existen variables de entorno
        if (getenv('DB_HOST')) {

            $servername = getenv('BD_HOST');
            $database = getenv('DB_NAME');
            $username = getenv('DB_USER');
            $password = getenv('DB_PASS');

        } else {

            // Si no existen usar archivo local
            $credentials = require 'C:/laragon/credentials11.php';

            $servername =    $credentials['servername'];
            $username   =    $credentials['username'];
            $password   =    $credentials['password'];
            $database   =    $credentials['database'];

        }

        $dsn = "mysql:host={$servername};port=3306;dbname={$database};charset=utf8mb4";

        return new PDO(

            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,

                // Forzar SSL
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false

            ]

        );

    }

?>