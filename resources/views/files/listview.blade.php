<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-panel">
        <div class="content-wrapper">

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

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">

                        <!-- Información del Empleado -->
                        <div class="contenedor mb-4">
                            <div class="item">
                                <label class="etiqueta">Nombre:</label>
                                <label id="remitente_nombre" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Primer Apellido:</label>
                                <label id="remitente_primer_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Segundo Apellido:</label>
                                <label id="remitente_segundo_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">RFC:</label>
                                <label id="remitente_rfc" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">CURP:</label>
                                <label id="remitente_curp" class="valor"></label>
                            </div>
                        </div>

                        <!-- Formulario de búsqueda -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="curp" class="form-label">CURP</label>
                                <input type="text" name="curp" id="curp" placeholder="Ingrese CURP"
                                    class="form-control form-control-sm" />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rfc" class="form-label">RFC</label>
                                <input type="text" name="rfc" id="rfc" placeholder="Ingrese RFC"
                                    class="form-control form-control-sm" />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" placeholder="Ingrese nombre"
                                    class="form-control form-control-sm" />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="primer_apellido" class="form-label">Apellido Paterno</label>
                                <input type="text" name="primer_apellido" id="primer_apellido" placeholder="Ingrese primer apellido"
                                    class="form-control form-control-sm" />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="segundo_apellido" class="form-label">Apellido Materno</label>
                                <input type="text" name="segundo_apellido" id="segundo_apellido" placeholder="Ingrese segundo apellido"
                                    class="form-control form-control-sm" />
                            </div>

                            <!-- Botón Search debajo de Apellido Paterno -->
       <div class="col-md-12 mb-3 d-flex justify-content-end">
    <button class="btn" onclick="validarcurp();" type="button"
        style="font-size: 1rem; padding: 10px 20px; background-color: rgb(235, 235, 235); color: #646464; border: none; display: inline-flex; justify-content: center; align-items: center;"
        data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar">
        <i class="fa fa-search me-1"></i> Search
    </button>
</div>


                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end column -->
        </div> <!-- end content-wrapper -->
    </div> <!-- end main-panel -->
</x-template-app.app-layout>








