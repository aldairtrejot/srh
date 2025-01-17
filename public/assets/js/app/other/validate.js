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

// Funcion para validar el no minimo de caracteres
function isMinLength(string, maxLength) {
    return string.length < maxLength; //Devulve true si exede el limite de caracteres
}

//Retorna verdadero si los valores son iguales
function isEqual(value1, value2) {
    return value1 === value2;
}

// Retorna verdero si el mail es correcto y false si es incorrecto 
function isMail(mail) {
    // Expresión regular para validar un email
    let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Patron mail
    return regex.test(mail);
}

// Valida que el correo electronico sea verdero o incorecto
function validateMail(value1) {
    let bool = false;
    if (!isMail(value1)) {
        bool = true;
        notyfEM.error('El correo electrónico ingresado no es válido.');
    }
    return bool;
}


//Valida que los valores sean iguales de lo contrario manda msj de error
function validateEqual(value1, value2) {
    let bool = false;
    if (!isEqual(value1, value2)) {
        bool = true;
        notyfEM.error('Las contraseñas no coinciden.');
    }
    return bool;
}

// Funcion que retorna verdadero o falso con el mensaje de rror, si es que exede el limite minimo de caracteres
function isExceedingMinLength(field, nameFiel, maxLength) {
    let bool = false;
    if (isMinLength(field, maxLength)) {
        bool = true;
        notyfEM.error('Campo ' + nameFiel + '  tiene un límite máximo de ' + maxLength + ' caracteres');
    }
    return bool;
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