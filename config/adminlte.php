<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'GOOD LIFE',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>GOOD LIFE</b> S.R.L.',
    'logo_img' => 'img/logo.png',
    'logo_img_class' => 'brand-image-xs',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'img/logo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'cwrapper',
        'img' => [
            'path' => 'img/logo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 500,
            'height' => 250,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-orange',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => 'bg-dark',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-olive elevation-4',
    'classes_sidebar_nav' => 'nav-compact nav-flat',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',
    /* sidebar-dark-primary */
    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Buscar',
        ],
        [
            'text' => 'blog',
            'url' => 'admin/blog',
            'can' => 'manage-blog',
        ],
        /* [
            'text' => 'Mensajes',
            'route'  => 'admin.mensajes.create',
            'icon' => 'fas fa-fw fas fa-comment',
            'can'  => 'admin.mensajes.index',
        ], */
        [
            'text' => 'Mensajes',
            'route'  => 'admin.mensajes.create',
            'icon' => 'fas fa-fw fas fa-comment',
            'can'  => 'admin.mensajes.index',
        ],
        /* [
            'text' => 'EXCEL',
            'route'  => 'upload.excel',
            'icon' => 'fas fa-fw fas fa-comment',
            'can'  => 'admin.mensajes.index',
        ], */
        /*  [
            'text' => 'Caja Central',
            'icon' => 'fas fa-cash-register',
            'can'  => 'admin.caja.index',
            'submenu' => [
                [
                    'text' => 'Ingresos',
                    'route'  => 'admin.caja.index',
                    'icon' => 'fas fa-arrow-circle-down',
                    'can'  => 'admin.caja.index',
                ],
                [
                    'text' => 'Egresos',
                    'route'  => 'admin.caja.index',
                    'icon' => 'fas fa-arrow-circle-up',
                    'can'  => 'admin.caja.index',
                ],
            ],
        ], */
        [
            'text' => 'Control de Registros',
            'icon' => 'fas fa-fw fas fa-calendar-alt',
            'can'  => 'admin.admprogramaciones.index',
            'submenu' => [
                [
                    'text' => 'Prog. por Fecha',
                    'route'  => 'admin.admprogramaciones.index',
                    'icon' => 'fas fa-fw fas fa-calendar-check',
                    'can'  => 'admin.admprogramaciones.programacionesdiarias',
                ],
                [
                    'text' => 'Gestión de Registros',
                    'route'  => 'admin.admprogramaciones.clientescreadoshoy',
                    'icon' => 'fas fa-fw fas fa-layer-group',
                    'can'  => 'admin.admprogramaciones.create',
                ],
                /* [
                    'text' => 'Baterias Creados Hoy',
                    'route'  => 'admin.admprogramaciones.bateriascreadoshoy',
                    'icon' => 'fas fa-fw fas fa-battery-half',
                    'can'  => 'admin.admprogramaciones.create',
                ], */
                
                [
                    'text' => 'Gestión de Informes',
                    'route'  => 'admin.admprogramaciones.documentacionpendiente',
                    'icon' => 'fas fa-fw fas fa-swatchbook',
                    'can'  => 'admin.admprogramaciones.create',
                ],
                /* [
                    'text' => 'Pagos de programaciones',
                    'route'  => 'admin.admprogramaciones.pagosprogramaciones',
                    'icon' => 'fas fa-fw fas fa-calendar-check',
                    'can'  => 'admin.admprogramaciones.pagosprogramaciones',
                ], */
                /* [
                    'text' => 'Doc. Activa',
                    'route'  => 'admin.admprogramaciones.documentacionactiva',
                    'icon' => 'fas fa-fw fas fa-folder-open',
                    'can'  => 'admin.admprogramaciones.create',
                ], */
                [
                    'text' => 'Reportes',
                    'route'  => 'admin.reportes.index',
                    'icon' => 'fas fa-fw fas fa-file-alt',
                    'can'  => 'admin.reportes.index',
                ],
                [
                    'text' => 'Registros generales',
                    'route'  => 'admin.controlprogramacion.index',
                    'icon' => 'fas fa-fw fas fa-chart-bar',
                    'can'  => 'admin.admprogramaciones.graficosregistrosgenerales',
                ],
                /* [
                    'text' => 'Unir Pdf',
                    'route'  => 'admin.admprogramaciones.unirpdf',
                    'icon' => 'fas fa-fw fas fa-chart-bar',
                ], */
                
            ],
        ],

        [
            'text' => 'Administrar Asociados',
            'icon' => 'fas fa-fw fas fa-users',
            'can'  => 'admin.asociados.index',
            'submenu' => [
                [
                    'text' => 'Asociados',
                    'route'  => 'admin.asociados.index',
                    'icon' => 'fas fa-fw fas fa-user-plus',
                    'can'  => 'admin.asociados.index',
                ],
                [
                    'text' => 'Proveedores',
                    'route'  => 'admin.proveedoresservicios.listaproveedoresservicios',
                    'icon' => 'fas fa-fw fa-id-card-alt',
                    'can'  => 'admin.proveedoresservicios.index',
                ],
                /* [
                    'text' => 'Proveedores Médicos',
                    'route'  => 'admin.proveedores.index',
                    'icon' => 'fas fa-fw fas fa-address-card',
                    'can'  => 'admin.proveedores.index',
                ], */
                [
                    'text' => 'Empresas',
                    'route'  => 'admin.empresas.index',
                    'icon' => 'fas fa-fw fas fa-building',
                    'can'  => 'admin.empresas.index',
                ],
                /* [
                    'text' => 'Requisitos',
                    'route'  => 'admin.serviciosrequisitos.index',
                    'icon' => 'fas fa-fw fas fa-thumbtack',
                    'can'  => 'admin.empresas.index',
                ], */
                

            ],
        ],
        
        [
            'text' => 'Programación y Doc.',
            'icon' => 'fas fa-fw fas fa-cogs',
            'can'  => 'admin.informesfinales.verresultadostodosclientes',
            'submenu' => [
                /* [
                    'text' => 'Estado de Prog.',
                    'route'  => 'admin.informesfinales.documentosprogramaciones',
                    'icon' => 'fas fa-fw fas fa-table',
                    'can'  => 'admin.admprogramaciones.index',
                ], */
                [
                    'text' => 'Reservas Médicas',
                    'route'  => 'admin.informesfinales.reservasmedicas',
                    'icon' => 'fas fa-fw fas fa-book-reader',
                    'can'  => 'admin.informesfinales.verreservasmedicas',
                ],
                [
                    'text' => 'Resultados Médicos ITA',
                    'route'  => 'admin.informesfinales.estadodocumentacionprogramacion',
                    'icon' => 'fas fa-fw fas fa-x-ray',
                    'can'  => 'admin.informesfinales.verresultadosmedicosgeneral',
                ],
                [
                    'text' => 'Resultados Médicos AUDI',
                    'route'  => 'admin.informesfinales.resultadosmedicosclientesauditoria',
                    'icon' => 'fas fa-fw fas fa-first-aid',
                    'can'  => 'admin.informesfinales.verresultadosmedicosgeneral',
                ],
                [
                    'text' => 'Resultados Médicos BNC',
                    'route'  => 'admin.informesfinales.resultadosmedicosclientesbancos',
                    'icon' => 'fas fa-fw fas fa-diagnoses',
                    'can'  => 'admin.informesfinales.verresultadosmedicosbanco',
                ],
                [
                    'text' => 'Informes Finales ITA',
                    'route'  => 'admin.informesfinales.index',
                    'icon' => 'fas fa-fw fas fa-paste',
                    'can'  => 'admin.informesfinales.verinformesfinales',
                ],
                [
                    'text' => 'Informes Finales AUDI',
                    'route'  => 'admin.informesfinales.informesfinalesauditoria',
                    'icon' => 'fas fa-fw fas fa-paste',
                    'can'  => 'admin.informesfinales.verinformesfinales',
                ],
            ],
        ],
        [
            'text' => 'Administrar Usuarios',
            'icon' => 'fas fa-fw fas fa-user-cog',
            'can'  => 'admin.users.index',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'route'  => 'admin.users.index',
                    'icon' => 'fas fa-fw fa-user-friends',
                    'can'  => 'admin.users.index',
                ],
                /* [
                    'text' => 'Proveedores',
                    'route'  => 'admin.proveedoresservicios.listaproveedoresservicios',
                    'icon' => 'fas fa-fw fa-id-card-alt',
                    'can'  => 'admin.proveedoresservicios.index',
                ], */
                [
                    'text' => 'Personal',
                    'route'  => 'admin.proveedoresservicios.listapersonal',
                    'icon' => 'fas fa-fw fas fa-users',
                    'can'  => 'admin.proveedores.index',
                ],
                /* [
                    'text' => 'Secciones de Servicios',
                    'route'  => 'admin.proveedoresservicios.listasecciones',
                    'icon' => 'fas fa-fw fas fa-swatchbook',
                    'can'  => 'admin.proveedoresservicios.index',
                ], */
                [
                    'text' => 'Roles',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-fw fas fa-user-lock',
                    'can'  => 'admin.roles.index',
                ],
                [
                    'text' => 'Asignar Código',
                    'route'  => 'admin.codigo.index',
                    'icon' => 'fas fa-fw fa-key',
                    'can'  => 'admin.codigo.index',
                ],
            ],
        ],
        [
            'text' => 'Administrar Baterias',
            'icon' => 'fas fa-fw fas fa-charging-station',
            'can'  => 'admin.areaacciones.index',
            'submenu' => [
                [
                    'text' => 'Baterias de Proveedores',
                    'route'  => 'admin.areaacciones.index',
                    'icon' => 'fas fa-fw fas fa-battery-three-quarters',
                    'can'  => 'admin.areaacciones.index',
                ],
                [
                    'text' => 'Lista de Áreas',
                    'route'  => 'admin.areaacciones.listadoareas',
                    'icon' => 'fas fa-fw fas fa-calendar',
                    'can'  => 'admin.areaacciones.verareasbateria',
                ],
                [
                    'text' => 'Lista de Acciones',
                    'route'  => 'admin.acciones.index',
                    'icon' => 'fas fa-fw fas fa-calendar',
                    'can'  => 'admin.areaacciones.veraccionesbateria',
                ],
            ],
        ],
        
        /* [
            'text' => 'Ordenes de Venta',
            'icon' => 'fas fa-fw fas fa-comment-dollar',
            'can'  => 'admin.informesfinales.consiliacionesgenerales',
            'submenu' => [
                [
                    'text' => 'Ordenes general',
                    'route'  => 'admin.informesfinales.consiliacionesclientesbanco',
                    'icon' => 'fas fa-fw fas fa-donate',
                    'can'  => 'admin.informesfinales.consiliacionesclientesbanco',
                ],
            ],
        ], */
        [
            'text' => 'Órdenes',
            'icon' => 'fas fa-fw fa-comment-dollar',
            'can'  => 'admin.informesfinales.consiliacionesgenerales',
            'submenu' => [
                [
                    'text' => 'Órdenes de Venta',
                    'route'  => 'admin.ordenes.ordenesventa.index',
                    'icon' => 'fas fa-fw fa-donate',
                    'can'  => 'admin.informesfinales.consiliacionesclientesbanco',
                ],
            ],
        ],
        [
            'text' => 'Prestaciones',
            'icon' => 'fas fa-fw fas fa-toolbox',
            'can'  => 'admin.tramites.index',
            'submenu' => [
                [
                    'text' => 'Instructivas de Poder',
                    'route'  => 'admin.instructivaspoder.index',
                    'icon' => 'fas fa-fw fas fa-book-reader',
                ],
                [
                    'text' => 'Proc. de Trámites Gestora',
                    'route'  => 'admin.tramites.index',
                    'icon' => 'fas fa-fw fas fa-paste',
                ],
                [
                    'text' => 'Modelo Cartas/Reclamos',
                    'route'  => 'admin.tramites.modelocartareclamo',
                    'icon' => 'fas fa-fw fas fa-file',
                ],

            ],
        ],

        [
            'text' => 'Panel Financiero',
            'icon' => 'fas fa-chart-bar',
            'can'  => 'admin.ingreso.index',
            'submenu' => [
                [
                    'text' => 'Resumen financiero',
                    'route'  => 'admin.caja.panel.resumenfinanciero',
                    'icon' => 'fas fa-chart-pie',
                    'can'  => 'admin.ingreso.index',
                ],                
            ],
        ],

        [
            'text' => 'Ingresos',
            'icon' => 'fas fa-chart-line',
            'can'  => 'admin.ingreso.index',
            'submenu' => [
                [
                    'text' => 'Caja de Ingreso',
                    'route'  => 'admin.caja.ingreso.index',
                    'icon' => 'fas fa-sign-in-alt',
                    'can'  => 'admin.ingreso.index',
                ],
                [
                    'text' => 'Documentacion',
                    'route'  => 'admin.caja.ingreso.documentacion',
                    'icon' => 'fas fa-file',
                    'can'  => 'admin.ingreso.index',
                ],
                [
                    'text' => 'Depósitos bancarios',
                    'route'  => 'admin.caja.ingreso.depositosbancarios',
                    'icon' => 'fas fa-piggy-bank',
                    'can'  => 'admin.ingreso.depositosbancarios',
                ],
                
            ],
        ],
        
        [
            'text' => 'Cuentas por Cobrar',
            'icon' => 'fas fa-cash-register',
            'can'  => 'admin.ingreso.index',
            'submenu' => [
                [
                    'text' => 'Nueva Cuenta Cobrar',
                    'route'  => 'admin.caja.cuentascobrar.nuevacuentacobrar',
                    'icon' => 'fas fa-bookmark',
                    'can'  => 'admin.ingreso.index',
                ],
                [
                    'text' => 'Cobrar Hoy',
                    'route'  => 'admin.caja.cuentascobrar.cobrarhoy',
                    'icon' => 'fas fa-donate',
                    'can'  => 'admin.ingreso.index',
                ],
                [
                    'text' => 'Cuentas Cobrar',
                    'route'  => 'admin.caja.cuentascobrar.listacuentascobrar',
                    'icon' => 'fas fa-money-bill-wave',
                    'can'  => 'admin.ingreso.index',
                ],
            ],
        ],
        [
            'text' => 'Créditos',
            'icon' => 'fas fa-hand-holding-usd',
            'can'  => 'admin.ingreso.index',
            'submenu' => [
                [
                    'text' => 'Asignar Crédito',
                    'route'  => 'admin.caja.cuentascobrar.ccporcredito',
                    'icon' => 'fas fa-money-check-alt',
                    'can'  => 'admin.ingreso.index',
                ],
                [
                    'text' => 'Créditos Aprobados',
                    'route'  => 'admin.caja.cuentascobrar.creditosaprobados',
                    'icon' => 'fas fa-stamp',
                    'can'  => 'admin.ingreso.index',
                ],
            ],
        ],

        [
            'text' => 'Egresos',
            'icon' => 'fas fa-comment-dollar',
            'can'  => 'admin.egreso.index',
            'submenu' => [
                [
                    'text' => 'Caja de Egresos',
                    'route'  => 'admin.caja.egreso.cajaegresos',
                    'icon' => 'fas fa-hand-holding-usd',
                    'can'  => 'admin.egreso.index',
                ],
                [
                    'text' => 'Documentación',
                    'route'  => 'admin.caja.egreso.documentacionegreso',
                    'icon' => 'fas fa-file',
                    'can'  => 'admin.egreso.index',
                ],
            ],
        ],
        [
            'text' => 'Cuentas por Pagar',
            'icon' => 'fas fa-comments-dollar',
            'can'  => 'admin.cuentasPagar.index',
            'submenu' => [
                [
                    'text' => 'Cuentas Pagar',
                    'route'  => 'admin.caja.cuentaspagar.listacuentaspagar',
                    'icon' => 'fas fa-money-bill-wave',
                    'can'  => 'admin.cuentasPagar.index',
                ],
                [
                    'text' => 'CxP Pendientes',
                    'route'  => 'admin.caja.cuentaspagar.cpppendientes',
                    'icon' => 'fas fa-coins',
                    'can'  => 'admin.cuentasPagar.cpppendientes',
                ],
                [
                    'text' => 'CxP Comprobantes',
                    'route'  => 'admin.caja.cuentaspagar.cppcomprobantes',
                    'icon' => 'fas fa-receipt',
                    'can'  => 'admin.cuentasPagar.cxpcomprobantes',
                ],
                /* [
                    'text' => 'Pagos Pendientes',
                    'route'  => 'admin.caja.cuentaspagar.cppregistradas',
                    'icon' => 'fas fa-hourglass-half',
                    'can'  => 'admin.cuentasPagar.index',
                ], */
            ],
        ],

        [
            'text' => 'Facturas',
            'icon' => 'fas fa-receipt',
            'can'  => 'admin.facturasegreso.index',
            'submenu' => [
                [
                    'text' => 'Facturas Egreso',
                    'route'  => 'admin.facturasegreso.index',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'can'  => 'admin.facturasegreso.index',
                ],
            ],
        ],

        [
            'text' => 'Cierre de Caja',
            'route'  => 'admin.caja.ingreso.cierre',
            'icon' => 'fas fa-lock',
            'can'  => 'admin.ingreso.index',
        ],

        [
            'text' => 'Anulaciones',
            'icon' => 'fas fa-ban',
            'can'  => 'admin.caja.anulaciones.anularcaja',
            'submenu' => [
                [
                    'text' => 'Anular Registro Caja',
                    'route'  => 'admin.caja.anulaciones.anularcaja',
                    'icon' => 'fas fa-times-circle',
                    'can'  => 'admin.caja.anulaciones.anularcaja',
                ],
                [
                    'text' => 'Anular Cuenta Cobrar',
                    'route'  => 'admin.asociados.anulaciones.anularcuentacobrar',
                    'icon' => 'fas fa-file-excel',
                    'can'  => 'admin.caja.anulaciones.anularcaja',
                ],
            ],
        ],
        
        [
            'text' => 'Inventario',
            'icon' => 'fas fa-cubes',
            'can'  => 'admin.inventario.verpestanainventario',
            'submenu' => [
                [
                    'text' => 'Inventario Total',
                    'route'  => 'admin.inventario.index',
                    'icon' => 'fas fa-boxes',
                    'can'  => 'admin.inventario.index',
                ],
                [
                    'text' => 'Solicitud de Inventario',
                    'route'  => 'admin.inventario.solicitarproducto',
                    'icon' => 'fas fa-file-signature',
                    'can'  => 'admin.inventario.solicitarproducto',
                ],
            ],
        ],
        [
            'text' => 'Pre-Órdenes y Órdenes',
            'icon' => 'fas fa-window-restore',
            'can'  => 'admin.inventario.index',
            'submenu' => [
                [
                    'text' => 'Nueva Pre-Órden',
                    'route'  => 'admin.inventario.crearordenes',
                    'icon' => 'fas fa-file-code',
                    'can'  => 'admin.inventario.index',
                ],
                [
                    'text' => 'Órdenes Pend. y Aprob.',
                    'route'  => 'admin.inventario.listaordenes',
                    'icon' => 'fas fa-sim-card',
                    'can'  => 'admin.inventario.index',
                ],
            ],
        ],

        [
            'text' => 'Bancos',
            'icon' => 'fas fa-piggy-bank',
            'can'  => 'admin.banco.index',
            'submenu' => [
                [
                    'text' => 'Consolidado General',
                    'route'  => 'admin.banco.index',
                    'icon' => 'fas fa-comments-dollar',
                    'can'  => 'admin.banco.index',
                ],
                /* [
                    'text' => 'Detalle Movimientos',
                    'route'  => 'admin.banco.detallemovimientos',
                    'icon' => 'fas fa-chart-pie',
                    'can'  => 'admin.banco.index',
                ], */
                /* [
                    'text' => 'Consolidado Órdenes',
                    'route'  => 'admin.banco.consolidadoegresos',
                    'icon' => 'fas fa-comments-dollar',
                    'can'  => 'admin.banco.index',
                ], */
                [
                    'text' => 'Cuentas Bancarias',
                    'route'  => 'admin.banco.montototalbancos',
                    'icon' => 'fas fa-landmark',
                    'can'  => 'admin.banco.index',
                ],
            ],
        ],


        /* [
            'text' => 'Caja Central',
            'icon' => 'fas fa-cash-register',
            'can'  => 'admin.cajaCentral.index',
            'submenu' => [
                [
                    'text' => 'Conciliación Bancaria',
                    'route'  => 'admin.caja.ingreso.cierre',
                    'icon' => 'fas fa-balance-scale',
                    'can'  => 'admin.cajaCentral.index',
                ],
                [
                    'text' => 'Facturas Emitidas',
                    'route'  => 'admin.caja.historialFacturas',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'can'  => 'admin.cajaCentral.index',
                ],
            ],
        ], */
        

        [
            'text' => 'Centro de Soporte',
            'icon' => 'fas fa-tools',
            'can'  => 'admin.soporte.index',
            'submenu' => [
                [
                    'text' => 'Registro Solicitudes',
                    'route'  => 'admin.soporte.index',
                    'icon' => 'fas fa-tasks',
                    'can'  => 'admin.soporte.index',
                ],
                [
                    'text' => 'Revisión Solicitudes',
                    'route'  => 'admin.soporte.review',
                    'icon' => 'fas fa-check-circle',
                    'can'  => 'admin.soporte.review',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
