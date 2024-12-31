
// La funcion valida que el no de correspondecia este asociado a correspondencia
function getNoCorrespondencia(value) {
    let isValid = false;  // Asumimos que es inválido inicialmente

    $.ajax({
        url: URL_DEFAULT.concat('/collection/validate/letter'),
        type: 'POST',
        async: false, // Asegura que la ejecución sea sincrónica
        data: {
            value: value,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.status;
            if (item) {
                isValid = true;  // La validación fue exitosa
            }
        }
    });

    return isValid;  // Regresa el resultado de la validación
}


// La funcion valida que el no de documento y el folio de gestion sean unicos
function getNoUnique(id, value, attribute) {
    let isValid = false;  // Asumimos que es inválido inicialmente
    if (value !== '') {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/validateUnique'),
            type: 'POST',
            async: false, // Asegura que la ejecución sea sincrónica
            data: {
                id: id,
                value: value,
                attribute: attribute,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                let item = response.status;
                if (item) {
                    isValid = true;  // La validación fue exitosa
                }
            }
        });
    }
    return isValid;  // Regresa el resultado de la validación
}


// La funcion valida que el remitente sea unico
function getUniqueRemitente(value, attribute) {
    let isValid = false;  // Asumimos que es inválido inicialmente
    if (value !== '') {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/uniqueRemitente'),
            type: 'POST',
            async: false, // Asegura que la ejecución sea sincrónica
            data: {
                value: value,
                attribute: attribute,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                let item = response.status;
                if (item) {
                    isValid = true;  // La validación fue exitosa
                }
            }
        });
    }
    return isValid;  // Regresa el resultado de la validación
}

// La funcion valida si existe un No de correspondencia, si es correcto imprime el usuario y el enlace que se tiene
function getNoDocument(value, labelUser, labelEnlsace) {
    $.ajax({
        url: URL_DEFAULT.concat('/valitade/letter'),
        type: 'POST',
        data: {
            value: value,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            $(labelUser).text(' _');
            $(labelEnlsace).text(' _');

            if (response.value && response.value.length > 0) { //Array con informacion
                let data = response.value[0];
                $(labelUser).text(data.id_usuario_area);
                $(labelEnlsace).text(data.id_usuario_enlace);
            }
        },
    });
}