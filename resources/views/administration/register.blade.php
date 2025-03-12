<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SIRH</title>
    <link rel="stylesheet" href="assets/css/login/style.css" />
    <!--
    <link rel="shortcut icon" href="../../images/favicon.png" />
-->
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
                            <h6 class="font-weight-light">Registro</h6>
                            <form class="pt-3">
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" placeholder="CURP" />
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" placeholder="Correo electrónico" />
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" placeholder="Confirmar correo electrónico" />
                                </div>
                                <div class="mt-3">
                                    <a style="background-color: #555555"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        href="../../index.html">Enviar</a>
                                </div>
                                <div class="text-center mt-4 font-weight-light">
                                    ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-primary">
                                        Inicia sesi&oacuten</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>