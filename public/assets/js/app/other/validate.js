/**
 * EL CODIGO PROPORCIONA FUNCIONES PARA LAS VALIDACIONES DE UN FORMULARIO
 */

// Función que verifica si un campo está vacío mandando mensaje
function isEmpty(field) {
    return (field === undefined || field === null || field === '' || field === 0 || field === false || (Array.isArray(field) && field.length === 0) || (typeof field === 'object' && Object.keys(field).length === 0));
}

// Función valida que sea un numero entero positivo
function isInteger(value) {
    return (Number.isInteger(value) && value > 0) ? true : false;
}

// Funcion para validar el no maximo de caracteres
function isLength(string, maxLength) {
    return string.length > maxLength; //Devulve true si exede el limite de caracteres
}

// Funcion que retorna verdadero o falso con el mensaje de rror, si es que exede el limite de caracteres
function isExceedingLength(field, nameFiel, maxLength) {
    let bool = false;
    if (isLength(field, maxLength)) {
        bool = true;
        notyfEM.error('Campo ' + nameFiel + '  tiene un límite máximo de ' + maxLength + ' caracteres');
    }
    return bool;
}

// FUNCION QUE RETORNA VERDADERO O FALSO CON EL MENSAJE DE ERROR, SI ES QUE NO ES UN NUMERO ENTERO POSITIVO
function isPositiveInteger(filed, nameFiel) {
    let bool = false;
    if (!isInteger(filed)) {
        bool = true;
        notyfEM.error('Campo ' + nameFiel + ' no es valido.');
    }
    return bool; // Retorna true si el campo es incorrecto
}

// FUNCION QUE RETORNA VERDADERO O FALSO CON EL MENSAJE DE ERROR, FIEL REQUIRED
function isFieldEmpty(field, nameFiel) {
    let bool = false;
    if (isEmpty(field)) {
        bool = true;
        notyfEM.error('Campo ' + nameFiel + ' es requerido.');
    }
    return bool; // Retorna true si el campo esta vacio
}