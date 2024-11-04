// Variables para controlar el estado del mensaje y el spinner
let currentMessage = "";
let spinnerActive = false;
let spinnerTimeout;

// Función para mostrar el spinner con mensaje personalizado y temporizador
function showGlobalSpinner(message = "") {
    const spinner = document.getElementById('global-loading-spinner');
    const spinnerText = document.querySelector('#global-loading-spinner .spinner-text');

    if (spinner && !spinnerActive) { // Solo mostrar si el spinner no está activo
        spinnerText.textContent = message;
        spinner.style.display = 'flex';
        spinner.classList.add('active');
        spinnerActive = true;
        currentMessage = message;

        // Cancela cualquier temporizador previo antes de iniciar uno nuevo
        clearTimeout(spinnerTimeout);

        // Temporizador para ocultar el spinner automáticamente después de 3 segundos
        spinnerTimeout = setTimeout(() => {
            hideGlobalSpinner();
        }, 3000);
    }
}

// Función para ocultar el spinner y resetear el mensaje
function hideGlobalSpinner() {
    const spinner = document.getElementById('global-loading-spinner');
    if (spinner && spinnerActive) { // Solo ocultar si el spinner está activo
        spinner.style.display = 'none';
        spinner.classList.remove('active');
        spinnerActive = false;
        currentMessage = ".";
        clearTimeout(spinnerTimeout); // Limpia el temporizador
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Detectar envío de formulario para mostrar "Guardando..."
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!spinnerActive) {
                showGlobalSpinner("");
            }
        });
    });

    // Detectar clics en botones de tipo submit para mostrar "Guardando..."
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function () {
            if (button.type === 'submit' && !spinnerActive) {
                showGlobalSpinner("");
            }
        });
    });

    // Detectar clics en botones de tipo submit para mostrar "Guardando..."
    document.getElementById('btn-crear-bateria').addEventListener('click', function() {
        const checkboxesAcciones = document.querySelectorAll('input[name="acciones[]"]:checked');
        const checkboxesEspecialidades = document.querySelectorAll('input[name="accionnombre[]"]:checked');
        
        // Verificar si no hay selección en las opciones requeridas
        if (checkboxesAcciones.length === 0 && checkboxesEspecialidades.length === 0) {
            // Mostrar alerta sin modificar el botón ni activar el spinner
            alert('No hay nada seleccionado. Por favor, selecciona al menos una acción o especialidad.');
            return; // Finalizar ejecución para evitar que se active el spinner o se oculte el botón
        }

        // Solo si hay selección, activar el spinner y enviar el formulario
        showGlobalSpinner("");
        
        // Ocultar el botón mientras se completa el envío
        document.getElementById('btn-crear-bateria').style.display = 'none';
        
        // Realizar el envío del formulario
        document.getElementById('form-crear-bateria').submit();
    });

    // Detectar eventos de retroceso o avance en el historial para desactivar el spinner
    window.addEventListener('popstate', function () {
        hideGlobalSpinner(); // Oculta el spinner en navegación de historial
    });

    // Detectar cuando el usuario intenta salir o recargar la página
    window.addEventListener('beforeunload', function (e) {
        // Solo activa el spinner si está en estado inactivo
        /* if (!spinnerActive) {
            showGlobalSpinner("Cargando...");
        } */
    });
});
