<!DOCTYPE html>
<html lang="en">

<head>
    <title>GOOD LIFE S.R.L.</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/img/logo.png" rel="icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/estilonuevo.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <!-- Barra de navegacion -->
    <nav class="navbar navbar-expand-lg navbar-light shadow fixed-top" style="background-color: #e2e2e1;"> 
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-success logo h1 align-self-center" href="welcome">
                <a href="welcome" class="navbar-brand ml-lg-3">
                    <img src="assets/img/logo.png" style="width: 120px; height: 60px;" alt="">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link current-page" href="welcome">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobrenosotros">Sobre nosotros</a>
                        </li>
                        
                        <li class="nav-item">
                            <div class="dropdown" onmouseleave="closeDropdown()">
                                <a href="#" class="nav-link dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown">Servicios</a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item nav-link" href="asesoramientolegal">Asesoramiento Legal</a>
                                    <a class="dropdown-item nav-link" href="medicina">Medicina</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact">Contactos</a>
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
            @endif
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


    <!-- Banner -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="q" placeholder="Search ...">
                    <button type="submit" class="input-group-text bg-success text-light">
                        <i class="fa fa-fw fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

        <div id="template-mo-zay-hero-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="bg-overlay">
                        <div class="container" style="margin-top: -50px;">
                            <div class="row align-items-center p-3 p-md-5">
                                <div class="col-lg-6 mb-4 mb-lg-0 mb-2 text-center text-lg-left" style="margin-top: 130px;">
                                    <h1 class="text-overlay2 mb-2">BIENVENIDOS A</h1>
                                    <h1 class="text-overlay mb-2"><b>GOOD LIFE S.R.L.</b></h1>
                                    <h1 class="tes1">
                                        En GOOD LIFE S.R.L. nos dedicamos a cuidar de tu bienestar y asegurar tu futuro financiero.
                                        Descubre nuestros servicios médicos y asesoría en la ley de pensiones para una vida plena y segura.
                                        Explora nuestra página web y conoce más sobre nuestro equipo de profesionales apasionados y dedicados a tu atención.
                                        Estamos aquí para escucharte y responder a tus necesidades de manera personalizada. ¡Tu bienestar es nuestra prioridad!
                                    </h1>
                                </div>
                                <div class="col-lg-6 mb-4 mb-lg-0 mb-2 text-center" style="margin-top: 100px;">
                                    <img class="img-fluid" src="assets/img/logo.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<style>
    .tes1 {
        font-family: "Latin Modern Roman";
        font-size: 20px;
        line-height: 1.6;
        text-align: justify;
        position: relative;
        z-index: 1;
        color: white;
        margin-top: 50px;
    }
    .text-overlay {
    position: relative;
    z-index: 1;
    color: #94c93b;
    text-align: left;
    font-size: 43px;
    font-family: "Segoe UI";
    }
    .text-overlay2 {
        position: relative;
        z-index: 1;
        color: #94c93b;
        text-align: left;
        font-weight: 200;
        font-size: 35px;
        font-family: "Segoe UI";
    }

    @media (max-width: 576px) {
        .row.align-items-center.p-3.p-md-5 {
            padding: 10px; /* Reducir el espacio interno de la fila en dispositivos móviles */
        }
        .col-lg-6.mb-4.mb-lg-0.mb-2.text-center {
            text-align: center; /* Centrar el logo en dispositivos móviles */
        }
        .col-lg-6.mb-4.mb-lg-0.mb-2.text-center img {
            max-width: 70%; /* Reducir el tamaño del logo en dispositivos móviles */
            margin-top: -180px; /* Ajustar el margen superior del logo en dispositivos móviles */
        }
        .col-lg-6 {
            padding: 10px; /* Añadir un espacio interno en la columna de texto en dispositivos móviles */
        }
        .tex2, .tex1, .txt1, .tes1{
            font-size: 15px; /* Reducir el tamaño del texto en dispositivos móviles */
            line-height: 1.5; /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
        }
        .text-overlay2{
            font-size: 25px; /* Reducir el tamaño del texto en dispositivos móviles */
            line-height: 1.5; /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
            text-align: center;
        }
        .text-overlay{
            font-size: 30px; /* Reducir el tamaño del texto en dispositivos móviles */
            line-height: 1.5; /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
            text-align: center;
        }
        .bg-overlay {
            height: 100vh; /* Ajusta la altura del banner para dispositivos móviles */
        }
        
        }
        /* Estilos para dispositivos medianos y grandes */
        @media (min-width: 576px) {
            .carousel-item {
                margin-top: 120px !important; /* Ajusta el margen superior para dispositivos medianos y grandes */
            }
        }
        .bg-overlay {
            background-image: url('assets/img/bn.png'); /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            height: 100vh;
            margin-top: -3px;
            margin-bottom: -40px; /* Ajustar margen inferior */
            background-color: transparent;
        }

        .bg-overlay::before {
            content: '';
            background: rgba(0, 0, 0, 0.6); /* Color de fondo oscurecido con opacidad del 50% */
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .carousel-item.active .bg-overlay::before {
            z-index: 0; /* Asegura que el fondo oscurecido esté detrás del contenido */
        }

        .carousel-item.active img {
            position: relative; /* Asegura que la imagen esté por encima del fondo oscurecido */
            z-index: 1;
        }
        /* Pseudo-elemento para el fondo oscurecido detrás de los textos */
        .bg-overlay::after {
            content: '';
        /* Reducimos la opacidad del fondo oscurecido detrás de los textos */
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .carousel-item.active .bg-overlay::after {
            z-index: 1; /* Asegura que el fondo oscurecido detrás de los textos esté encima del contenido */
        }
</style>        
       
    <!-- Destacados -->
    <section class="container py-5">
        <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="txt3">⧻ Destacados ⧻</h1>
            </div>
        </div>
        <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="txts3">Medicina</h1>
            </div>
        </div>
        <div class="row" style="margin-top: -50px;">
            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body custom-card-body1" autoplay muted loop style="width: 100%; height: 100%;">
                        <source src="assets/mp4/rayosx.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txttt3">Rayos X y Espirometria</h5>
                <p class="text-center"><a class="btn btn-success1" href="medicina.html">Ver más</a></p>
            </div>
        

            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body custom-card-body1" autoplay muted loop style="width: 100%; height: 100%;">
                        <source src="assets/mp4/psicologia.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txttt3">Psicologia</h5>
                <p class="text-center"><a class="btn btn-success1" href="medicina.html">Ver más</a></p>
            </div>
            
        </div>
        <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="txt3">Asesoramiento legal</h1>
            </div>
        </div>
        <div class="row" style="margin-top: -50px;">
            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body2 custom-card-body2" autoplay muted loop style="width: 100%; height: 100%;">
                        <source src="assets/mp4/vejez.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txtt3">Pensión de vejez</h5>
                <p class="text-center"><a class="btn btn-success11" href="asesoramientolegal.html">Ver más</a></p>
                
            </div>
            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body2 custom-card-body2" autoplay muted loop style="width: 100%; height: 100%;">
                        <source src="assets/mp4/invalidez.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txtt3">Pensión por invalidez</h5>
                <p class="text-center"><a class="btn btn-success11" href="asesoramientolegal.html">Ver más</a></p>
            </div>
            
        </div>
        
        <style>
            .custom-card-body {
                border-left: 15px solid #94c93b; /* Borde izquierdo */
                border-bottom: 7px solid #94c93b; /* Borde inferior */
                padding-bottom: 0px; /* Espacio inferior para el borde */
            }

            .custom-card-body1 {
                border-right: 15px solid #94c93b; /* Borde derecho */
                border-top: 7px solid #94c93b; /* Borde superior */
                padding-top: 0px; /* Espacio superior para el borde */
            }
            .custom-card-body2 {
                border-left: 15px solid #faa625; /* Borde izquierdo */
                border-bottom: 7px solid #faa625; /* Borde inferior */
                padding-bottom: 0px; /* Espacio inferior para el borde */
            }

            .custom-card-body2 {
                border-right: 15px solid #faa625; /* Borde derecho */
                border-top: 7px solid #faa625; /* Borde superior */
                padding-top: 0px; /* Espacio superior para el borde */
            }
            .btn-success1 {
                background-color: #94c93b; /* Color verde */
                color: #fff; /* Color del texto */
                /* Otros estilos opcionales */
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
            }
    
            /* Estilos adicionales para el botón al pasar el cursor */
            .btn-success1:hover {
                background-color: #faa625; /* Color verde más oscuro al pasar el cursor */
            }
            .btn-success11 {
                background-color:  #faa625; /* Color verde */
                color: #fff; /* Color del texto */
                /* Otros estilos opcionales */
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
            }
    
            /* Estilos adicionales para el botón al pasar el cursor */
            .btn-success11:hover {
                background-color: #94c93b; /* Color verde más oscuro al pasar el cursor */
            }
            .txts3 {
                font-family: "Latin Modern Roman";
                font-size: 30px;
                line-height: 1.6;
                text-align: center;
                color: #94c93b;
            }
            .txtt3 {
                font-family: "Latin Modern Roman";
                font-size: 25px;
                line-height: 1.6;
                text-align: center;
                color: #faa625;
            }
            .txttt3 {
                font-family: "Latin Modern Roman";
                font-size: 25px;
                line-height: 1.6;
                text-align: center;
                color: #94c93b;
            }
         </style>
        <!-- <div class="row text-center pt-3">
            <div class="col-lg-6 m-auto">
                <h1 class="txt3">⧻ Promociones ⧻</h1>
            </div>
        </div>
        <div class="carousel-container4 custom-card-body11" id="carouselContainer4" style="margin-bottom: 20px; margin-top: 30px;">
            <div class="carousel4" id="carousel4">
                <img src="assets/img/promocion/estudios/AUD3EN1.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/AUDOSEA.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/AUDTONAL.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/AUDVOCAL.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/CAMPI.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/electro.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/ENCEFALO.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/ESPIROCONBR.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/ESPIROSINBR.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/LAVADODEOIDOSINBR.png" alt="Imagen 1">
                <img src="assets/img/promocion/estudios/MEDVISTA.png" alt="Imagen 1">
            </div>
        </div> -->
        <style>
            .custom-card-body11 {
                border-left: 20px solid #faa625; /* Borde izquierdo */
                border-right: 20px solid #faa625; /* Borde inferior */
            }
            .carousel-container4 {
                width: 100%;
                height: 45vh; /* Tamaño completo de la ventana */
                overflow: hidden; /* Ocultar el desbordamiento horizontal */
                position: relative;
            }
            .carousel4 {
                display: flex; /* Utilizar flexbox para alinear las imágenes */
                position: absolute;
            }
            .carousel4 img {
                width: 360px; /* Tamaño de las imágenes */
                height: 360px;
                margin-right: 20px; /* Espacio entre las imágenes */
                object-fit: cover; /* Escalar las imágenes para llenar el contenedor sin deformar */
            }
        </style>
        <script>
            const carouselContainer = document.getElementById('carouselContainer4');
            const carousel = document.getElementById('carousel4');
            const images = carousel.querySelectorAll('img');
            const carouselWidth = carousel.offsetWidth;
            let isPaused = false;
            let intervalId;
        
            // Clonar las imágenes para crear un bucle infinito
            images.forEach(image => {
                const clone = image.cloneNode(true);
                carousel.appendChild(clone);
            });
        
            // Animar el desplazamiento
            let position = 0;
            intervalId = setInterval(() => {
                if (!isPaused) {
                    position -= 0.5; // Ajusta la velocidad del desplazamiento
                    carousel.style.transform = `translateX(${position}px)`;
                    if (Math.abs(position) >= carouselWidth) {
                        position = 0;
                    }
                }
            }, 23); // Ajusta el intervalo de tiempo para un desplazamiento más lento
        
            // Pausar o reanudar el desplazamiento al colocar el cursor o tocar una imagen
            images.forEach(image => {
                image.addEventListener('mouseenter', () => {
                    isPaused = true;
                    clearInterval(intervalId);
                });
        
                image.addEventListener('mouseleave', () => {
                    isPaused = false;
                    intervalId = setInterval(() => {
                        if (!isPaused) {
                            position -= 0.5;
                            carousel.style.transform = `translateX(${position}px)`;
                            if (Math.abs(position) >= carouselWidth) {
                                position = 0;
                            }
                        }
                    }, 20);
                });
        
                // Manejar eventos táctiles
                image.addEventListener('touchstart', () => {
                    isPaused = true;
                    clearInterval(intervalId);
                });
        
                image.addEventListener('touchend', () => {
                    isPaused = false;
                    intervalId = setInterval(() => {
                        if (!isPaused) {
                            position -= 0.5;
                            carousel.style.transform = `translateX(${position}px)`;
                            if (Math.abs(position) >= carouselWidth) {
                                position = 0;
                            }
                        }
                    }, 20);
                });
            });
        </script>
        
        
    </section>
    <script>
        document.querySelectorAll(".fullscreenButton").forEach(button => {
    button.addEventListener("click", () => {
        const videoPlayer = button.parentElement.querySelector("video");
        if (videoPlayer.requestFullscreen) {
            videoPlayer.requestFullscreen();
        } else if (videoPlayer.mozRequestFullScreen) { /* Firefox */
            videoPlayer.mozRequestFullScreen();
        } else if (videoPlayer.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
            videoPlayer.webkitRequestFullscreen();
        } else if (videoPlayer.msRequestFullscreen) { /* IE/Edge */
            videoPlayer.msRequestFullscreen();
        }
    });
});
    </script>

  <style>
    .custom-carousel-card {
      width: 350px; /* Tamaño del card */
      margin: 0 auto; /* Centrar horizontalmente */
      margin-bottom: 10px; /* Espacio entre los cards */
    }
    .custom-carousel-inner img {
      width: 100%;
      height: auto;
    }
    .custom-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      color: #fff;
      cursor: pointer;
    }
    .custom-arrow-left {
      left: 10px;
    }
    .custom-arrow-right {
      right: 10px;
    }
    /* Nuevos estilos para ajustar el margen inferior del encabezado */
    .custom-carousel-title {
      margin-bottom: 0; /* Elimina el margen inferior */
    }
    /* Estilos adicionales para pantallas grandes */
    @media (min-width: 992px) {
      .custom-carousel-column {
        margin-top: 20px; /* Ajustar margen superior en pantallas grandes */
      }
    }
  </style>
  <!-- Script para activar el carrusel -->
  <script>
    $(document).ready(function(){
      $('#custom-carousel1, #custom-carousel2').carousel({
        interval: 2000 // Cambia esta duración para ajustar el intervalo entre las transiciones de las imágenes (en milisegundos)
      });
    });
  </script>

    <!-- Pie de pagina -->
    <footer class="bg-black" id="tempaltemo_footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 pt-5">
                    <h2 class="h2 border-bottom pb-3 border-light logo txt6">Santa Cruz</h2>
                    <ul class="list-unstyled text-light custom-footer-links">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw"></i>
                            <a class="text-decoration-none" href="https://maps.app.goo.gl/9YcukYKG32Kg2NeX6">Av. Rene Moreno N° 484 Esq. Ana Barba entre 1er y 2do anillo - Barrio Sur</a>
                        </li>
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none" href="tel:65045401">65045401</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none" href="mailto:scz.prestaciones2@goodlife.com.bo?subject=Asunto&body=Cuerpo del mensaje&app=OUTLOOK">scz.prestaciones2@goodlife.com.bo</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 pt-5">
                    <h2 class="h2 border-bottom pb-3 border-light logo txt6">Cochabamba</h2>
                    <ul class="list-unstyled text-light custom-footer-links">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw" ></i> 
                            <a class="text-decoration-none" href="https://maps.app.goo.gl/US5TiFQpvk7gAh346">Calle Lanza entre R. Rivero y Oruro Edif. Shashelly piso 2 of. 2B</a>
                        </li>
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none" href="tel:65045401">65045401</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none" href="mailto:cbbaprestaciones@goodlife.com.bo?subject=Asunto&body=Cuerpo del mensaje&app=OUTLOOK">cbbaprestaciones@goodlife.com.bo</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-light border-bottom pb-3 border-light">Menu</h2>
                    <ul class="list-unstyled text-light custom-footer-links">
                        <li><a class="text-decoration-none current-page" href="welcome">Inicio</a></li>
                        <li><a class="text-decoration-none" href="sobrenosotros">Sobre nosotros</a></li>
                        <li><a class="text-decoration-none" href="asesoramientolegal">Servicio de asesoramiento legal</a></li>
                        <li><a class="text-decoration-none" href="medicina">Servicio de medicina</a></li>
                        <li><a class="text-decoration-none" href="contact">Contactos</a></li>
                    </ul>
                </div>


            </div>

            <div class="row text-light mb-4">
                <div class="col-12 mb-3">
                    <div class="w-100 my-3 border-top border-light"></div>
                </div>
                <div class="col-auto me-auto">
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.facebook.com/Good.Life.Consultora.de.Pensiones/"><i class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.instagram.com/goodlife_srl/"><i class="fab fa-instagram fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.youtube.com/@goodlifesrlpensionesysalud5514"><i class="fab fa-youtube fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.tiktok.com/@good_life_srl"><i class="fab fa-tiktok fa-lg fa-fw"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="w-100 py-3 footerend">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="m-0 text-black">&copy; <a class="h5 logo text-dark text-decoration-none">GOOD LIFE</a>. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <script src="assets/js/jquery-1.11.0.min.js"></script>
    <script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/templatemo.js"></script>
    <script src="assets/js/custom.js"></script>
         
    <script>
        function closeDropdown() {
            var dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.classList.remove('show');
        }
    </script>
</body>
<style>
    .txt1 {
        font-family: "Latin Modern Roman";
        font-size: 17px;
        line-height: 1.6;
        text-align: justify;
    }
</style>
</html>
