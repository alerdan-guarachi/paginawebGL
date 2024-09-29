<nav class="navbar navbar-expand-lg navbar-light shadow fixed-top" style="background-color: #e2e2e1;">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-success logo h1 align-self-center" href="welcome">
            <a href="welcome" class="navbar-brand ml-lg-3">
                <img src="assets/img/logo.png" style="width: 120px; height: 60px;" alt="">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between"
                id="templatemo_main_nav">
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('welcome') ? 'current-page' : '' }}"
                                href="{{ url('welcome') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('sobrenosotros') ? 'current-page' : '' }}"
                                href="{{ url('sobrenosotros') }}">Sobre nosotros</a>
                        </li>

                        <li class="nav-item">
                            <div class="dropdown" onmouseleave="closeDropdown()">
                                <a href="#" class="nav-link dropdown-toggle" id="dropdownMenuLink"
                                    data-toggle="dropdown">Servicios</a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item nav-link {{ request()->is('asesoramientolegal') ? 'current-page' : '' }}"
                                        href="{{ url('asesoramientolegal') }}">Asesoramiento Legal</a>
                                    <a class="dropdown-item nav-link {{ request()->is('medicina') ? 'current-page' : '' }}"
                                        href="{{ url('medicina') }}">Medicina</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? 'current-page' : '' }}" href="{{ url('contact') }}">Contactos</a>
                        </li>
                    </ul>
                </div>

                @if (Route::has('login'))
                    <div class="d-flex justify-content-end">
                        @auth
                            <a href="{{ url('/home') }}"
                                class="btn oval-button text-decoration-none d-flex align-items-center mr-2" title="Inicio">
                                <i class="fa fa-fw fa-home icon"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn oval-button text-decoration-none d-flex align-items-center mr-2">
                                <i class="fa fa-fw fa-user icon"></i>
                                <span class="text">Iniciar sesión</span>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="btn register-button text-decoration-none d-flex align-items-center">
                                    <i class="fa fa-fw fa-user-plus icon"></i>
                                    <span class="text">Registrarse</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif

                <script>
                    function closeDropdown() {
                        var dropdownMenu = document.querySelector('.dropdown-menu');
                        dropdownMenu.classList.remove('show');
                    }
                </script>

                <style>
                    .oval-button {
                        background-color: white;
                        border: 2px solid #94c93b;
                        border-radius: 30px;
                        padding: 5px 10px;
                    }

                    .oval-button:hover {
                        background-color: #94c93b;
                    }

                    .oval-button .icon {
                        color: #94c93b;
                        transition: color 0.3s ease;
                        margin-right: 5px;
                    }

                    .oval-button:hover .icon {
                        color: white;
                    }

                    .oval-button .text {
                        color: #94c93b;
                        transition: color 0.3s ease;
                        font-size: 12px;
                    }

                    .oval-button:hover .text {
                        color: white;
                    }

                    .register-button {
                        background-color: white;
                        border: 2px solid #faa625;
                        border-radius: 30px;
                        padding: 5px 10px;
                    }

                    .register-button:hover {
                        background-color: #faa625;
                    }

                    .register-button .icon {
                        color: #faa625;
                        transition: color 0.3s ease;
                        margin-right: 5px;
                    }

                    .register-button:hover .icon {
                        color: white;
                    }

                    .register-button .text {
                        color: #faa625;
                        transition: color 0.3s ease;
                        font-size: 12px;
                    }

                    .register-button:hover .text {
                        color: white;
                    }
                </style>
            </div>
            <style>
                .current-page {
                    color: #94c93b !important;
                }

                .nav-link:hover {
                    color: orange !important;
                }
            </style>

    </div>
</nav>
