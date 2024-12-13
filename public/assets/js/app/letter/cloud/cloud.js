// La funcion retorna los valores para listar los archivos de cloud
function templateCloud(templateData, templateDataNull, data) {
    // Limpiar el contenedor antes de agregar nuevos elementos
    templateData.empty();

    // Verificamos si hay datos en los anexos
    if (data.length !== 0) {
        // Si hay información, ocultamos el mensaje "sin contenido"
        templateDataNull.hide();

        // Iteramos sobre los anexos y creamos los elementos HTML dinámicamente
        data.forEach(function (valueTemplate) {
            // Creamos el HTML para cada anexo utilizando la segunda función
            let fileHTML = generateFileHTML(valueTemplate);

            // Insertamos el HTML generado en el contenedor
            templateData.append(fileHTML);
        });
    } else {
        // Si no hay información, mostramos el mensaje de "sin contenido"
        templateDataNull.show();
    }
}

// La función que genera el HTML para cada archivo de cloud
function generateFileHTML(template) {
    return `
        <div class="custom-file-container">
            <div class="custom-file-icon-container">
                <i style="color:#777777" class="fa fa-file" aria-hidden="true"></i>
                <div class="custom-button-container">
                    <button onclick="getInfo('${template.id}')" style="background: #003366" class="custom-button" title="Usuario">
                        <i style="color: white" class="fa fa-user"></i>
                    </button>
                    <button onclick="getInfo('${template.uid}')" style="background: #1D5B3B" class="custom-button" title="Ver">
                        <i style="color: white" class="fa fa-eye"></i>
                    </button>
                    <button onclick="download('${template.uid}')" style="background: #707070" class="custom-button" title="Descargar">
                        <i style="color: white" class="fa fa-download"></i>
                    </button>
                    <button onclick="deleteDocument('${template.id}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                        <i style="color: white" class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="custom-file-name">
                <p>${template.nombre}</p>
            </div>
        </div>
    `;
}

//La funcion desabilita input tomando como parametro el nombre
function disabledInput(idLabel, idIcon, idValue) {
    $(idLabel).css('color', 'gray');  // Cambiar color del texto a gris
    $(idIcon).css('color', 'gray');   // Cambiar color del ícono a gris
    $(idValue).prop('disabled', true);  // Deshabilitar el input
    $(idLabel).css('cursor', 'not-allowed');
}

function enableIput(idLabel, idIcon, idValue) {
    $(idLabel).css('color', 'red');  // Restaurar color del texto
    $(idIcon).css('color', '');      // Restaurar color del ícono
    $(idValue).prop('disabled', false);  // Habilitar el input
    $(idLabel).css('cursor', 'pointer');  // Restaurar el cursor normal
}