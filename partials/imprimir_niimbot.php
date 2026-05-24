<?php

    $data = json_decode(file_get_contents("php://input"), true);

    $imagenes = $data['imagenes'];

    $resultados = [];

    foreach($imagenes as $i => $base64){

        $base64 = str_replace(
            'data:image/png;base64,',
            '',
            $base64
        );

        $imagen = base64_decode($base64);

        $nombre = 'etiqueta_' . time() . "_$i.png";

        $ruta = dirname(__DIR__) . '\\temp_labels\\' . $nombre;

        file_put_contents($ruta, $imagen);

        $cmd = 'py -3.11 -m niimprint '
            . '-m b1 '
            . '-c usb '
            . '-a COM3 '
            . '-r 0 '
            . '-i "' . $ruta . '"';

        $output = [];
        $result = null;

        exec($cmd . " 2>&1", $output, $result);

        $resultados[] = [
            'cmd' => $cmd,
            'result' => $result,
            'output' => $output
        ];

        usleep(3000000);
    }

    echo json_encode($resultados);

