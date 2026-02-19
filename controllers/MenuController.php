<?php

    require_once __DIR__ . '/../config/config.php';

    class MenuController {

        public static function getMenuByPerfil($perfil) {

            $menu = [
                [
                    'title' => 'Registros',
                    'icon'  => 'fas fa-file-lines',
                    'color' => 'orange',
                    'url'   =>  BASE_URL . '/views/fREG.php',
                    'roles' => ['Adminis', 'Supervi']
                ],
                [
                    'title' => 'Toma de Signos Vitales',
                    'icon'  => 'fas fa-stethoscope',
                    'color' => 'green',
                    'url'   => BASE_URL . '/views/fTSV.php',
                    'roles' => ['Adminis', 'Supervi', 'Svitale', 'Pulmvit']
                ],
                [
                    'title' => 'Toma de Muestras',
                    'icon'  => 'fas fa-syringe',
                    'color' => 'blue',
                    'url'   => BASE_URL . '/views/fTDM.php',
                    'roles' => ['Adminis', 'Supervi', 'Tmuestr']
                ],
                [
                    'title' => 'Composicion Corporal',
                    'icon'  => 'fas fa-person',
                    'color' => 'brown',
                    'url'   => BASE_URL . '/views/fCCO.php',
                    'roles' => ['Adminis', 'Supervi', 'Ccorpor']
                ],
                [
                    'title' => 'Salud Nutricional',
                    'icon'  => 'fa-solid fa-drumstick-bite',
                    'color' => 'greengrass',
                    'url'   => BASE_URL . '/views/fSNU.php',
                    'roles' => ['Adminis', 'Supervi', 'Snutric']
                ],
                [
                    'title' => 'Capacidad Auditiva',
                    'icon'  => 'fas fa-ear-deaf',
                    'color' => 'blue',
                    'url'   => BASE_URL . '/views/fCAU.php',
                    'roles' => ['Adminis', 'Supervi']
                ],
                [
                    'title' => 'Capacidad Pulmonar',
                    'icon'  => 'fas fa-lungs',
                    'color' => 'pink',
                    'url'   => BASE_URL . '/views/fCPU.php',
                    'roles' => ['Adminis', 'Supervi', 'Pulmvit']
                ],
                [
                    'title' => 'Agudeza Visual',
                    'icon'  => 'fas fa-eye',
                    'color' => 'cyan',
                    'url'   => BASE_URL . '/views/fAVI.php',
                    'roles' => ['Adminis', 'Supervi', 'Avisual']
                ],
                [
                    'title' => 'Activacion Fisica',
                    'icon'  => 'fas fa-person-running',
                    'color' => 'purple',
                    'url'   => BASE_URL . '/views/fAFI.php',
                    'roles' => ['Adminis', 'Supervi', 'Afisica']
                ],
                [
                    'title' => 'Relajacion',
                    'icon'  => 'fas fa-bed',
                    'color' => 'gray',
                    'url'   => BASE_URL . '/views/fREL.php',
                    'roles' => ['Adminis', 'Supervi']
                ],
            ];

            foreach ($menu as $key => $item) {
                $menu[$key]['enabled'] = in_array($perfil, $item['roles'] ?? []);
            }

            return $menu;

        }
    }

?>