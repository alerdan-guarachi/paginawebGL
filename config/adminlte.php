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

    'logo' => '<b>Good Life</b> S.R.L.',
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
    'classes_sidebar' => 'sidebar-dark-orange elevation-4',
    'classes_sidebar_nav' => '',
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
    'sidebar_collapse' => false,
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
        [
            'text' => 'Mensajes',
            'route'  => 'admin.mensajes.create',
            'icon' => 'fas fa-fw fas fa-comment',
            'can'  => 'admin.mensajes.index',
        ],
        [
            'text' => 'Control de Registros',
            'icon' => 'fas fa-fw fas fa-calendar-alt',
            'can'  => 'admin.admprogramaciones.index',
            'submenu' => [
                [
                    'text' => 'Prog. Para Hoy',
                    'route'  => 'admin.admprogramaciones.index',
                    'icon' => 'fas fa-fw fas fa-calendar-check',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Clientes Creados Hoy',
                    'route'  => 'admin.admprogramaciones.clientescreadoshoy',
                    'icon' => 'fas fa-fw fas fa-users',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Baterias Creados Hoy',
                    'route'  => 'admin.admprogramaciones.bateriascreadoshoy',
                    'icon' => 'fas fa-fw fas fa-battery-half',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Prog. Creados Hoy',
                    'route'  => 'admin.admprogramaciones.programacionescreadoshoy',
                    'icon' => 'fas fa-fw fas fa-calendar-check',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Doc. Pendientes',
                    'route'  => 'admin.admprogramaciones.documentacionpendiente',
                    'icon' => 'fas fa-fw fas fa-question-circle',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Doc. Activa',
                    'route'  => 'admin.admprogramaciones.documentacionactiva',
                    'icon' => 'fas fa-fw fas fa-folder-open',
                    'can'  => 'admin.admprogramaciones.index',
                ],
                [
                    'text' => 'Reportes',
                    'route'  => 'admin.reportes.index',
                    'icon' => 'fas fa-fw fas fa-file-alt',
                    'can'  => 'admin.reportes.index',
                ],
                [
                    'text' => 'Control de registros',
                    'route'  => 'admin.controlprogramacion.index',
                    'icon' => 'fas fa-fw fas fa-chart-bar',
                    'can'  => 'admin.reportes.index',
                ],
                
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
                    'route'  => 'admin.proveedores.index',
                    'icon' => 'fas fa-fw fas fa-address-card',
                    'can'  => 'admin.proveedores.index',
                ],
                [
                    'text' => 'Empresas',
                    'route'  => 'admin.empresas.index',
                    'icon' => 'fas fa-fw fas fa-building',
                    'can'  => 'admin.empresas.index',
                ],
                [
                    'text' => 'Requisitos',
                    'route'  => 'admin.serviciosrequisitos.index',
                    'icon' => 'fas fa-fw fas fa-thumbtack',
                    'can'  => 'admin.empresas.index',
                ],
                

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
                    'text' => 'Informes Finales',
                    'route'  => 'admin.informesfinales.index',
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
                [
                    'text' => 'Personal',
                    'route'  => 'admin.personal.index',
                    'icon' => 'fas fa-fw fa-user-friends',
                    'can'  => 'admin.personal.index',
                ],
                [
                    'text' => 'Roles',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-fw fas fa-user-lock',
                    'can'  => 'admin.roles.index',
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
        
        [
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
                    'icon' => 'fas fa-fw fas fa-file',
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
