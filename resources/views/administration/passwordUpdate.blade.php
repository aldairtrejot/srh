<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SIRH</title>
    <link rel="stylesheet" href="assets/css/login/style.css" />
    <link rel="stylesheet" href="assets/icons/fontawesome-free-6.6/css/all.min.css">
    <link rel="shortcut icon" href="assets/images/imss/favicon.png" />
    <link rel="stylesheet" href="assets/messages/notyf/notyf.min.css">
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <img src="assets/images/imss/imss-bienestar-2025.png" alt="logo"
                                    style="width: 200px; height: auto;" />
                            </div>

                            <div class="alert alert-secondary role="alert">
                                <h4 class="alert-heading">Actualizar contraseña</h4>
                                <p>Por motivos de seguridad es necesario actualizar tu contraseña para proteger tu
                                    cuenta.</p>
                                <hr>
                                <p class="mb-0">
                                    La nueva contraseña debe cumplir con los siguientes requisitos:
                                <ul>
                                    <li>Mínimo 8 caracteres</li>
                                    <li>Al menos una letra mayúscula</li>
                                    <li>Al menos una letra minúscula</li>
                                    <li>Al menos un número</li>
                                    <li>Al menos un carácter especial (ejemplo: @ & . #)</li>
                                </ul>
                                </p>
                            </div>



                            <form class="pt-3" method="POST" action="{{ route('savePassword') }}">
                                @csrf

                                <div class="form-group">
                                    <input type="password" name="new_value" class="form-control form-control-lg"
                                        placeholder="Contraseña" value="{{ old('new_value') }}" />
                                    @error('new_value')
                                        <x-template-message-required>
                                            {{ $message }}
                                        </x-template-message-required>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input type="password" name="confirm_password" class="form-control form-control-lg"
                                        placeholder="Confirmar contraseña" value="{{ old('confirm_password') }}">
                                    @error('confirm_password')
                                        <x-template-message-required>
                                            {{ $message }}
                                        </x-template-message-required>
                                    @enderror
                                </div>


                                <div class="mt-3">
                                    <button type="submit"
                                        style="background-color: #6c757d; transition: background-color 0.3s ease;"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        onmouseover="this.style.backgroundColor='#5a6268'"
                                        onmouseout="this.style.backgroundColor='#6c757d'">
                                        Continuar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/messages/notyf/notyf.min.js"></script>


    @if (session('estatus'))
        <x-template-message :message="session('message')" :value="session('value')" />
    @endif

</body>

</html>
