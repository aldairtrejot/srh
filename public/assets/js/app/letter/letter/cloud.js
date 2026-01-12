//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id = $('#id').val();//Obtener elemento  (id_tbl_correspondencia)
var id_cat_area = $('#id_cat_area').val(); //Se obtiene el id de la area
var id_cat_entrada = $('#id_cat_entrada').val(); //Se obtiene el id de la area
var id_cat_tipo_oficio = $('#id_cat_tipo_oficio').val(); //Se obtiene el id de la area
var es_oficio = 1; //Identifica si es oficio
var es_anexo = 0;//Identifca si es un anexo

// ===== NUEVO: datos para anexos de respuesta (lado derecho) =====
var replyOficioId      = null;  // id_tbl_oficio (respuesta)
var replyAnexosCount   = 0;     // cuantos anexos de salida hay
var MAX_ANEXOS_SALIDA  = 3;     // se sobreescribe con lo que mande el back

// ===== Tipos permitidos (PDF/ZIP/Excel) =====
var ALLOWED_EXT = ['pdf', 'zip', 'xls', 'xlsx', 'xlsm', 'csv'];

//Inicio de variables
$(document).ready(function () {
    getDataCloud();
    getDataDocument();
    getReplySummary(); // <-- llena el panel "Documento de Respuesta"

    $(window).click(function (event) {//El evento se utiliza para oculatar el modal de eliminar cuando se da click en cualquier parte distinta
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });
});

/**
 * Valida extensión del archivo en frontend (ayuda al usuario).
 * NOTA: backend debe validar también (mimes).
 */
function isAllowedFile(file) {
    if (!file || !file.name) return false;
    var ext = (file.name.split('.').pop() || '').toLowerCase();
    return ALLOWED_EXT.includes(ext);
}

function showFileNotAllowedMessage() {
    var msg = 'Tipo de archivo no permitido. Solo: PDF, ZIP y Excel (XLS/XLSX/XLSM) / CSV.';
    if (typeof notyfEM !== 'undefined' && notyfEM.error) notyfEM.error(msg);
    else alert(msg);
}

/**
 * NOTA: Ya no se usará para bloquear subida (porque quieres permisos para todos),
 * pero lo dejo por si lo ocupas para otras cosas.
 */
function getRole() {
    let bool_user_role = $('#bool_user_role').val();
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false;
    if (!new_variable) {
        disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
        disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');
    } else {
        enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
        enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');
    }
}

//La funcion lista los documentos que existen en el cloud (ENTRADA)
function getDataDocument() {

    let bool_user_role = $('#bool_user_role').val();
    let hasRole = !!(bool_user_role && bool_user_role.trim() !== ''); // solo para mostrar botón eliminar
    // ✅ PERMISOS: todos pueden subir (ya no condicionamos por rol)

    let container_anexo_entrada_vacio = $('#container_anexo_entrada_vacio');
    let container_anexo_entrada = $('#container_anexo_entrada');
    let container_oficio_entrada_vacio = $('#container_oficio_entrada_vacio');
    let container_oficio_entrada = $('#container_oficio_entrada');

    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/anexos'),
        type: 'POST',
        data: {
            id_tbl_oficio: id, // <- tu endpoint actual lo espera así
            _token: token
        },
        success: function (response) {
            let anexosEntrada = response.anexosEntrada || [];
            let oficosEntrada = response.oficosEntrada || [];

            // ✅ ENTRADA: habilitar/deshabilitar solo por límite (no por rol)
            response.resultOficioEntrada
                ? disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada')
                : enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');

            response.resultAnexosEntrada
                ? disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada')
                : enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');

            // Mostrar lista (botón eliminar se controla con hasRole)
            templateCloud(hasRole, container_anexo_entrada, container_anexo_entrada_vacio, anexosEntrada);
            templateCloud(hasRole, container_oficio_entrada, container_oficio_entrada_vacio, oficosEntrada);
        },
    });
}

//La funcion obtiene los datos del encabezado de cloud
function getDataCloud() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/data'),
        type: 'POST',
        data: {
            id: id,
            _token: token
        },
        success: function (response) {
            let item = response.value;
            $('#_noOficio').text(item.num_turno_sistema);
            $('#_noCorrespondencia').text(item.num_documento);
            $('#_noAnio').text(item.anio);
            $('#_fechaInicio').text(item.fecha_inicio);
            $('#_fechaFin').text(item.fecha_fin);
        },
    });
}

// ========= Lado derecho: Documento de respuesta (solo lectura + subir anexos) =========
function getReplySummary() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/reply'),
        type: 'POST',
        data: {
            id: id,
            _token: token
        },
        success: function (r) {
            console.log(r);

            // Guardar info global
            replyOficioId     = r.id_oficio || null;
            MAX_ANEXOS_SALIDA = r.max_anexos_salida || 3;
            replyAnexosCount  = Array.isArray(r.anexosSalida) ? r.anexosSalida.length : 0;

            // Texto
            $('#resp_asunto').text(r.asunto || '—');
            $('#resp_observaciones').text(r.observaciones || '—');

            // Rol solo para mostrar botón eliminar
            let bool_user_role = $('#bool_user_role').val();
            let hasRole        = !!(bool_user_role && bool_user_role.trim() !== '');

            // ===== Oficio de salida =====
            const ofiVacio = $('#container_oficio_salida_vacio');
            const ofiCont  = $('#container_oficio_salida');
            ofiCont.empty();

            if (Array.isArray(r.oficiosSalida) && r.oficiosSalida.length > 0) {
                ofiVacio.hide();
                r.oficiosSalida.forEach(function (v) {
                    ofiCont.append(generateFileHTML(hasRole, v));
                });
            } else {
                ofiVacio.show();
            }

            // ===== Anexos de salida =====
            const aneVacio = $('#container_anexo_salida_vacio');
            const aneCont  = $('#container_anexo_salida');
            aneCont.empty();

            if (Array.isArray(r.anexosSalida) && r.anexosSalida.length > 0) {
                aneVacio.hide();
                r.anexosSalida.forEach(function (v) {
                    aneCont.append(generateFileHTML(hasRole, v)); // eliminar solo si hasRole
                });
            } else {
                aneVacio.show();
            }

            // Habilitar / deshabilitar botón "Cargar" de anexos de respuesta
            toggleReplyAnexoUpload();
        }
    });
}


// ====== EVENTOS DE SUBIDA (ENTRADA) ======

//La funcion sube el archivo que el usuario esta seleccionando (OFICIO ENTRADA)
var fileOficioEntradaEl = document.getElementById('file_oficio_entrada');
if (fileOficioEntradaEl) {
    fileOficioEntradaEl.addEventListener('change', function (event) {
        if (event.target.files.length > 0) {
            var f = event.target.files[0];
            if (!isAllowedFile(f)) {
                showFileNotAllowedMessage();
                event.target.value = '';
                return;
            }
            sendFile(f, id_cat_entrada, es_oficio);
        }
        event.target.value = '';
    });
}

//La funcion sube el archivo que el usuario esta seleccionando (ANEXO ENTRADA)
var fileAnexoEntradaEl = document.getElementById('file_anexo_entrada');
if (fileAnexoEntradaEl) {
    fileAnexoEntradaEl.addEventListener('change', function (event) {
        if (event.target.files.length > 0) {
            var f = event.target.files[0];
            if (!isAllowedFile(f)) {
                showFileNotAllowedMessage();
                event.target.value = '';
                return;
            }
            sendFile(f, id_cat_entrada, es_anexo);
        }
        event.target.value = '';
    });
}

// ====== EVENTO DE SUBIDA (ANEXO SALIDA / RESPUESTA) ======
var fileAnexoSalidaEl = document.getElementById('file_anexo_salida');
if (fileAnexoSalidaEl) {
    fileAnexoSalidaEl.addEventListener('change', function (event) {
        var files = event.target.files || [];
        if (!files.length) return;

        var f = files[0];
        if (!isAllowedFile(f)) {
            showFileNotAllowedMessage();
            event.target.value = '';
            return;
        }

        sendReplyAnexo(f);
        event.target.value = ''; // limpiar input
    });
}

// ====== FUNCIÓN PARA SUBIR ANEXO DE RESPUESTA ======
function sendReplyAnexo(file) {
    if (!file) return;

    if (!replyOficioId) {
        if (typeof notyfEM !== 'undefined' && notyfEM.error) {
            notyfEM.error('No hay oficio de respuesta para adjuntar.');
        } else {
            alert('No hay oficio de respuesta para adjuntar.');
        }
        $('#file_anexo_salida').val('');
        return;
    }

    var remaining = MAX_ANEXOS_SALIDA - replyAnexosCount;
    if (remaining <= 0) {
        if (typeof notyfEM !== 'undefined' && notyfEM.error) {
            notyfEM.error('Solo se permiten hasta ' + MAX_ANEXOS_SALIDA + ' anexos de respuesta.');
        } else {
            alert('Solo se permiten hasta ' + MAX_ANEXOS_SALIDA + ' anexos de respuesta.');
        }
        $('#file_anexo_salida').val('');
        return;
    }

    showSpinner();

    var data = new FormData();
    data.append('file', file);
    data.append('id_tbl_oficio', replyOficioId);
    data.append('id_tbl_correspondencia', id);
    data.append('id_cat_area', id_cat_area);
    data.append('id_cat_entrada', id_cat_entrada);
    data.append('id_cat_tipo_oficio', id_cat_tipo_oficio);

    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/reply/upload-anexo'),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': token },
        success: function (resp) {
            hideSpinner();
            $('#file_anexo_salida').val('');

            if (resp.status) {
                if (typeof notyfEM !== 'undefined' && notyfEM.success) {
                    notyfEM.success(resp.message || 'Anexo agregado correctamente.');
                } else {
                    alert(resp.message || 'Anexo agregado correctamente.');
                }
                getReplySummary();
            } else {
                if (typeof notyfEM !== 'undefined' && notyfEM.error) {
                    notyfEM.error(resp.message || 'No fue posible subir el anexo.');
                } else {
                    alert(resp.message || 'No fue posible subir el anexo.');
                }
            }
        },
        error: function () {
            hideSpinner();
            $('#file_anexo_salida').val('');
            if (typeof notyfEM !== 'undefined' && notyfEM.error) {
                notyfEM.error('Error al subir el anexo.');
            } else {
                alert('Error al subir el anexo.');
            }
        }
    });
}

// ====== Habilitar / deshabilitar botón "Cargar" anexos de respuesta (TODOS pueden) ======
function toggleReplyAnexoUpload() {
    var remaining = MAX_ANEXOS_SALIDA - replyAnexosCount;

    // Solo bloquea si no hay oficio de respuesta o si ya no hay cupo
    if (!replyOficioId || remaining <= 0) {
        disabledInput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida');
    } else {
        enableIput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida');
        $('#file_anexo_salida').attr('data-remaining', remaining);
    }
}


// ====== SUBIDA DE ARCHIVOS (ENTRADA) ======
function sendFile(file, id_entrada_salida, esOficio) {
    if (file) {
        showSpinner();
        let data = new FormData();
        data.append('file', file);
        data.append('id_cat_tipo_oficio', id_cat_tipo_oficio);
        data.append('id_cat_area', id_cat_area);
        data.append('id', id);
        data.append('id_entrada_salida', id_entrada_salida);
        data.append('esOficio', esOficio);

        $.ajax({
            url: URL_DEFAULT.concat("/letter/cloud/upload"),
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (response) {
                hideSpinner();
                if (response.status) {
                    notyfEM.success("Documento agregado correctamente.");
                } else {
                    notyfEM.error(response.messages || "No fue posible subir el archivo.");
                }
                getDataDocument();
                getReplySummary(); // por si el backend refleja algo en respuesta
            },
            error: function () {
                hideSpinner();
                notyfEM.error("Error al subir el archivo.");
            }
        });
    }
}

// ====== ELIMINAR DOCUMENTO (FIX: evita listeners duplicados) ======
function deleteDocument(uid) {
    $('#modalBackdrop').fadeIn();

    $('#cancelBtn').off('click').one('click', function () {
        $('#modalBackdrop').fadeOut();
    });

    $('#confirmBtn').off('click').one('click', function () {
        deleteDocumenServer(uid);
        $('#modalBackdrop').fadeOut();
    });
}

//La funcion elimina oficios del repositorio, solo de la base
function deleteDocumenServer(uid) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/delete'),
        type: 'POST',
        data: {
            uid: uid,
            _token: token
        },
        success: function (response) {
            if (response.messages) {
                notyfEM.success("El archivo se eliminó correctamente.");
            } else {
                notyfEM.error("Algo inesperado ocurrió al realizar la acción.");
            }
            // Refrescar ENTRADA
            getDataDocument();
            // Refrescar RESPUESTA
            getReplySummary();
        },
        error: function () {
            notyfEM.error("Error al eliminar el archivo.");
        }
    });
}


