<?php

    require_once __DIR__ . '/../config/config.php';

    class MenuController {

        public static function getMenuByPerfil($perfil) {

            $menu = [
                [
                    'title' => 'Estudios',
                    'icon'  => 'fa-solid fa-x-ray',
                    'color' => 'black',
                    'url'   =>  BASE_URL . '/views/fEST.php',
                    'roles' => ['Laboratorio', 'Caplab']
                ],
                [
                    'title' => 'Recepcion',
                    'icon'  => 'fa-solid fa-box',
                    'color' => 'green',
                    'url'   => BASE_URL . '/views/fREC.php',
                    'roles' => ['Laboratorio', 'Caplab']
                ],
            ];

            foreach ($menu as $key => $item) {
                $menu[$key]['enabled'] = in_array($perfil, $item['roles'] ?? []);
            }

            return $menu;

        }
    }