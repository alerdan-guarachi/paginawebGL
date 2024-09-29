@extends('layouts.main')

@section('content')

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

@endsection