<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Registro" caption="Alumnos" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_instructores) ? 'Modificar' : 'Agregar' }} Alumnos"
                            route="{{ route('assignedcourse.list') }}" />

                        <x-template-tittle.tittle-caption-secon tittle="Información del Trabajador" />

                        <!-- Contenedor de Resultados -->
                    <div class="contenedor">
                    <div class="item">
                    <label class="etiqueta">Nombre:</label>
                    <label id="label_nombre" class="valor">{{ $item->nombre ?? '_' }}</label>
                    </div>
                    <div class="item">
                    <label class="etiqueta">Primer Apellido:</label>
                    <label id="label_primer_apellido" class="valor">{{ $item->primer_apellido ?? '_' }}</label>
                    </div>
                    <div class="item">
                    <label class="etiqueta">Segundo Apellido:</label>
                    <label id="label_segundo_apellido" class="valor">{{ $item->segundo_apellido ?? '_' }}</label>
                    </div>
                    <div class="item">
                    <label class="etiqueta">RFC:</label>
                    <label id="label_rfc" class="valor">{{ $item->rfc ?? '_' }}</label>
                    </div>
                    </div>

                    <br>

                        <!-- FORMULARIO -->
                        <form id="form-instructor"
                            action="{{ isset($item->id_tbl_instructores) ? route('tableinstructor.update', $item->id_tbl_instructores) : route('tableinstructor.save') }}"
                            method="POST"
                            class="form-sample">
                            
                            @csrf

                            @if(isset($item->id_tbl_instructores))
                                @method('PUT')
                            @endif

                            <!-- 🔹 Campos ocultos para ID e identificación de edición -->
                            <input type="hidden" name="id_tbl_instructores" id="id_tbl_instructores" value="{{ $item->id_tbl_instructores ?? '' }}">
                            <input type="hidden" name="is_editing" id="is_editing" value="{{ isset($item->id_tbl_instructores) ? '1' : '0' }}">

                            <!-- Campos ocultos para asegurar que los valores se llenan al editar -->
                            <input type="hidden" id="nombre" value="{{ $item->nombre ?? '' }}">
                            <input type="hidden" id="primer_apellido" value="{{ $item->primer_apellido ?? '' }}">
                            <input type="hidden" id="segundo_apellido" value="{{ $item->segundo_apellido ?? '' }}">
                            <input type="hidden" id="rfc" value="{{ $item->rfc ?? '' }}">

                            <div class="row align-items-center">
                                <!-- Campo CURP -->
                                <div class="col-md-10 d-flex align-items-center">
                                    <div class="form-group flex-grow-1">
                                        <label for="curp">CURP</label>
                                        <input type="text" name="curp" id="curp"
                                            placeholder="Ingrese CURP"
                                            autocomplete="off"
                                            value="{{ $item->curp ?? '' }}"
                                            class="form-control" />
                                    </div>
                                    <!-- Botón CONSULTAR (Solo se oculta en edición) -->
                                    <button id="boton-consultar-curp" class="btn ml-2" onclick="validarcurp();" type="button"
                                        style="background-color:rgb(235, 235, 235); color:#646464; border: none;display: inline-flex; justify-content: center; align-items: center;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar CURP"
                                        {{ isset($item->id_tbl_instructores) ? 'disabled' : '' }}>
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Campo Estatus -->
                            <div class="col-4">
                                <label for="estatus">Estatus</label>
                                <input type="hidden" name="estatus" value="0">
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" value="1"
                                    {{ isset($item->estatus) && $item->estatus == true ? 'checked' : '' }}>
                            </div>

                            <br>

                            <x-template-button.button-form-footer routeBack="{{ route('tableinstructor.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>

<!-- CODE SCRIPT -->
<!--<script src="{{ asset('assets/js/app/courses/tableinstructor/form.js') }}"></script>-->