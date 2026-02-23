<?php

    function conn(): PDO
    {
        if (getenv('DB_HOST')) {

            $servername = getenv('DB_HOST');
            $database   = getenv('DB_NAME');
            $username   = getenv('DB_USER');
            $password   = getenv('DB_PASS');

            $dsn = "mysql:host={$servername};port=3306;dbname={$database};charset=utf8mb4";

            return new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,

                    // Cambios
                    PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt'
                ]
            );

        } else {

            $credentials = require 'C:/laragon/credentials11.php';

            $servername = $credentials['servername'];
            $username   = $credentials['username'];
            $password   = $credentials['password'];
            $database   = $credentials['database'];

            $dsn = "mysql:host={$servername};dbname={$database};charset=utf8mb4";

            return new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                    // 👈 SIN SSL en local
                ]
            );
        }            
    }