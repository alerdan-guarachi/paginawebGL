@extends('adminlte::page')

@section('title', 'Tarjeta Digital de Fabricio Prado')

@section('content')
<div class="d-flex justify-content-center">
    <div class="card tarjeta-digital shadow">

        <div class="text-center mt-3">
            <img src="{{ asset('img/logo.png') }}" class="foto-perfil">
        </div>

        <div class="text-center mt-3" style="margin-bottom: -30px;">
            <h2 class="nombre">Fabricio Orlando Prado Parrado</h2>
            <p class="cargo"><strong>Gerente General - Good Life S.R.L.</strong></p>
            <p class="subcargo">Consultora en Pensiones y Salud Ocupacional</p>
        </div>

        <div class="d-flex align-items-center mt-4 px-3 perfil-contacto">
            <div class="mr-3">
                <div class="foto-perfil-horizontal">
                    <img src="{{ asset('img/fotogerente.png') }}" style="width:100%; height:100%; object-fit:cover;">
                </div>
            </div>
            <div class="contacto">
                <p>
                    <a href="https://wa.me/59176477555" target="_blank">
                        <i class="fas fa-phone"></i> +591 76477555
                    </a>
                </p>
                <p>
                    <a href="mailto:fabricio.prado@goodlife.com.bo">
                        <i class="fas fa-envelope"></i> fabricio.prado@goodlife.com.bo
                    </a>
                </p>
                <p>
                    <i class="fas fa-leaf"></i> Good Life S.R.L.
                </p>
            </div>
        </div>

        <div class="servicios px-4 mt-3">
            <h6><strong>Servicios:</strong></h6>
            <ul>
                <li>Gestión de trámites sobre la ley de pensiones 065</li>
                <li>Gestión de trámites de Invalidez</li>
                <li>Salud e higiene ocupacional</li>
                <li>Red de especialistas médicos</li>
            </ul>
        </div>

        <div class="ubicacion px-4 mt-2">
            <p>
                <a href="https://maps.app.goo.gl/9YcukYKG32Kg2NeX6" target="_blank">
                    <i class="fas fa-map-marker-alt"></i> 
                    <strong>Santa Cruz, Bolivia:</strong> 
                    Av. Rene Moreno N° 484 Esq. Ana Barba entre 1er y 2do anillo - Barrio Sur
                </a>
            </p>
            <p>
                <a href="https://maps.app.goo.gl/US5TiFQpvk7gAh346" target="_blank">
                    <i class="fas fa-map-marker-alt"></i> 
                    <strong>Cochabamba, Bolivia:</strong> 
                    Calle Lanza entre R. Rivero y Oruro Edif. Shashelly piso 2 of. 2B
                </a>
            </p>
        </div>

        <div class="frase text-center px-4 mb-3">
            <i>"Cuidamos la salud y el bienestar de tu equipo con soluciones confiables."</i>
        </div>

        <div class="compartir text-center mt-2">
            <small class="text-muted d-block mb-1">Compartir por:</small>
            <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="https://wa.me/?text=https://goodlife.com.bo/digital-card" target="_blank" class="btn-share whatsapp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://t.me/share/url?url=https://goodlife.com.bo/digital-card" target="_blank" class="btn-share telegram">
                    <i class="fab fa-telegram"></i>
                </a>
                <a href="fb-messenger://share?link=https://goodlife.com.bo/digital-card" class="btn-share messenger">
                    <i class="fab fa-facebook-messenger"></i>
                </a>
                <a href="mailto:?subject=Tarjeta Digital Good Life&body=https://goodlife.com.bo/digital-card" class="btn-share correo">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    body {
        background: linear-gradient(135deg, #e6f4ea, #ffffff);
        font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    .btn-share {
        margin: 0 2px;
    }

    .tarjeta-digital {
        width: 100%;
        max-width: 450px;
        border-radius: 25px;
        background: linear-gradient(135deg, rgb(247, 255, 237), #fff6e7);
        overflow: hidden;
        padding: 20px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: 1px solid #eef2f1;
    }

    .ubicacion a {
        color: inherit;
        text-decoration: none;
        display: block;
    }

    .ubicacion a:hover {
        color: #1b3a2f;
    }

    .foto-perfil {
        width: 150px;
        height: 150px;
        object-fit: contain;
        margin-bottom: 5px;
        margin-top: -40px;
        margin-bottom: -40px;
    }

    .nombre {
        font-size: 22px;
        font-weight: 700;
        color: #1b3a2f;
        margin-bottom: 5px;
    }

    .cargo {
        color: #94c93b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .subcargo {
        font-size: 13px;
        color: #6c757d;
    }

    .perfil-contacto {
        background: #ffffff;
        border-radius: 15px;
        margin: 15px;
        padding: 12px;
        align-items: center;
    }

    .foto-perfil-horizontal {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(135deg, #94c93b, #a5d6a7);
    }

    .foto-perfil-horizontal img {
        border-radius: 50%;
    }

    .contacto a {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .contacto a:hover {
        color: #1b3a2f;
    }

    .contacto p {
        margin: 4px 0;
        color: #333;
        font-size: 13.5px;
        display: flex;
        align-items: center;
    }

    .contacto i {
        width: 22px;
        text-align: center;
        color: #94c93b;
        margin-right: 6px;
    }

    .servicios {
        margin-top: 10px;
    }

    .servicios h5 {
        color: #1b3a2f;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .servicios ul {
        list-style: none;
        padding-left: 0;
    }

    .servicios li {
        margin-bottom: 2px;
        color: #444;
        font-size: 14px;
        position: relative;
        padding-left: 22px;
    }

    .servicios li::before {
        content: "✔";
        position: absolute;
        left: 0;
        color: #94c93b;
        font-size: 13px;
    }

    .compartir {
        background: #ffffff;
        margin: 15px;
        padding: 10px 12px;
        border-radius: 12px;
    }

    .ubicacion {
        background: #ffffff;
        margin: 15px;
        padding: 10px 12px;
        border-radius: 12px;
    }

    .ubicacion p {
        font-size: 13px;
        margin-bottom: 5px;
        color: #444;
    }

    .ubicacion i {
        color: #ff7043;
        margin-right: 5px;
    }

    .frase {
        font-size: 13px;
        color: #2e4d2c;
        font-style: italic;
        margin-top: 10px;
        padding: 0 20px;
    }

    .perfil-contacto,
    .ubicacion {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(5px);
    }

    .btn-share {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #fff;
        transition: all 0.2s ease;
    }

    .btn-share.whatsapp { background: #25d366; }
    .btn-share.telegram { background: #2aabee; }
    .btn-share.messenger { background: #0084ff; }
    .btn-share.correo { background: #ff7043; }

    .btn-share:hover {
        transform: scale(1.1);
        opacity: 0.9;
    }

    @media (max-width: 576px) {

        .nombre {
            font-size: 19px;
        }

        .perfil-contacto {
            flex-direction: column;
            text-align: center;
        }

        .contacto {
            margin-top: 10px;
        }

        .contacto p {
            justify-content: center;
        }
    }
</style>
@endsection