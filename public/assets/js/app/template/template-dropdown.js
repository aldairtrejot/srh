//La funcion hace grande o pequeño el dropw de una tabla. se pasa como parametro la cantidad de registros

function talldropdown(value, minValue) {
    // Verificar el número de registros
    if (value <= minValue) {
        // Si hay un solo registro, activar la clase para el scroll
        $('.dropdown-menu').css({
            'max-height': '100px',
            'overflow-y': 'auto'
        });
    } else {
        // Si hay más de uno, quitar las clases que agregan el scroll
        $('.dropdown-menu').css({
            'max-height': '',
            'overflow-y': ''
        });
    }
}

