/**
 * EL CODIGO PROPORCIONA FUNCIONES PARA LAS VALIDACIONES DE UN FORMULARIO
 * 
 * 
 */

// Función que verifica si un campo está vacío mandando mensaje
function isEmpty(field) {
    return (field === undefined || field === null || field === '' || field === 0 || field === false || (Array.isArray(field) && field.length === 0) || (typeof field === 'object' && Object.keys(field).length === 0));
}

// Función valida que sea un numero entero positivo
function isInteger(value) {
    return (Number.isInteger(value) && value > 0) ? true : false;
}


// FUNCION QUE RETORNA VERDADERO O FALSO CON EL MENSAJE DE ERROR, SI ES QUE NO ES UN NUMERO ENTERO POSITIVO
function isPositiveInteger(filed, nameFiel) {
    let boolean = false;
    if (!isInteger(filed)) {
        boolean = true;
        notyfEM.error('Campo ' + nameFiel + ' no es valido.');
    }
    return boolean; // Retorna true si el campo esta vacio
}


// FUNCION QUE RETORNA VERDADERO O FALSO CON EL MENSAJE DE ERROR, FIEL REQUIRED
function isFieldEmpty(field, nameFiel) {
    let boolean = false;
    if (isEmpty(field)) {
        boolean = true;
        notyfEM.error('Campo ' + nameFiel + ' es requerido.');
    }
    return boolean; // Retorna true si el campo esta vacio
}