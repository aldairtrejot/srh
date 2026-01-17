<!-- TEMPLATE APP-->
<x-template-app.app-layout>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Sistema Integral para Recursos Humanos</h3>
                            <h5 class="font-weight-normal mb-0">Usuarios</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Usuarios del sistema</h4>
                                <p class="card-description">
                                    ¿Deseas agregar un usuario nuevo? <a href="{{ route('user.create') }}"
                                        class="text-danger" style="margin-left: 10px;">
                                        <i class="fa fa-arrow-up"></i> Agregar Usuario
                                    </a>
                                </p>
                            </div>
                            <div class="input-group" style="max-width: 300px;">
                                <!-- TEMPLATE SEARCH-->
                                <x-template-table.template-search />
                            </div>
                        </div>








                        <!-- ON SCRIP FOR MODAL -->

                        <style>
                            /* El fondo del modal */
                            .modal {
                                display: none;
                                /* Ocultar el modal por defecto */
                                position: fixed;
                                /* Fijar el modal a la pantalla */
                                z-index: 1;
                                /* Asegurar que el modal esté por encima del contenido */
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                background-color: rgba(0, 0, 0, 0.3);
                                /* Fondo oscuro ligeramente opaco */
                            }

                            /* Estilos del contenido del modal */
                            .modal-content {
                                background-color: white;
                                margin: 15% auto;
                                /* Centrar el modal en la pantalla */
                                padding: 20px;
                                border: 0px solid #333;
                                /* Bordes oscuros */
                                width: 80%;
                                /* Ancho responsive */
                                max-width: 500px;
                                /* Máximo ancho */
                                border-radius: 0px;
                                /* Bordes cuadrados */
                            }

                            /* Estilo para el botón de cierre */
                            .close {
                                color: #333;
                                font-size: 30px;
                                font-weight: bold;
                                position: absolute;
                                top: 10px;
                                right: 20px;
                                cursor: pointer;
                            }

                            /* Cambiar color cuando el botón de cierre es hover */
                            .close:hover,
                            .close:focus {
                                color: #f44336;
                                text-decoration: none;
                                cursor: pointer;
                            }

                            /* Ajustar el tamaño del modal en pantallas pequeñas */
                            @media screen and (max-width: 768px) {
                                .modal-content {
                                    width: 90%;
                                    /* En pantallas pequeñas, el modal ocupará un 90% del ancho */
                                }
                            }

                            /* Línea debajo del título */
                            .modal-header {
                                border-bottom: 1px solid #ced4da;
                                /* Línea más gruesa debajo del título */
                                padding-bottom: 10px;
                                margin-bottom: 10px;
                            }

                            /* Estilo para el pie del modal (footer) */
                            .modal-footer {
                                display: flex;
                                justify-content: flex-end;
                                /* Esto mueve el contenido (el botón) hacia la derecha */
                                margin-top: 20px;
                                /* Espaciado superior */
                            }

                            /* Estilo de los botones en el footer */
                            .modal-footer .btn {
                                font-size: 1rem;
                                padding: 10px 20px;
                                margin-left: 10px;
                                border: none;
                            }

                            .modal-footer .btn-secondary {
                                background-color: #6c757d;
                                color: white;
                            }

                            /*
#ced4da
*/
                            .modal-footer .btn-primary {
                                background-color: #10312B;
                                color: white;
                            }
                        </style>

                        <!-- FINALLY ON THE SCRIP -->
                        <!-- Botón para abrir el modal -->
                         <!--
                        <button id="openModal" style="font-size: 1rem; padding: 10px 20px;">Abrir Modal</button>
                        -->
                        <!-- El modal -->
                        <div id="myModal" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="font-weight-bold d-flex align-items-center" style="width: 100%;">
                                        <i class="fa fa-lock" style="font-size: 1.5rem; margin-right: 10px;"></i>
                                        Actualizar contraseña
                                    </h3>
                                    <span class="close" style="cursor: pointer; font-size: 30px;">&times;</span>
                                </div>
                                <br>

                                <form class="form-sample">
                                    @csrf
                                    <div class="row">
                                        <x-template-form.template-form-input-required label="Contraseña" type="password"
                                            name="newPassword" placeholder="Nueva contraseña" grid="12" autocomplete=""
                                            value="" />
                                    </div>

                                    <div class="modal-footer">
                                        <div class="d-flex justify-content-end">
                                            <a class="btn btn-secondary" href="#" id="closeModal"
                                                style="font-size: 1rem; padding: 10px 20px; margin-left: 10px; background-color: #6c757d; color: white; border: none;">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                            <button onclick="passwordSave();"
                                                style="font-size: 1rem; padding: 10px 20px; margin-left: 10px; background-color: #10312B; color: white; border: none;">
                                                <i class="fas fa-save"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script>




                            var modal = document.getElementById("myModal");
                            var btn = document.getElementById("openModal");
                            var span = document.getElementsByClassName("close")[0];
                            var closeModal = document.getElementById("closeModal");

                            function showModalTempleta(userID) {
                                showModal();
                            }

                            function showModal() {
                                modal.style.display = "block";
                            }

                            function passwordSave() {
                            }



                            /*
                                                        document.addEventListener("DOMContentLoaded", function () {
                            
                            
                            
                                                            // Asegurarse de que los elementos estén cargados
                            
                            
                                                            // Verificar si los elementos existen antes de asignarles eventos
                                                            if (modal && btn && span && closeModal) {
                                                                // Función para ocultar el modal
                                                                function hiddenModal() {
                                                                    modal.style.display = "none";
                                                                }
                            
                                                                // Función para mostrar el modal
                                                                function showModal() {
                                                                    modal.style.display = "block";
                                                                }
                            
                                                                // Abrir el modal cuando el usuario haga clic en el botón
                                                                btn.onclick = function () {
                                                                    showModal();
                                                                }
                            
                                                                // Cerrar el modal cuando el usuario haga clic en el botón de cierre (span)
                                                                span.onclick = function () {
                                                                    hiddenModal();
                                                                }
                            
                                                                // Cerrar el modal cuando el usuario haga clic en el botón de "Cancelar"
                                                                closeModal.onclick = function () {
                                                                    hiddenModal();
                                                                }
                            
                                                                // Cerrar el modal si el usuario hace clic fuera del contenido del modal
                                                                window.onclick = function (event) {
                                                                    if (event.target == modal) {
                                                                        hiddenModal();
                                                                    }
                                                                }
                                                            }
                                                        });
                                                        */
                        </script>




                        <!-- TEMPLATE TABLE -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>
                                        Menú
                                    </th>
                                    <th>
                                        Usuario
                                    </th>
                                    <th>
                                        Correo electrónico
                                    </th>
                                </tr>
                            </thead>
                        </x-template-table.template-table>

                        <!-- TEMPLATE PAGINATOR-->
                        <x-template-table.template-paginator />

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CODE SCRIPT-->
    <script src="{{ asset('assets/js/app/administration/user/table.js') }}"></script>

</x-template-app.app-layout>