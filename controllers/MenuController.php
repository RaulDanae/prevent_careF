<?php

    require_once __DIR__ . '/../config/config.php';

    class MenuController {

        public static function getMenuByPerfil($perfil, $modulo) {

            $menu = [

                'feria' => [
                    [
                        'title' => 'Toma de Signos Vitales',
                        'icon'  => 'fas fa-stethoscope',
                        'color' => 'green',
                        'url'   => BASE_URL . '/views/fTSV.php',
                        'roles' => ['Adminis', 'Supervi', 'Svitale', 'Comodin'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Toma de Muestras',
                        'icon'  => 'fas fa-syringe',
                        'color' => 'blue',
                        'url'   => BASE_URL . '/views/fTDM.php',
                        'roles' => ['Adminis', 'Supervi', 'Tmuestr', 'Comodin'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Composicion Corporal',
                        'icon'  => 'fas fa-person',
                        'color' => 'brown',
                        'url'   => BASE_URL . '/views/fCCO.php',
                        'roles' => ['Adminis', 'Supervi', 'Ccorpor'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Salud Nutricional',
                        'icon'  => 'fa-solid fa-drumstick-bite',
                        'color' => 'greengrass',
                        'url'   => BASE_URL . '/views/fSNU.php',
                        'roles' => ['Adminis', 'Supervi', 'Snutric'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Capacidad Auditiva',
                        'icon'  => 'fas fa-ear-deaf',
                        'color' => 'blue',
                        'url'   => BASE_URL . '/views/fCAU.php',
                        'roles' => ['Adminis', 'Supervi', 'Cauditiva', 'Comodin'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Capacidad Pulmonar',
                        'icon'  => 'fas fa-lungs',
                        'color' => 'pink',
                        'url'   => BASE_URL . '/views/fCPU.php',
                        'roles' => ['Adminis', 'Supervi', 'Pulmvit', 'Comodin'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Agudeza Visual',
                        'icon'  => 'fas fa-eye',
                        'color' => 'cyan',
                        'url'   => BASE_URL . '/views/fAVI.php',
                        'roles' => ['Adminis', 'Supervi', 'Avisual', 'Comodin'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Activacion Fisica',
                        'icon'  => 'fas fa-person-running',
                        'color' => 'purple',
                        'url'   => BASE_URL . '/views/fAFI.php',
                        'roles' => ['Adminis', 'Supervi', 'Afisica'],
                        'modulos' => ['feria']
                    ],
                    [
                        'title' => 'Relajacion',
                        'icon'  => 'fas fa-bed',
                        'color' => 'gray',
                        'url'   => BASE_URL . '/views/fREL.php',
                        'roles' => ['Adminis', 'Supervi', 'Relajacion'],
                        'modulos' => ['feria']

                    ],
                ],

                'admin' => [
                    [
                        'title' => 'Empresas',
                        'icon'  => 'fa-solid fa-industry',
                        'color' => 'black',
                        'url'   =>  BASE_URL . '/views/fEMP.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['admin']
                    ],
                    [
                        'title' => 'Eventos ',
                        'icon'  => 'fa-regular fa-handshake',
                        'color' => 'blue',
                        'url'   => BASE_URL . '/views/fEVE.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['admin']
                    ],
                    [
                        'title' => 'Pacientes',
                        'icon'  => 'fa-solid fa-user-check',
                        'color' => 'orange',
                        'url'   =>  BASE_URL . '/views/fREG.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['admin']
                    ],
                    [
                        'title' => 'Filtro Evento',
                        'icon'  => 'fa-solid fa-filter',
                        'color' => 'green',
                        'url'   =>  BASE_URL . '/views/fFIL.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['admin']
                    ],
                ],

                'general' => [
                    [
                        'title' => 'Administracion',
                        'icon'  => 'fa-solid fa-users-gear',
                        'color' => 'orange',
                        'url'   =>  BASE_URL . '/views/menuA.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['general']
                    ],
                    [
                        'title' => 'Laboratorio',
                        'icon'  => 'fa-solid fa-microscope',
                        'color' => 'green',
                        'url'   => BASE_URL . '/views/menuL.php',
                        'roles' => ['Adminis', 'Supervi', 'Laboratorio', 'Caplab'],
                        'modulos' => ['general']
                    ],
                    [
                        'title' => 'Feria',
                        'icon'  => 'fa-solid fa-users',
                        'color' => 'blue',
                        'url'   => BASE_URL . '/views/menuF.php',
                        'roles' => ['Adminis', 'Supervi', 'Tmuestr', 'Svitale', 'Ccorpor', 'Snutric', 
                                    'Cauditiva', 'Pulmvit', 'Avisual', 'Afisica', 'Relajacion', 'Comodin'],
                        'modulos' => ['general']
                    ],
                    [
                        'title' => 'Staff',
                        'icon'  => 'fa-solid fa-helmet-safety',
                        'color' => 'green',
                        'url'   =>  BASE_URL . '/views/fALBA.php',
                        'roles' => ['Adminis', 'Supervi', 'Tmuestr', 'Svitale', 'Ccorpor', 'Snutric', 
                                    'Cauditiva', 'Pulmvit', 'Avisual', 'Afisica', 'Relajacion', 'Comodin',
                                    'Laboratorio', 'Caplab'],
                        'modulos' => ['general']
                    ],
                    [
                        'title' => 'Agenda',
                        'icon'  => 'fa-regular fa-calendar',
                        'color' => 'purple',
                        'url'   =>  BASE_URL . '/views/fCAL.php',
                        'roles' => ['Adminis', 'Supervi'],
                        'modulos' => ['feria']
                    ]                  
                ],

                'laboratorio' => [
                    [
                        'title' => 'Estudios',
                        'icon'  => 'fa-solid fa-x-ray',
                        'color' => 'black',
                        'url'   =>  BASE_URL . '/views/fEST.php',
                        'roles' => ['Adminis', 'Supervi', 'Laboratorio', 'Caplab'],
                        'modulos' => ['laboratorio']
                    ],
                    [
                        'title' => 'Recepcion',
                        'icon'  => 'fa-solid fa-box',
                        'color' => 'green',
                        'url'   => BASE_URL . '/views/fREC.php',
                        'roles' => ['Adminis', 'Supervi', 'Laboratorio', 'Caplab'],
                        'modulos' => ['laboratorio']
                    ]                    
                ]

            ];

            $resultado = [];

            // Validar que el módulo exista
            if (!isset($menu[$modulo])) {
                return [];
            }            

            foreach ($menu[$modulo] as $item) {

                $item['enabled'] = in_array($perfil, $item['roles']);
                $resultado[] = $item;

            }

            return $resultado;

        }
    }