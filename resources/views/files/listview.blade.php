<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Ruta JS para buscar -->
    <script>
        const rutaBuscarEmpleado = "{{ route('files.buscar') }}";
    </script>

    <div class="main-panel">
        <div class="content-wrapper">

            <!-- Título -->
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header 
                            tittle="Control de Archivos" 
                            caption="Información del Usuario" 
                        />
                    </div>
                </div>
            </div>

            <!-- Tarjeta principal -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <!-- Formulario búsqueda -->
                        <div class="row align-items-end">
                            <!-- Input -->
                            <div class="col-12 col-md-9 mb-3">
                                <label for="datos" class="form-label">Datos del Usuario</label>
                                <input type="text" name="datos" id="datos" placeholder="Ingrese datos del Usuario"
                                    class="form-control form-control-sm" />
                            </div>

                            <!-- Botón -->
                            <div class="col-12 col-md-3 mb-3 text-md-end">
                                <label class="form-label d-none d-md-block invisible">Buscar</label>
                                <button class="btn w-100 w-md-auto" onclick="validarcurp();" type="button"
                                    style="font-size: 1rem; padding: 10px 20px; background-color: rgb(235, 235, 235); color: #646464; border: none; display: inline-flex; justify-content: center; align-items: center;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar">
                                    <i class="fa fa-search me-1"></i>Buscar
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de resultados -->
                        <x-template-table.template-table>
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>RFC</th>
                                    <th>Curp</th>
                                    <th>Nombre</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Fecha Movimiento</th>
                                    <th>Nombre Movimiento</th>
                                </tr>
                            </thead>
                            <tbody id="contenidoTabla">
                                {{-- Resultados por JS --}}
                            </tbody>
                        </x-template-table.template-table>

                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end column -->

        </div> <!-- end content-wrapper -->
    </div> <!-- end main-panel -->

    <!-- Script externo -->
    <script src="{{ asset('assets/js/app/files/files/form.js') }}"></script>
</x-template-app.app-layout>










