<?php

    function conn(): mysqli{
        // Carga las credenciales desde un archivo.
        $credentials = require 'C:\laragon\credentials.php';

        $mysqli = new mysqli(
            $credentials['servername'],
            $credentials['username'],
            $credentials['password'],
            $credentials['database']
        );

        // Certificar conexion
        if($mysqli->connect_errno) {
            throw new Exception("Error de conexion: " . $mysqli->connect_error);
        }

        $mysqli->set_charset("utf8mb4");
        // Echo "Conexion Exitosa";

        return $mysqli;
    }

?> 