<nav class="navbar navbar-expand-lg navbar-light shadow fixed-top" style="background-color: #e2e2e1;"> 
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-success logo h1 align-self-center" href="welcome">
            <a href="welcome" class="navbar-brand ml-lg-3">
                @if(now()->month === 12)
                    <img src="assets/img/logonavidad.png" style="width: 80px; height: 60px;" alt="">
                @else
                    <img src="assets/img/logo.png" style="width: 120px; height: 60px;" alt="">
                @endif
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
            <div class="flex-fill">
                <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('welcome') ? 'current-page' : '' }}" href="welcome">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('sobrenosotros') ? 'current-page' : '' }}" href="sobrenosotros">Sobre nosotros</a>
                    </li>
                    <li class="nav-item">
                        <div class="dropdown" onmouseleave="closeDropdown()">
                            <a href="#" class="nav-link dropdown-toggle {{ Request::is('asesoramientolegal','medicina') ? 'current-page' : '' }}" id="dropdownMenuLink" data-toggle="dropdown">Servicios</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item nav-link {{ Request::is('asesoramientolegal') ? 'current-page' : '' }}" href="asesoramientolegal">Asesoramiento Legal</a>
                                <a class="dropdown-item nav-link {{ Request::is('medicina') ? 'current-page' : '' }}" href="medicina">Medicina</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('contact') ? 'current-page' : '' }}" href="contact">Contactos</a>
                    </li>
                </ul>
            </div>
            @if (Route::has('login'))
                <div class="d-flex justify-content-end">
                    @auth
                        <a href="{{ url('/home') }}" class="btn oval-button text-decoration-none d-flex align-items-center mr-2" title="Inicio">
                            <i class="fa fa-fw fa-home icon"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn oval-button text-decoration-none d-flex align-items-center mr-2">
                            <i class="fa fa-fw fa-user icon"></i>
                            <span class="text">Iniciar sesión</span>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn register-button text-decoration-none d-flex align-items-center">
                                <i class="fa fa-fw fa-user-plus icon"></i>
                                <span class="text">Registrarse</span>
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>
</nav>
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
    .current-page {
        color: #94c93b !important;
    }
    .nav-link:hover {
        color: orange !important;
    }
</style>
@if(now()->month === 12)
    <script src="https://cdn.jsdelivr.net/gh/scottschiller/Snowstorm/snowstorm-min.js"></script>
    <script>
        snowStorm.excludeMobile = false;
        // COLOR
        snowStorm.snowColor = '#ffffff';

        // CANTIDAD
        snowStorm.flakesMax = 200;
        snowStorm.flakesMaxActive = 100;
        snowStorm.snowStick = false;

        // SOLO CAÍDA VERTICAL
        snowStorm.vMaxX = 0;
        snowStorm.vMinX = 0;
        snowStorm.vMaxY = 2;
        snowStorm.vMinY = 2;

        // TAMAÑO
        snowStorm.flakesMinSize = 12;
        snowStorm.flakesMaxSize = 28;

        // EFECTOS
        snowStorm.useTwinkleEffect = true;
        snowStorm.followMouse = false;
        snowStorm.freezeOnBlur = false;
        snowStorm.zIndex = 9999;
    </script>
@endif