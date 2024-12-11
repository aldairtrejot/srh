
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id_tbl_oficio = $('#id_tbl_oficio').val();//Obtener elemento

//Inicio de variables
$(document).ready(function () {
    getDataCloud();
    getDataDocument();
});

//La funcion lista los documentos que existen en el cloud
function getDataDocument() {
    let container_anexo_entrada_vacio = $('#container_anexo_entrada_vacio');
    let container_anexo_entrada = $('#container_anexo_entrada');
    let container_oficio_entrada_vacio = $('#container_oficio_entrada_vacio');
    let container_oficio_entrada = $('#container_oficio_entrada');
    let container_anexo_salida_vacio = $('#container_anexo_salida_vacio');
    let container_anexo_salida = $('#container_anexo_salida');
    let container_oficio_salida_vacio = $('#container_oficio_salida_vacio');
    let container_oficio_salida = $('#container_oficio_salida');

    $.ajax({
        url: '/srh/public/office/cloud/anexos',
        type: 'POST',
        data: {
            id_tbl_oficio: id_tbl_oficio,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let anexosEntrada = response.anexosEntrada;  // Suponiendo que la respuesta tiene una propiedad 'value' con los datos
            let oficosEntrada = response.oficosEntrada;
            let anexoSalida = response.anexoSalida;
            let oficosSalida = response.oficosSalida;

            templateCloud(container_anexo_entrada, container_anexo_entrada_vacio, anexosEntrada); //Listamos la informacion
            templateCloud(container_oficio_entrada, container_oficio_entrada_vacio, oficosEntrada); //Listamos la informacion
            templateCloud(container_anexo_salida, container_anexo_salida_vacio, anexoSalida);
            templateCloud(container_oficio_salida, container_oficio_salida_vacio, oficosSalida);

        },
    });
}

//La funcion obtiene los datos del encabezado de cloud
function getDataCloud() {

    $.ajax({
        url: '/srh/public/office/cloud/data',
        type: 'POST',
        data: {
            id_tbl_oficio: id_tbl_oficio,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value; //Obtenemos la consulta

            $('#_noOficio').text(item.num_turno_sistema); // establecer los valores
            $('#_noCorrespondencia').text(item.num_turno_sistema_correspondencia); // establecer los valores
            $('#_noAnio').text(item.anio); // establecer los valores
            $('#_fechaInicio').text(item.fecha_inicio); // establecer los valores
            $('#_fechaFin').text(item.fecha_fin); // establecer los valores
        },
    });
}


//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_oficio_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0]); // Pasa el archivo real a la función
    }
});

function sendFile(file) {
    if (file) {
        let data = new FormData();// Crear el objeto FormData
        data.append('file', file);
        $.ajax({
            url: "/srh/public/office/cloud/upload",
            type: 'POST',
            data: data, // Enviar directamente el FormData
            processData: false,  // No procesar los datos, jQuery no debe intentar convertir los datos en una cadena
            contentType: false,  // No establecer un Content-Type porque el navegador lo hará automáticamente
            headers: {
                'X-CSRF-TOKEN': token  // Usar el token CSRF para proteger la solicitud
            },
            success: function (response) {
                console.log(response);
            },
        });
    }
}


function download(uid) {
    console.log(uid);
}

function deleteDocument(id) {
    console.log(id);
}