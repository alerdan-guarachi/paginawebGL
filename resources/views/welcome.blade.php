<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body>
    @include('encabezado')

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
                                @if(now()->month === 12)
                                    <img class="img-fluid" src="assets/img/logonavidad.png" alt="">
                                @else
                                    <img class="img-fluid" src="assets/img/logo.png" alt="">
                                @endif
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

    @include('piepagina')

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
