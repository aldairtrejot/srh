<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    @php include(resource_path('views/config.php')); @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de Instructores" caption="Formulario de Instructor" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ optional($instructor)->id_instructor ? 'Modificar' : 'Agregar' }} Instructor"
                            route="{{ route('tableinstructor.list') }}" />
                        <div>
                            <form id="myForm" action="{{ route('tableinstructor.save') }}" method="POST" class="form-sample">
                                @csrf

                                <x-template-form.template-form-input-hidden name="id_instructor"
                                    value="{{ optional($instructor)->id_instructor }}" />

                                <x-template-tittle.tittle-caption-secon tittle="Información del Instructor" />
                                <div class="row">
                                    <x-template-form.template-form-input-required 
                                        label="ID Empleados" 
                                        type="text"
                                        name="id_empleados" 
                                        placeholder="ID del empleado"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" 
                                        autocomplete="off"
                                        value="{{ optional($instructor)->id_empleados }}" />

                                    <x-template-form.template-form-input-required 
                                        label="UUID CV" 
                                        type="text"
                                        name="uuid_cv" 
                                        placeholder="UUID del CV"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" 
                                        autocomplete="off"
                                        value="{{ optional($instructor)->uuid_cv }}" />

                                    <x-template-form.template-form-input-required 
                                        label="UUID Constancia" 
                                        type="text"
                                        name="uuid_constancia" 
                                        placeholder="UUID de la Constancia"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" 
                                        autocomplete="off"
                                        value="{{ optional($instructor)->uuid_constancia }}" />
                                </div>

                                <x-template-tittle.tittle-caption-secon tittle="Estatus y Observaciones" />
                                <div class="row">
                                    <x-template-form.template-form-select-required 
                                        :selectValue="[['id' => 1, 'value' => 'Apto'], ['id' => 0, 'value' => 'No Apto']]"
                                        :selectEdit="optional($instructor)->estatus_apto"
                                        name="estatus_apto" 
                                        tittle="Estatus Apto"
                                        grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                    <x-template-form.template-form-input-required 
                                        label="Observaciones" 
                                        type="text"
                                        name="observaciones" 
                                        placeholder="Observaciones"
                                        grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8" 
                                        autocomplete="off"
                                        value="{{ optional($instructor)->observaciones }}" />
                                </div>

                                <x-template-button.button-form-footer routeBack="{{ route('tableinstructor.list') }}" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>

<!-- CODE SCRIPT -->
