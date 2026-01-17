
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


// La funcion valida que el nombre de remitente sea unico
function getUniqueNameRemitente(name, fistLastName, seconLastName, attribute) {
    let isValid = false;  // Asumimos que es inválido inicialmente
    if (name !== '') {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/uniqueNameValidate'),
            type: 'POST',
            async: false, // Asegura que la ejecución sea sincrónica
            data: {
                name: name,
                fistLastName: fistLastName,
                seconLastName: seconLastName,
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
function getNoDocument(value, labelUser, labelEnlace, labelArea, id_area, id_usuario, id_enlace, id_tbl_correspondencia) {
    $.ajax({
        url: URL_DEFAULT.concat('/valitade/letter'),
        type: 'POST',
        data: {
            value: value,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.value && response.value.length > 0) { //Array con informacion
                let data = response.value[0];
                $(labelUser).text(data.usuario_area);
                $(labelEnlace).text(data.usuario_enlace);
                $(labelArea).text(data.area);

                $(id_area).val(data.id_cat_area);
                $(id_usuario).val(data.id_usuario_area);
                $(id_enlace).val(data.id_usuario_enlace);
                $(id_tbl_correspondencia).val(data.id_tbl_correspondencia);
            } else {
                cleanUserArea(labelArea, labelUser, labelEnlace, id_area, id_usuario, id_enlace, id_tbl_correspondencia);// clean
            }
        },
    });
}


// La función obtiene el valor de area, usuario y enlace y remplaza los valores
function getDataUsers(id_area, id_usuario, id_enlace, labelArea, labelUser, labelEnlace) {
    if (id_area !== '') {
        $.ajax({
            url: URL_DEFAULT.concat('/collection/areaAndUser'),
            type: 'POST',
            data: {
                id_area: id_area,
                id_usuario: id_usuario,
                id_enlace: id_enlace,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                $(labelArea).text(response.nameArea);
                $(labelUser).text(response.nameUser);
                $(labelEnlace).text(response.nameEnlace);
            },
        });
    } else {
        cleanUserArea(labelArea, labelUser, labelEnlace, id_area, id_usuario, id_enlace);// clean
    }
}

// La funcion agrega un - a los atributos, asi como le quita el valor a area, usuario y enlace
function cleanUserArea(labelArea, labelUser, labelEnlace, id_area, id_usuario, id_enlace, id_tbl_correspondencia) {
    // Variables de input
    $(id_area).val('');
    $(id_usuario).val('');
    $(id_enlace).val('');
    $(id_tbl_correspondencia).val('');

    // Variables de text
    $(labelArea).text(' _');
    $(labelUser).text(' _');
    $(labelEnlace).text(' _');
}