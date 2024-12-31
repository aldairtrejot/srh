
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