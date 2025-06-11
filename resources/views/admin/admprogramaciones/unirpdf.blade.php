@extends('adminlte::page')

@section('content_header')
<h1>PROGRAMACIONES POR FECHA</h1>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 5000);
    </script>
@endif
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                <input type="file" id="pdf-files" multiple accept="application/pdf">
                <button type="button" onclick="uploadFiles()">Subir Archivos</button>
                <button type="button" onclick="mergePDFs()">Unir PDFs</button>
            </form>
            
            <ul id="file-list"></ul>
            
            <script>
                let uploadedFiles = [];
            
                function uploadFiles() {
                    let files = document.getElementById('pdf-files').files;
                    if (files.length === 0) {
                        alert("Selecciona al menos un archivo.");
                        return;
                    }
            
                    let formData = new FormData();
                    for (let i = 0; i < files.length; i++) {
                        formData.append('pdfs[]', files[i]);
                        uploadedFiles.push(files[i].name); // Guarda el orden
                    }
            
                    fetch("{{ route('pdf.upload') }}", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Archivos subidos correctamente.");
                            document.getElementById("file-list").innerHTML = uploadedFiles.map(f => `<li>${f}</li>`).join('');
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            
                function mergePDFs() {
                    fetch("{{ route('pdf.merge') }}", {
                        method: "POST",
                        body: JSON.stringify({ files: uploadedFiles }),
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
                        }
                    })
                    .then(response => response.blob())
                    .then(blob => {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement("a");
                        a.href = url;
                        a.download = "merged.pdf";
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    })
                    .catch(error => console.error("Error:", error));
                }
            </script>
            
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .table td {
            padding: 5px 10px;;
        }
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        .btn-editar {
                background-color:  #ffffff;
                color: #0400ff;
                border-color: #0400ff;
                border-radius: 5px;
            }
        .btn-editar:hover {
                background-color: #0400ff;
                color: #ffffff;
            }
        .btn-eliminar {
                background-color:  #ffffff;
                color: #ff0000;
                border-color: #ff0000;
                border-radius: 5px;
            }
        .btn-eliminar:hover {
                background-color: #ff0000;
                color: #ffffff;
            }
        .btn-crear {
                background-color:  #ffffff;
                color: #94c93b;
                border-color: #94c93b;
                border-radius: 5px;
                padding: 10px 20px;
            }
        .btn-crear:hover {
                background-color: #94c93b;
                color: #ffffff;
            }
        .btn-buscar { 
                background-color:  #ffffff;
                color: #faa625;
                border-color: #faa625;
                border-radius: 5px;
            }
        .btn-buscar:hover {
                background-color: #faa625;
                color: #ffffff;
            }
            .btn-whatsapp {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-whatsapp:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
</script>
@endsection