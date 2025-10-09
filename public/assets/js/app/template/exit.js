document.addEventListener("DOMContentLoaded", function () {
    // Valor de Laravel (.env), en minutos
    const sessionLifetime = 1; //window.SESSION_LIFETIME - 2;
    const sessionMs = sessionLifetime * 60 * 1000; // convertir a ms

    // Contador regresivo
    setTimeout(() => {
        // Aquí abres el modal cuando el tiempo expira
        console.log('logout...')
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: URL_DEFAULT.concat('/session-lifetime-logout'),
            type: 'POST',
            data: {
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                console.log(response)
                if (response.status) {
                    window.location.href = URL_DEFAULT.concat('/login');
                } else {
                    notyfEM.error("Ocurrió un error inesperado. Por favor, actualiza la página e inténtalo nuevamente.");
                }
            },
        });
    }, sessionMs);
});