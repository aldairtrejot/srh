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

            <!-- Información del Empleado -->
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

                        <hr>

                        <!-- Checklist de Documentos -->
                        <x-template-tittle.tittle-caption-secon tittle="Checklist de Documentos" />

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Documento</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentos as $doc)
                                        @php
                                            $checked = $estatusMap[$doc->id_cat_documento]->estatus ?? false;
                                        @endphp
                                        <tr>
                                            <td>{{ $doc->descripcion }}</td>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    class="check-doc"
                                                    data-id-documento="{{ $doc->id_cat_documento }}"
                                                    data-id-gestion="{{ $doc->id_tbl_gestion_documentos }}"
                                                    {{ $checked ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Alerta tipo burbuja -->
                        <div id="alerta-exito" class="alerta-guardar" style="display: none;">
                            Guardado correctamente
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JSON: Pasar datos del empleado -->
    <script>
        const empleadoData = {!! $empleado_json !!};
        const BASE_URL = "{{ url('/') }}";  // Ejemplo: http://localhost/srh/public
    </script>

    <!-- Scripts externos -->
    <script src="{{ asset('assets/js/app/files/fileschecklist/table.js') }}"></script>
    <script src="{{ asset('assets/js/app/files/fileschecklist/checklist.js') }}"></script>

    <!-- Estilo para burbuja -->
    <style>
        .alerta-guardar {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: bold;
            opacity: 0.95;
            transition: opacity 0.3s ease;
        }
    </style>
</x-template-app.app-layout>


