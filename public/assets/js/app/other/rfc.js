// Validacion de un rfc correcto
function validateRfc(rfc) {
    // Expresión regular para RFC de persona física (13 caracteres)
    let regexFisica = /^[A-Z&Ñ]{4}\d{6}[A-Z0-9]{3}$/;

    // Expresión regular para RFC de persona moral (12 caracteres)
    let regexMoral = /^[A-Z&Ñ]{3}\d{6}[A-Z0-9]{3}$/;

    // Convertimos el RFC a mayúsculas para hacer la comparación insensible al caso
    rfc = rfc.toUpperCase();

    // Validamos si el RFC coincide con el formato de persona física o moral
    if (regexFisica.test(rfc) || regexMoral.test(rfc)) {
        return true;
    } else {
        return false;
    }
    // Se retorna true si el rfc es correcto y false si es incorrecto
}