<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRH</title>
    <link rel="stylesheet" href="assets/css/login/style.css" />
    <link rel="shortcut icon" href="assets/images/imss/favicon.png" />
    <link rel="stylesheet" href="assets/icons/fontawesome-free-6.6/css/all.min.css">
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="assets/images/imss/logo_imss.png" alt="logo"
                                    style="width: 300px; height: auto" />
                            </div>
                            <h4>Sistema Integral para Recursos Humanos</h4>
                            <h6 class="font-weight-light">Respuesta</h6>

                            <div class="message-container">
                                @if(session('existsEmail'))
                                    <div>
                                        <i class="fas fa-check-circle success-message"></i>
                                    </div>
                                    <div class="message-text">
                                        Se ha enviado una nueva contraseña al correo:
                                        <strong class="email">{{ session('email') }}</strong>
                                    </div>
                                @else
                                    <div>
                                        <i class="fas fa-times-circle error-message"></i>
                                    </div>
                                    <div class="message-text">
                                        El correo <strong class="email">{{ session('email') }}</strong> no se
                                        encuentra registrado.
                                        Por favor, verifica la dirección e inténtalo de nuevo.
                                    </div>
                                @endif
                            </div>

                            <div class="text-center mt-4 font-weight-light">
                                <a href="{{ route('login') }}" class="text-primary">
                                    Regresar al inicio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>