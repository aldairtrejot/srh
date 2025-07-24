// La funcion retorna los valores para listar los archivos de cloud
function templateCloud(bool, templateData, templateDataNull, data) {
    // Limpiar el contenedor antes de agregar nuevos elementos
    templateData.empty();

    // Verificamos si hay datos en los anexos
    if (data.length !== 0) {
        // Si hay información, ocultamos el mensaje "sin contenido"
        templateDataNull.hide();

        // Iteramos sobre los anexos y creamos los elementos HTML dinámicamente
        data.forEach(function (valueTemplate) {
            // Creamos el HTML para cada anexo utilizando la segunda función
            let fileHTML = generateFileHTML(bool, valueTemplate);

            // Insertamos el HTML generado en el contenedor
            templateData.append(fileHTML);
        });
    } else {
        // Si no hay información, mostramos el mensaje de "sin contenido"
        templateDataNull.show();
    }
}

// La función que genera el HTML para cada archivo de cloud
function generateFileHTML(boolx, template) {
    return `
        <div class="custom-file-container">
            <div class="custom-file-icon-container">
                <i style="color:#777777" class="fa fa-file" aria-hidden="true"></i>
                <div class="custom-button-container">
                    <button onclick="seeDocumentUid('${template.uid}')" style="background: #10312b; padding: 8px 12px;" class="custom-button-x custom-button" title="Ver">
                        <i style="color: white; font-size: 15px" class="fa fa-eye"></i>
                    </button> 
                    <button onclick="download('${template.uid}')" style="background: #707070; padding: 8px 12px;" class="custom-button-x custom-button" title="Descargar">
                        <i style="color: white; font-size: 15px" class="fa fa-download"></i>
                    </button>
                    ${boolx ? `
                            <button onclick="deleteDocument('${template.uid}')" style="background: #6A1B3D; padding: 8px 12px;" class="custom-button-x custom-button" title="Eliminar">
                                <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
                            </button>
                        ` : ''
        }
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

//La funcion habilita el campo para que se puedan cargar mas archivos
function enableIput(idLabel, idIcon, idValue) {
    $(idLabel).css('color', 'red');  // Restaurar color del texto
    $(idIcon).css('color', '');      // Restaurar color del ícono
    $(idValue).prop('disabled', false);  // Habilitar el input
    $(idLabel).css('cursor', 'pointer');  // Restaurar el cursor normal
}

// Función para descargar archivo Excel correctamente usando fetch
function download(uid) {
  const url = URL_DEFAULT.concat('/cloud/download');

  const formData = new FormData();
  formData.append('uid', uid);
  formData.append('_token', token);  // token global definido

  fetch(url, {
    method: 'POST',
    body: formData,
  })
    .then(response => {
      if (!response.ok) throw new Error('Error en la descarga');

      // Obtener el nombre de archivo del header Content-Disposition
      const disposition = response.headers.get('Content-Disposition');
      let filename = 'archivo_descargado';

      if (disposition && disposition.indexOf('filename=') !== -1) {
        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        const matches = filenameRegex.exec(disposition);
        if (matches != null && matches[1]) {
          filename = matches[1].replace(/['"]/g, '');
        }
      }

      return response.blob().then(blob => ({ blob, filename }));
    })
    .then(({ blob, filename }) => {
      const urlBlob = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = urlBlob;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(urlBlob);
    })
    .catch(error => {
      console.error('Error descargando archivo:', error);
      alert('Error descargando archivo.');
    });
}



//Se utiliza la funcion para ver archivos de alfresco
function seeDocumentUid(uid) {

    //showSpinner();// Inicio de spinner
    // Crear una URL para la descarga
    let url = URL_DEFAULT.concat('/cloud/see');

    // Crear un formulario temporal para enviar el UID y activar la descarga
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    // Añadir un campo oculto para el UID
    let uidField = document.createElement('input');
    uidField.type = 'hidden';
    uidField.name = 'uid';
    uidField.value = uid;
    form.appendChild(uidField);

    // Añadir el token CSRF (si es necesario)
    let tokenField = document.createElement('input');
    tokenField.type = 'hidden';
    tokenField.name = '_token';
    tokenField.value = token;  // Asegúrate de que 'token' esté correctamente definido
    form.appendChild(tokenField);

    // Añadir el formulario al body y enviarlo
    document.body.appendChild(form);
    form.submit();

    // Limpiar el formulario después de enviarlo
    document.body.removeChild(form);
    //hideSpinner(); // Se oculta el spinner
}

