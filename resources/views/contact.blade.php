@extends('layouts.main')

@section('content')
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
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d896.4176097083832!2d-63.17958094013224!3d-17.79711328369592!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x93f1e94b8fc3f891%3A0x4a993b74a09bfe46!2sGood%20Life%20Consultora%20de%20Pensiones%20y%20Prevision%20Social!5e0!3m2!1ses!2sbo!4v1709308947575!5m2!1ses!2sbo"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="card map-container">
                    <div class="card-body">
                        <h2 class="map-title txt5">Cochabamba</h2>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3807.6103352231644!2d-66.15769802901474!3d-17.38247352352944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x93e3740e5fa9ffe7%3A0xf8e0405c1c444843!2sGOOD%20LIFE%20SRL!5e0!3m2!1ses!2sbo!4v1709309165190!5m2!1ses!2sbo"
                            allowfullscreen></iframe>
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
                        const contactos = [{
                                nombre: "Lic. Jasmine",
                                numero: "59167409620"
                            },
                            {
                                nombre: "Lic. Eudal",
                                numero: "59167409620"
                            },
                            {
                                nombre: "Lic. Fernando",
                                numero: "59167409620"
                            }
                        ];

                        // Mostrar un cuadro de diálogo para que el usuario elija un contacto
                        let seleccion = prompt("Seleccione un contacto:\n\n1. " + contactos[0].nombre + "\n2. " + contactos[1].nombre +
                            "\n3. " + contactos[2].nombre);

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
                    <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)"
                        onmouseleave="slideDown(this)">
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
                    <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)"
                        onmouseleave="slideDown(this)">
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
                    <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)"
                        onmouseleave="slideDown(this)">
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
                    <div class="team position-relative overflow-hidden mb-5" onmouseover="slideUp(this)"
                        onmouseleave="slideDown(this)">
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
@endsection
