<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Expediente" caption="Empleado" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        
                        <x-template-tittle.tittle-caption-secon tittle="Información del Empleado" />

                        <!-- Contenedor de Resultados -->
                        <div class="contenedor">
                            <div class="item">
                                <label class="etiqueta">Nombre:</label>
                                <label id="file_nombre" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Primer Apellido:</label>
                                <label id="file_primer_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Segundo Apellido:</label>
                                <label id="file_segundo_apellido" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">RFC:</label>
                                <label id="file_rfc" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">CURP:</label>
                                <label id="file_curp" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Unidad:</label>
                                <label id="file_unidad" class="valor"></label>
                            </div>
                            <div class="item">
                                <label class="etiqueta">Coordinación:</label>
                                <label id="file_coordinacion" class="valor"></label>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasar datos de empleado desde PHP a JavaScript -->
    <script>
        const empleadoData = {!! $empleado_json !!};
    </script>
    
    <!-- Script JS externo -->
    <script src="{{ asset('assets/js/app/files/fileschecklist/table.js') }}"></script>
</x-template-app.app-layout>
