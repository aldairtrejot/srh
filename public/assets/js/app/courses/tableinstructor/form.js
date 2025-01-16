// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});


function validarcurp (){
    let curp=$('#curp').val();
    if (curp ===''){
        console.log('vacio');
    }
    else {
        console.log(curp);
        $.ajax({
            url: URL_DEFAULT.concat('/tableinstructor/table/dataClave'),
            type: 'POST',
            data: {
                curp: curp,

                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                console.log(response) // establecer los valores
            },
        });
    }
}