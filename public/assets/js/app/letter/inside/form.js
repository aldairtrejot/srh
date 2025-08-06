// Scrip que se ejecuta con el formulario, para funciones u herramientas extras
// Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('#num_documento_area').prop('disabled', true); //Desabilitar input
    $('select').selectpicker(); //Iniciar los select

    setData(); //Establecer las variables de informacion general
    //getRole(); //Obtener y definir los roles para no tener los input
    //setCheckboxArea();
    //tooltip('#id_checkbox_Template_tooltip', 'Marcar para añadir un No. Correspondencia manual'); // Tooltip
    //tooltip('#num_correspondencia', 'Asociar por No. de Turno o Folio de Gestión'); // Tooltip
    //getDataUsers($('#id_cat_area').val(), $('#id_usuario_area').val(), $('#id_usuario_enlace').val(), '#_labArea', '#_labUsuario', '#_labEnlace')

    // === FECHAS: aplicar límites y validación para Fecha de inicio / Fecha de fin ===
    setDateLimitsInicioFin();      // coloca min/max dinámicos (hoy + 3 meses, min 2020-01-01)
    bindValidationInicioFin();     // valida y muestra feedback al cambiar
});

// =========================
// FECHAS: límites y validación (INICIO / FIN)
// =========================
function setDateLimitsInicioFin() {
    const MIN_STR = '2020-01-01';
    const maxStr = toYMD(addMonths(new Date(), 3));

    // Establecer límites base
    ['#fecha_inicio', '#fecha_fin'].forEach(sel => {
        const $inp = $(sel);
        if ($inp.length) {
            $inp.attr('min', MIN_STR).attr('max', maxStr);
            const val = $inp.val();
            if (val && (val < MIN_STR || val > maxStr)) {
                $inp.val('');
            }
        }
    });

    // Hacer que FIN no pueda ser menor que INICIO si INICIO ya tiene valor
    const inicio = $('#fecha_inicio').val();
    if (inicio) {
        const minFin = (inicio > MIN_STR) ? inicio : MIN_STR;
        $('#fecha_fin').attr('min', minFin);
        if ($('#fecha_fin').val() && $('#fecha_fin').val() < minFin) {
            $('#fecha_fin').val('');
        }
    }
}

function bindValidationInicioFin() {
    const MIN_STR = '2020-01-01';

    // Reusa misma validación en ambos campos
    $('#fecha_inicio, #fecha_fin').on('input change', function () {
        const maxStr = toYMD(addMonths(new Date(), 3)); // recalcular por si cambia el día
        const id = this.id;
        const sel = '#' + id;
        const $inp = $(sel);
        const val = $inp.val();

        // Actualizar límites dinámicos
        $inp.attr('min', MIN_STR).attr('max', maxStr);

        // Validación básica de rango
        if (val) {
            if (val < MIN_STR || val > maxStr) {
                showFieldError(sel, `La fecha debe estar entre ${MIN_STR} y ${maxStr}.`);
                $inp.val('');
                return;
            } else {
                clearFieldError(sel);
            }
        } else {
            clearFieldError(sel);
        }

        // Reglas cruzadas INICIO/FIN
        const fi = $('#fecha_inicio').val();
        const ff = $('#fecha_fin').val();

        // Ajustar el min de FIN según INICIO (no menor a inicio)
        const minFin = fi ? (fi > MIN_STR ? fi : MIN_STR) : MIN_STR;
        $('#fecha_fin').attr('min', minFin);

        if (fi && ff && ff < fi) {
            showFieldError('#fecha_fin', 'La fecha fin no puede ser menor a la fecha de inicio.');
            $('#fecha_fin').val('');
        } else {
            // Si FIN quedó dentro de rango, limpiar error
            if (id === 'fecha_fin') clearFieldError('#fecha_fin');
        }
    });
}

// =========================
// Utilidades de fechas / feedback
// =========================
function toYMD(d) {
    const year  = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day   = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function addMonths(date, months) {
    const d = new Date(date);
    d.setMonth(d.getMonth() + months);
    return d;
}

// Feedback visual sin alertas
function showFieldError(selector, message) {
    const $inp = $(selector);
    let $fb = $inp.next('.invalid-feedback');
    if ($fb.length === 0) {
        $fb = $('<div class="invalid-feedback"></div>');
        $inp.after($fb);
    }
    $inp.addClass('is-invalid');
    $fb.text(message).show();
}

function clearFieldError(selector) {
    const $inp = $(selector);
    $inp.removeClass('is-invalid');
    const $fb = $inp.next('.invalid-feedback');
    if ($fb.length) $fb.text('').hide();
}

// =========================
// FUNCIONES EXISTENTES
// =========================

// La funcion activa o desactiva el valor de un checkbox de area
function setCheckboxArea() {
    if ($('#es_por_area').val()) { //valor del  la variable check true
        $('#idcheckboxTemplate').prop('checked', true); //Activar el checkk
        showDiv('mostrar_ocultar_no_area'); //Mostrar contenido
        $('#num_correspondencia').val('');// Limpiar input
        $('#num_correspondencia').prop('disabled', true); // desabilitar no de documento por area
    } else { //Valor de la variable check falso
        hideDiv('mostrar_ocultar_no_area'); //Ocultar contenido
        cleanSelect('#id_cat_area_documento'); // Limpiar select
        $('#num_documento_area').val('');// Limpiar input
        $('#num_correspondencia').prop('disabled', false); // desabilitar no de documento por area
        cleanSelectMoreSelect('#id_usuario_area_aux'); //Se limpia el select
        cleanSelectMoreSelect('#id_usuario_enlace_aux'); //Se limpia el select
    }
    //getRole(); //Validacion por roles
}

// Codigo para la ejecucion de un checkbox
$('#idcheckboxTemplate').change(function () {
    let bool = false; // Inicio de variable de checkbox de area
    bool = $(this).prop('checked') ? true : ''; //Se valida si el checkbox es verdadero o falso para asignarle ese valor a la variable
    $('#es_por_area').val(bool); //Se asigna el valor
    setCheckboxArea();//Se ejecuta la funcon
});

// La funcion desabilita los campos dependiendo del rol de usuario
function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        $('#num_correspondencia').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#asunto').prop('disabled', true);
        $('#idcheckboxTemplate').prop('disabled', true);

        $('#id_cat_area_documento').prop('disabled', true);

        $('#id_cat_area_documento').selectpicker('refresh');
    }
}

// la funcion obtiene los datos al iniciar el formulario, como fecha inicio, año etc
function setData() {
    let fecha_captura = $('#fecha_captura').val();//fecha de captura
    $('#_labFechaCaptura').text(fecha_captura); //establecer los varoles

    let num_turno_sistema = $('#num_turno_sistema').val();//fecha de captura
    $('#_labNoCorrespondencia').text(num_turno_sistema); //establecer los varoles

    let usuario = $('#usuario').val();//usuario
    $('#_labUsuario').text(usuario); //establecer los varoles

    let enlace = $('#enlace').val();//Enlace
    $('#_labEnlace').text(enlace); //establecer los varoles

    getData();//Se hace busqueda de la informacion
}

// La funcion obtiene el año
function getData() {

    let id_cat_anio = $('#id_cat_anio').val();//Obtener elemento

    $.ajax({
        url: URL_DEFAULT.concat('/year/getYear'),
        type: 'POST',
        data: {
            id_cat_anio: id_cat_anio,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.nameYear;
            $('#_labAño').text(item.name); // establecer los valores
        },
    });
}

// Se detecta el cambio de valor de No de correspondencia asoc, con el fin de obtener el usuario y enlace si es que es correcto
$('#num_correspondencia').on('input', function () {
    let value = $(this).val().trim();  // Obtener el valor del campo de texto
    if (value !== '') { // Validacion para que el campo no este en blanco
        getNoDocument(value, '#_labUsuario', '#_labEnlace', '#_labArea', '#id_cat_area', '#id_usuario_area', '#id_usuario_enlace', '#id_tbl_correspondencia');
    } else {
        $('#_labUsuario').text(' _');
        $('#_labEnlace').text(' _');
        $('#_labArea').text(' _');
    }
});
