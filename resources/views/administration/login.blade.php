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

    <style>
        /* Icono del ojo */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 2;
        }

        #password.form-control {
            padding-right: 2.5rem;
        }

        .d-none {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">

                            <div class="brand-logo text-center mb-3">
                                <img src="assets/images/imss/imss-bienestar-2025.png" alt="logo"
                                     style="width: 200px; height: auto;" />
                            </div>

                            <h3 class="text-center" style="font-weight: 700;">Sistema Integral de Recursos Humanos</h3>
                            <br>
                            <h4>Control de Gestión</h4>
                            <h6 class="font-weight-light">Iniciar sesión</h6>

                            <form class="pt-3" method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- USUARIO --}}
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control form-control-lg"
                                           placeholder="Usuario" value="{{ old('email') }}" autocomplete="username" />
                                    @error('email')
                                        <x-template-message-required>{{ $message }}</x-template-message-required>
                                    @enderror
                                </div>

                                {{-- CONTRASEÑA CON OJO --}}
                                <div class="form-group position-relative">
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-lg"
                                        placeholder="Contraseña" autocomplete="current-password"
                                        oninput="checkPasswordInput()">

                                    <!-- Ícono del ojo (oculto al inicio) -->
                                    <span id="togglePasswordContainer" class="toggle-password d-none"
                                          onclick="togglePassword()">
                                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                                    </span>

                                    @error('password')
                                        <x-template-message-required>{{ $message }}</x-template-message-required>
                                    @enderror
                                </div>

                                {{-- BOTÓN --}}
                                <div class="mt-3">
                                    <button type="submit"
                                        style="background-color: #6c757d; transition: background-color 0.3s ease;"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        onmouseover="this.style.backgroundColor='#5a6268'"
                                        onmouseout="this.style.backgroundColor='#6c757d'">
                                        Ingresar
                                    </button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    <a href="#" class="text-primary">¿Olvidaste tu contraseña?</a>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notificaciones --}}
    <script src="assets/messages/notyf/notyf.min.js"></script>

    {{-- Mostrar notificaciones --}}
    @if (session('estatus'))
        <x-template-message :message="session('message')" :value="session('value')" />
    @endif

    {{-- Script: Mostrar/Ocultar Contraseña --}}
    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eyeIcon");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Mostrar ojo solo cuando hay texto
        function checkPasswordInput() {
            const input = document.getElementById("password");
            const iconContainer = document.getElementById("togglePasswordContainer");

            if (input.value.trim() === "") {
                iconContainer.classList.add("d-none");
            } else {
                iconContainer.classList.remove("d-none");
            }
        }
    </script>

</body>
</html>


