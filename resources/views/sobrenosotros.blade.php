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
    @include('encabezado')

    <!-- Sobre nosotros -->
    <div class="container text-center py-5" style="margin-bottom: -31px;">
        <h3 class="display-3 mb-1 txt2" style="margin-top: 70px;"><b>SOBRE NOSOTROS</b></h3>
    </div>
    
    <section>
        <div class="container">
            <div class="row justify-content-center align-items-stretch" style="margin-top: 40px;">
                <div class="col-md-4 col-lg-4 pb-5 text-center vision-section">
                    <div class="h-100 d-flex flex-column">
                        <div class="card-body custom-card-body">
                            <div class="col-lg-12">
                                <h1 class="txt3 mb-4">⧻ Visión ⧻</h1>
                            </div>
                            <h1 class="txt1" style="margin-top: 45px;">
                                Ser una de las principales empresas líderes en Bolivia en la mejora de la calidad de vida de los colaboradores, incremento de la eficiencia del potencial humano de las organizaciones y haber generado un compromiso social empresarial que se traduce en un impacto positivo para los trabajadores, empleadores y Bolivia.
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 pb-5 text-center" style="margin-right: -80px; margin-left: -80px;">
                    <a href="shop-single.html">
                        <img src="assets/img/ia.png" class="img-fluid mx-auto d-block" style="max-width: 90%; height: auto;" alt="...">
                    </a>
                </div>
                
                
                <div class="col-md-4 col-lg-4 pb-5 text-center mision-section">
                    <div class="h-100 d-flex flex-column">
                        <div class="card-body custom-card-body1">
                            <div class="col-lg-12">
                                <h1 class="txt7 mb-4">⧻ Misión ⧻</h1>
                            </div>
                            <h1 class="txt1">
                                Apoyar a las organizaciones en la búsqueda del mejoramiento continuo de la calidad de vida de los trabajadores, mediante la oferta de servicios eficientes en Salud Ocupacional, Medicina Ocupacional, Seguridad e Higiene Ocupacional, Prevención en Salud, Ergonomía y Psicosociología laboral, promoviendo un concepto integral y social de la salud, impactando positivamente en el bienestar y salud de los trabajadores, optimizando su desempeño, mejorando su eficiencia laboral y la productividad de sus organizaciones.
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        @keyframes pulsate {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.08);
        }
        100% {
            transform: scale(1);
        }
        }

        .pulsate {
        animation: pulsate 3s infinite;
        }
        .custom-card-body {
            border-left: 15px solid #faa625; /* Borde izquierdo */
            border-bottom: 3px solid #faa625; /* Borde inferior */
            padding-bottom: 20px; /* Espacio inferior para el borde */
        }

        .custom-card-body1 {
            border-right: 15px solid #94c93b; /* Borde derecho */
            border-bottom: 3px solid #94c93b; /* Borde inferior */
            padding-bottom: 20px; /* Espacio inferior para el borde */
        }
        .txt7 {
            font-family: "Latin Modern Roman";
            font-size: 30px;
            line-height: 1.6;
            text-align: center;
            color: #94c93b;
        }
    </style>

    <!-- Valores -->
    <section class="container">
        <div class="row text-center pt-5 pb-5">
            <div class="col-lg-6 m-auto">
                <h1 class="txt3">⧻ Valores ⧻</h1>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 iconovalores text-center"><i class="fa fa-graduation-cap fa-lg"></i></div>
                    <h2 class="mt-4 text4">Profesionalismo</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Actuar siempre de acuerdo con los valores de nuestras profesiones, poniendo al servicio de la comunidad la mejor atención y la mejor evidencia científica, velando por la calidad de los servicios y la seguridad de las personas.
                        </h1>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 iconovalores text-center"><i class="fa fa-cogs fa-lg"></i></div>
                    <h2 class="mt-4 text4">Gestión responsable</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Velar por una gestión basada en la transparencia y la gestión eficiente de los recursos, teniendo en cuenta los aspectos éticos de nuestras decisiones, y que se comprometa con la responsabilidad social corporativa.
                        </h1>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 text-center iconovalores"><i class="fa fa-sun fa-lg"></i></div>
                    <h2 class="mt-4 text4">Responsabilidad ambiental</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Comprometidos a operar de manera responsable y consciente con el medio ambiente, minimizando nuestra huella ecológica y promoviendo prácticas sostenibles en todas las operaciones.
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 iconovalores text-center"><i class="fas fa-users fa-lg"></i></div>
                    <h2 class="mt-4 text4">Trabajo en equipo</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Trabajar en cooperación para unos objetivos compartidos, buscando la participación, el compromiso para un objetivo en común.
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 iconovalores text-center"><i class="fa fa-star fa-lg"></i></div>
                    <h2 class="mt-4 text4">Innovación</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Crear y aplicar nuevas formas de hacer que impulsen la mejora continua.
                        </h1>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-3">
                    <div class="h1 iconovalores text-center"><i class="fa fa-medal fa-lg"></i></div>
                    <h2 class="mt-4 text4">Respeto</h2>
                    <div class="card-body">
                        <h1 class="card-text txt1">
                            Ofrecer un trato humano y empático a las personas, respetando sus derechos individuales y colectivos, su autonomía y su diversidad.
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .iconovalores {
            color: #94c93b;
        }
        .text4 {
            font-family: "Latin Modern Roman";
            font-size: 23px;
            line-height: 1.6;
            text-align: center;
            font-weight: 400;
            color: #94c93b;
        }
    </style>

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

</html>
