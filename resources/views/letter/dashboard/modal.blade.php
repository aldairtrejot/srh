<x-template-modal.modal-template tittle="Generar informe" idModal="modalReport" idCancel="cancel_copy"
    idConfirm="confir_copy" functionConfirm="validateDate();" width="1200px" height="700px">

    <div class="row">
        <!-- Dos selects con buscador -->
        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label for="select-pickup-1" class="label-time">Área</label>
                <select class="form-control custom-select selectpicker" data-style="input-select-selectpicker"
                    aria-label="Default select example" data-live-search="true" data-none-results-text="Sin resultados"
                    id="id_cat_area_informe">
                </select>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="custom-col">
                <label for="select-pickup-2" class="label-time">Estatus</label>
                <select class="form-control custom-select selectpicker" data-style="input-select-selectpicker"
                    aria-label="Default select example" data-live-search="true" data-none-results-text="Sin resultados"
                    id="id_cat_status_informe">
                </select>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="custom-col">
                <label for="select-pickup-2" class="label-time">Año</label>
                <select class="form-control custom-select selectpicker" data-style="input-select-selectpicker"
                    aria-label="Default select example" data-live-search="true" data-none-results-text="Sin resultados"
                    id="id_cat_date_informe">
                </select>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label for="select-pickup-2" class="label-time">Fecha Inicio de captura</label>
                <input type="date" id="fecha_inicio_informe" class="form-control" style="font-size: 1rem;" />
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label for="select-pickup-2" class="label-time">Fecha Fin de captura</label>
                <input type="date" id="fecha_fin_informe" class="form-control" style="font-size: 1rem;" />
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 form-check form-check-flat form-check-secondary"
            style="display: inline-block; margin-bottom: 30px;">
            <label class="form-check-label" style="display: flex; align-items: center;">
                ¿Incluir todas las horas?
                <input type="checkbox" class="form-check-input" id="incluir_horas" style="margin-left: 10px;">
            </label>
        </div>

        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 form-check form-check-flat form-check-secondary"
            style="display: inline-block; margin-bottom: 30px;">
            <label class="form-check-label" style="display: flex; align-items: center;">
                ¿Agregar datos de captura?
                <input type="checkbox" class="form-check-input" id="inlcuir_usuario_capturo" style="margin-left: 10px;">
            </label>
        </div>

        <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-3 form-check form-check-flat form-check-secondary"
            style="display: inline-block; margin-bottom: 30px;">
            <label class="form-check-label" style="display: flex; align-items: center;">
                ¿Filtrar por fecha de inicio y fecha de finalización?
                <input type="checkbox" class="form-check-input" id="fecha_inicio_fecha_fin" style="margin-left: 10px;">
            </label>
        </div>
    </div>


    <div class="custom-row" style="margin-bottom: 20px;">
        <div class="custom-col">
            <span class="label-time" id="inicio-label">Hora de inicio de captura</span>
            <input type="range" id="inicio" min="0" max="24" value="0" step="1" class="range">
            <span class="hour-label" id="inicio-hour-right">00:00</span>
        </div>
    </div>

    <div class="custom-row" style="margin-bottom: 20px;">
        <div class="custom-col">
            <span class="label-time" id="fin-label">Hora de fin de captura</span>
            <input type="range" id="fin" min="0" max="24" value="24" step="1" class="range">
            <span class="hour-label" id="fin-hour-right">24:00</span>
        </div>
    </div>

</x-template-modal.modal-template>

<style>
    /* Estilo para los inputs de tipo range deshabilitados */
    input[type="range"]:disabled {
        background-color: #ddd;
        /* Fondo gris claro */
        cursor: not-allowed;
        /* Cursor de no permitido */
        opacity: 0.5;
        /* Opacidad para dar un efecto visual de desactivado */
    }

    /* Estilo para el track del slider deshabilitado */
    input[type="range"]:disabled::-webkit-slider-runnable-track {
        background-color: #bbb;
        /* Un gris más suave para el track */
        border: none;
        /* Sin borde */
        height: 10px;
        /* Establece la altura del track */
    }

    /* Firefox */
    input[type="range"]:disabled::-moz-range-track {
        background-color: #bbb;
        /* Un gris más suave para el track */
        border: none;
        height: 10px;
    }

    /* Internet Explorer */
    input[type="range"]:disabled::-ms-track {
        background-color: #bbb;
        /* Un gris más suave para el track */
        border: none;
        height: 10px;
        width: 100%;
        /* Asegura que ocupe todo el ancho del slider */
        color: transparent;
        /* Asegura que no se vea color extra */
    }

    /* Estilo para el thumb (el control deslizante) deshabilitado */
    input[type="range"]:disabled::-webkit-slider-thumb {
        background-color: #888;
        /* Color más oscuro para el thumb */
        border: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
        /* Hacer que el thumb sea redondo */
    }

    /* Firefox */
    input[type="range"]:disabled::-moz-range-thumb {
        background-color: #888;
        /* Color más oscuro para el thumb */
        border: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
    }

    /* Internet Explorer */
    input[type="range"]:disabled::-ms-thumb {
        background-color: #888;
        /* Color más oscuro para el thumb */
        border: none;
        height: 20px;
        width: 20px;
        border-radius: 50%;
    }

    /* Estilo de las etiquetas de "Hora de inicio" y "Hora de fin" */
    .label-time {
        display: block;
        text-align: left;
        font-size: 14px;
        color: #333;
        margin-bottom: 5px;
    }

    /* Estilo de la hora a la derecha */
    .hour-label {
        display: block;
        text-align: right;
        font-size: 14px;
        color: #333;
        margin-top: 5px;
    }

    .range {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 10px;
        background: #777777;
        /* Fondo gris */
        border-radius: 5px;
        position: relative;
        outline: none;
        margin-top: 10px;
    }

    .range::-webkit-slider-runnable-track {
        height: 10px;
        background: #777777;
        /* Fondo gris */
        border-radius: 5px;
    }

    .range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #10312b;
        /* Color de la bolita */
        cursor: pointer;
        position: relative;
        z-index: 1;
    }

    /* Asegurando que los controles no se crucen */
    .range#inicio::-webkit-slider-thumb {
        left: calc(100% * var(--inicio) / 24);
    }

    .range#fin::-webkit-slider-thumb {
        left: calc(100% * var(--fin) / 24);
    }

    /* Espaciado entre los rangos y los títulos */
    .custom-row {
        margin-bottom: 20px;
    }
</style>

<script>
    const inicio = document.getElementById('inicio');
    const fin = document.getElementById('fin');
    const inicioHourLabelRight = document.getElementById('inicio-hour-right');
    const finHourLabelRight = document.getElementById('fin-hour-right');

    // Control de movimiento sin que las bolitas se crucen
    inicio.addEventListener('input', function () {
        if (parseInt(inicio.value) > parseInt(fin.value)) {
            fin.value = inicio.value;
        }
        updateHourLabels();
    });

    fin.addEventListener('input', function () {
        if (parseInt(fin.value) < parseInt(inicio.value)) {
            inicio.value = fin.value;
        }
        updateHourLabels();
    });

    // Actualización de las horas al mover los rangos
    function updateHourLabels() {
        inicioHourLabelRight.textContent = formatHour(inicio.value);
        finHourLabelRight.textContent = formatHour(fin.value);
    }

    // Formatear hora en formato 00:00
    function formatHour(value) {
        const hours = Math.floor(value);
        const minutes = Math.round((value - hours) * 60);
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '00')}`;
    }

    // Inicializar las horas al cargar
    updateHourLabels();
</script>