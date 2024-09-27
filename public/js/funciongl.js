/* CANCELAR BOTON ENTER */
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

/* CREAR AREA */
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
    function setTipoArea() {
        var tipoarea = document.getElementById('tipoarea').value;
        var idtipoarea = document.getElementById('idtipoarea');

        if (tipoarea === 'ESTUDIO') {
            idtipoarea.value = 2;
        } else if (tipoarea === 'ESPECIALIDAD') {
            idtipoarea.value = 1;
        } else {
            idtipoarea.value = '';
        }
    }

/* CREAR ACCION */
    function setAsociado() {
        var asociado = document.getElementById('asociado').value;
        var asociadoid = document.getElementById('asociadoid');
        if (asociado === 'CLIENTES ITA') {
            asociadoid.value = 6;
        } else if (asociado === 'CLIENTES COMUNES') {
            asociadoid.value = 3;
        } else {
            asociadoid.value = '';
        }
    }