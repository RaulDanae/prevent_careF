<?php

    function conn(): PDO
    {
        // Carga las credenciales.
        $credentials = require 'C:/laragon/credentials.php';


        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $credentials['servername'],
            $credentials['database']
        );

        return new PDO(
            $dsn,
            $credentials['username'],
            $credentials['password'],
            [
                PDO::ATTR_ERRMODE                => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES       => false,
            ]
        );
    }

?>