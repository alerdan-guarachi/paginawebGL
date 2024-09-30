@extends('layouts.main')

@section('content')
    <!-- Banner -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="q"
                        placeholder="Search ...">
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
                                    En GOOD LIFE S.R.L. nos dedicamos a cuidar de tu bienestar y asegurar tu futuro
                                    financiero.
                                    Descubre nuestros servicios médicos y asesoría en la ley de pensiones para una vida
                                    plena y segura.
                                    Explora nuestra página web y conoce más sobre nuestro equipo de profesionales
                                    apasionados y dedicados a tu atención.
                                    Estamos aquí para escucharte y responder a tus necesidades de manera personalizada. ¡Tu
                                    bienestar es nuestra prioridad!
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

    <!-- Estilos para el banner -->
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
                padding: 10px;
                /* Reducir el espacio interno de la fila en dispositivos móviles */
            }

            .col-lg-6.mb-4.mb-lg-0.mb-2.text-center {
                text-align: center;
                /* Centrar el logo en dispositivos móviles */
            }

            .col-lg-6.mb-4.mb-lg-0.mb-2.text-center img {
                max-width: 70%;
                /* Reducir el tamaño del logo en dispositivos móviles */
                margin-top: -180px;
                /* Ajustar el margen superior del logo en dispositivos móviles */
            }

            .col-lg-6 {
                padding: 10px;
                /* Añadir un espacio interno en la columna de texto en dispositivos móviles */
            }

            .tex2,
            .tex1,
            .txt1,
            .tes1 {
                font-size: 15px;
                /* Reducir el tamaño del texto en dispositivos móviles */
                line-height: 1.5;
                /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
            }

            .text-overlay2 {
                font-size: 25px;
                /* Reducir el tamaño del texto en dispositivos móviles */
                line-height: 1.5;
                /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
                text-align: center;
            }

            .text-overlay {
                font-size: 30px;
                /* Reducir el tamaño del texto en dispositivos móviles */
                line-height: 1.5;
                /* Ajustar el espaciado entre líneas del texto en dispositivos móviles */
                text-align: center;
            }

            .bg-overlay {
                height: 100vh;
                /* Ajusta la altura del banner para dispositivos móviles */
            }

        }

        /* Estilos para dispositivos medianos y grandes */
        @media (min-width: 576px) {
            .carousel-item {
                margin-top: 120px !important;
                /* Ajusta el margen superior para dispositivos medianos y grandes */
            }
        }

        .bg-overlay {
            background-image: url('assets/img/bn.png');
            /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            height: 100vh;
            margin-top: -3px;
            margin-bottom: -40px;
            /* Ajustar margen inferior */
            background-color: transparent;
        }

        .bg-overlay::before {
            content: '';
            background: rgba(0, 0, 0, 0.6);
            /* Color de fondo oscurecido con opacidad del 50% */
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .carousel-item.active .bg-overlay::before {
            z-index: 0;
            /* Asegura que el fondo oscurecido esté detrás del contenido */
        }

        .carousel-item.active img {
            position: relative;
            /* Asegura que la imagen esté por encima del fondo oscurecido */
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
            z-index: 1;
            /* Asegura que el fondo oscurecido detrás de los textos esté encima del contenido */
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
                    <video class="embed-responsive-item custom-card-body custom-card-body1" autoplay muted loop
                        style="width: 100%; height: 100%;">
                        <source src="assets/mp4/rayosx.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i
                            class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txttt3">Rayos X y Espirometria</h5>
                <p class="text-center"><a class="btn btn-success1" href="medicina.html">Ver más</a></p>
            </div>


            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body custom-card-body1" autoplay muted loop
                        style="width: 100%; height: 100%;">
                        <source src="assets/mp4/psicologia.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i
                            class="fas fa-expand"></i></button>
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
                    <video class="embed-responsive-item custom-card-body2 custom-card-body2" autoplay muted loop
                        style="width: 100%; height: 100%;">
                        <source src="assets/mp4/vejez.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i
                            class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txtt3">Pensión de vejez</h5>
                <p class="text-center"><a class="btn btn-success11" href="asesoramientolegal.html">Ver más</a></p>

            </div>
            <div class="col-12 col-md-6 p-5 mt-3">
                <div class="rounded-circle embed-responsive embed-responsive-1by1 position-relative">
                    <video class="embed-responsive-item custom-card-body2 custom-card-body2" autoplay muted loop
                        style="width: 100%; height: 100%;">
                        <source src="assets/mp4/invalidez.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                    <button class="fullscreenButton btn position-absolute bottom-0 end-0 m-2"><i
                            class="fas fa-expand"></i></button>
                </div>
                <h5 class="text-center mt-3 mb-3 txtt3">Pensión por invalidez</h5>
                <p class="text-center"><a class="btn btn-success11" href="asesoramientolegal.html">Ver más</a></p>
            </div>

        </div>

        <!-- Estilos adicionales para los destacados -->

        <style>
            .custom-card-body {
                border-left: 15px solid #94c93b;
                /* Borde izquierdo */
                border-bottom: 7px solid #94c93b;
                /* Borde inferior */
                padding-bottom: 0px;
                /* Espacio inferior para el borde */
            }

            .custom-card-body1 {
                border-right: 15px solid #94c93b;
                /* Borde derecho */
                border-top: 7px solid #94c93b;
                /* Borde superior */
                padding-top: 0px;
                /* Espacio superior para el borde */
            }

            .custom-card-body2 {
                border-left: 15px solid #faa625;
                /* Borde izquierdo */
                border-bottom: 7px solid #faa625;
                /* Borde inferior */
                padding-bottom: 0px;
                /* Espacio inferior para el borde */
            }

            .custom-card-body2 {
                border-right: 15px solid #faa625;
                /* Borde derecho */
                border-top: 7px solid #faa625;
                /* Borde superior */
                padding-top: 0px;
                /* Espacio superior para el borde */
            }

            .btn-success1 {
                background-color: #94c93b;
                /* Color verde */
                color: #fff;
                /* Color del texto */
                /* Otros estilos opcionales */
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
            }

            /* Estilos adicionales para el botón al pasar el cursor */
            .btn-success1:hover {
                background-color: #faa625;
                /* Color verde más oscuro al pasar el cursor */
            }

            .btn-success11 {
                background-color: #faa625;
                /* Color verde */
                color: #fff;
                /* Color del texto */
                /* Otros estilos opcionales */
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
            }

            /* Estilos adicionales para el botón al pasar el cursor */
            .btn-success11:hover {
                background-color: #94c93b;
                /* Color verde más oscuro al pasar el cursor */
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
    </section>

    <script>
        document.querySelectorAll(".fullscreenButton").forEach(button => {
            button.addEventListener("click", () => {
                const videoPlayer = button.parentElement.querySelector("video");
                if (videoPlayer.requestFullscreen) {
                    videoPlayer.requestFullscreen();
                } else if (videoPlayer.mozRequestFullScreen) {
                    /* Firefox */
                    videoPlayer.mozRequestFullScreen();
                } else if (videoPlayer.webkitRequestFullscreen) {
                    /* Chrome, Safari and Opera */
                    videoPlayer.webkitRequestFullscreen();
                } else if (videoPlayer.msRequestFullscreen) {
                    /* IE/Edge */
                    videoPlayer.msRequestFullscreen();
                }
            });
        });
    </script>

    <style>
        .custom-carousel-card {
            width: 350px;
            /* Tamaño del card */
            margin: 0 auto;
            /* Centrar horizontalmente */
            margin-bottom: 10px;
            /* Espacio entre los cards */
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
            margin-bottom: 0;
            /* Elimina el margen inferior */
        }

        /* Estilos adicionales para pantallas grandes */
        @media (min-width: 992px) {
            .custom-carousel-column {
                margin-top: 20px;
                /* Ajustar margen superior en pantallas grandes */
            }
        }
    </style>
    <!-- Script para activar el carrusel -->
    <script>
        $(document).ready(function() {
            $('#custom-carousel1, #custom-carousel2').carousel({
                interval: 2000 // Cambia esta duración para ajustar el intervalo entre las transiciones de las imágenes (en milisegundos)
            });
        });
    </script>
@endsection
