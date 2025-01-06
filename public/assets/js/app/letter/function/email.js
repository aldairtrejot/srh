var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

function opneEmail(value) {
    sendEmail(value);
}

function sendEmail(value) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/email'),
        type: 'POST',
        data: {
            value: value,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log(response);
        },
    });
}