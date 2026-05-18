<?php

    require_once __DIR__ . '/../config/config.php';

    class BusquedaController {

        public static function getAcciones($perfil, $modulo) {

            $acciones = [

                'registro' => [
                    [
                        'id' => 'nuevo',
                        'icon' => 'fa fa-plus',
                        'roles' => ['Adminis', 'Supervi'],
                        'modal' => '#modalNuevo'
                    ],
                    [
                        'id' => 'excel',
                        'icon' => 'fa fa-file-import',
                        'roles' => ['Adminis', 'Supervi']
                    ],
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'alba' => [
                    [
                        'id' => 'nuevo',
                        'icon' => 'fa fa-plus',
                        'roles' => ['Adminis', 'Supervi'],
                        'modal' => '#modalNuevo'
                    ],
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'empresa' => [
                    [
                        'id' => 'nuevo',
                        'icon' => 'fa fa-plus',
                        'roles' => ['Adminis', 'Supervi'],
                        'modal' => '#modalNuevo'
                    ],
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'evento' => [
                    [
                        'id' => 'nuevo',
                        'icon' => 'fa fa-plus',
                        'roles' => ['Adminis', 'Supervi'],
                        'modal' => '#modalNuevo'   
                    ],
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'laboratorio' => [
                    [
                        'id' => 'nuevo',
                        'icon' => 'fa fa-plus',
                        'roles' => ['Adminis', 'Supervi', 'Laboratorio', 'Caplab'],
                        'modal' => '#modalNuevo'   
                    ],
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'recepcion' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'signos_vitales' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'toma_muestras' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'composicion_corporal' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'capacidad_pulmonar' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'salud_nutricional' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'relajacion' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'capacidad_auditiva' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'agudeza_visual' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'activacion_fisica' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'filtro' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ]
                ],

                'agenda' => [
                    [
                        'id' => 'descargar',
                        'icon' => 'fa fa-download',
                        'roles' => ['*']
                    ],
                                        [
                        'id' => 'excel',
                        'icon' => 'fa fa-file-import',
                        'roles' => ['Adminis', 'Supervi']
                    ]
                ]

            ];

            $resultado = [];

            if (!isset($acciones[$modulo])) {
                return [];
            }

            foreach ($acciones[$modulo] as $accion) {

                if (
                    in_array('*', $accion['roles']) ||
                    in_array($perfil, $accion['roles'])
                ) {
                    $resultado[] = $accion;
                }
            }

            return $resultado;
        }
    }