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
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
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
                            <a class="nav-link" href="welcome">Inicio</a>
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
                            <a class="nav-link current-page" href="contact">Contactos</a>
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

    <!-- Modal -->
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


    <!-- Contactos -->
    <div class="container text-center py-5" style="margin-bottom: -31px;">
        <h3 class="display-3 mb-1 txt2" style="margin-top: 40px;"><b>CONTACTOS</b></h3>
    </div>

    <div class="container">
        <div class="row text-center pt-1 pb-3">
            <div class="col-lg-6 m-auto">
                <h1 class="txt3">⧻ Encuéntranos ⧻</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="card map-container">
                    <div class="card-body">
                        <h2 class="map-title txt5">Santa Cruz</h2>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d896.4176097083832!2d-63.17958094013224!3d-17.79711328369592!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x93f1e94b8fc3f891%3A0x4a993b74a09bfe46!2sGood%20Life%20Consultora%20de%20Pensiones%20y%20Prevision%20Social!5e0!3m2!1ses!2sbo!4v1709308947575!5m2!1ses!2sbo" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="card map-container">
                    <div class="card-body">
                        <h2 class="map-title txt5">Cochabamba</h2>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3807.6103352231644!2d-66.15769802901474!3d-17.38247352352944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x93e3740e5fa9ffe7%3A0xf8e0405c1c444843!2sGOOD%20LIFE%20SRL!5e0!3m2!1ses!2sbo!4v1709309165190!5m2!1ses!2sbo" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)" onmouseleave="slideDown(this)">
                <img class="img-fluid" src="assets/img/asesoramiento/jubilacion.png" alt="">
                <div class="position-relative text-center">
                    <div class="team-text text-white pie">
                        <h5 class="text-white text-uppercase">Área prestaciones</h5>
                    </div>
                </div>
                <div class="button-container">
                    <a href="#" class="btn btn-success" onclick="openWhatsApp()">Contactar por WhatsApp</a>
                </div>
            </div>
            
            <script>
                function openWhatsApp() {
                    // Array de números de WhatsApp con sus respectivos nombres
                    const contactos = [
                        { nombre: "Lic. Jasmine", numero: "59167409620" },
                        { nombre: "Lic. Eudal", numero: "59167409620" },
                        { nombre: "Lic. Fernando", numero: "59167409620" }
                    ];
            
                    // Mostrar un cuadro de diálogo para que el usuario elija un contacto
                    let seleccion = prompt("Seleccione un contacto:\n\n1. " + contactos[0].nombre + "\n2. " + contactos[1].nombre + "\n3. " + contactos[2].nombre);
            
                    // Redireccionar a WhatsApp con el número seleccionado
                    if (seleccion === "1") {
                        window.location.href = "https://wa.me/" + contactos[0].numero;
                    } else if (seleccion === "2") {
                        window.location.href = "https://wa.me/" + contactos[1].numero;
                    } else if (seleccion === "3") {
                        window.location.href = "https://wa.me/" + contactos[2].numero;
                    } else {
                        alert("Selección no válida.");
                    }
                }
            </script> -->
<div class="container-fluid py-1">
    <div class="container pt-3">
        <div class="col-lg-6 m-auto">
            <h1 class="txt3" style="margin-top: 70px;">⧻ Contáctanos ⧻</h1>
        </div>
        <div class="row justify-content-center text-center">
            <div class="col-lg-6">
                <h6 class="txt5">Santa Cruz</h6>
            </div>
        </div>
        <div class="row justify-content-center">
            
            <div class="col-lg-3 col-md-6 mx-4">
                <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)" onmouseleave="slideDown(this)">
                    <img class="img-fluid" src="assets/img/asesoramiento/1.png" alt="">
                    <div class="position-relative text-center">
                        <div class="team-text text-white pie">
                            <h5 class="text-white text-uppercase">Área prestaciones</h5>
                        </div>
                    </div>
                    <div class="button-container">
                        <a href="https://wa.me/59167409620" target="_blank" class="btn btn-succes">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:scz.prestaciones2@goodlife.com.bo" class="btn btn-succes">
                            <i class="far fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mx-4">
                <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)" onmouseleave="slideDown(this)">
                    <img class="img-fluid" src="assets/img/medicina/2.png" alt="">
                    <div class="position-relative text-center">
                        <div class="team-text text-white pie2">
                            <h5 class="text-white text-uppercase">Área médica</h5>
                        </div>
                    </div>
                    <div class="button-container">>
                        <a href="https://wa.me/59165045401" target="_blank" class="btn btn-succes">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:fabricio.prado@goodlife.com.bo" class="btn btn-succes">
                            <i class="far fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container pt-3">
        <div class="row justify-content-center text-center" style="margin-top: -50px">
            <div class="col-lg-6">
                <h6 class="txt5">Cochabamba</h6>
            </div>
        </div>
        <div class="row justify-content-center">

            <div class="col-lg-3 col-md-6 mx-4"> 
                <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)" onmouseleave="slideDown(this)">
                    <img class="img-fluid" src="assets/img/asesoramiento/1.png" alt="">
                    <div class="position-relative text-center">
                        <div class="team-text text-white pie">
                            <h5 class="text-white text-uppercase">Área prestaciones</h5>
                        </div>
                    </div>
                    <div class="button-container">
                        <a href="https://wa.me/59172222960" target="_blank" class="btn btn-succes">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:h.prestaciones@goodlife.com.bo" class="btn btn-succes">
                            <i class="far fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mx-4">
                <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)" onmouseleave="slideDown(this)">
                    <img class="img-fluid" src="assets/img/medicina/2.png" alt="">
                    <div class="position-relative text-center">
                        <div class="team-text text-white pie2">
                            <h5 class="text-white text-uppercase">Área médica</h5>
                        </div>
                    </div>
                    <div class="button-container">
                        <a href="https://wa.me/59165045401" target="_blank" class="btn btn-succes">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:fabricio.prado@goodlife.com.bo" class="btn btn-succes">
                            <i class="far fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <li><a class="text-decoration-none" href="welcome">Inicio</a></li>
                        <li><a class="text-decoration-none" href="sobrenosotros">Sobre nosotros</a></li>
                        <li><a class="text-decoration-none" href="asesoramientolegal">Servicio de asesoramiento legal</a></li>
                        <li><a class="text-decoration-none" href="medicina">Servicio de medicina</a></li>
                        <li><a class="text-decoration-none current-page" href="contact">Contactos</a></li>
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
</body>
</html>

<script>
function showButton(element) {
    element.querySelector('.button-container').style.opacity = '1';
}

function hideButton(element) {
    element.querySelector('.button-container').style.opacity = '0';
}
function closeDropdown() {
        var dropdownMenu = document.querySelector('.dropdown-menu');
        dropdownMenu.classList.remove('show');
    }
</script>