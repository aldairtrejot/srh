var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

function opneEmail(id, value) {
    sendEmailLetter(id, value, 'nameUser', 'mail');
}

function sendEmailLetter(id, value, nameUser, mail) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/email'),
        type: 'POST',
        data: {
            value: value,
            id: id,
            nameUser: nameUser,
            mail: mail,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log(response);
        },
    });
}