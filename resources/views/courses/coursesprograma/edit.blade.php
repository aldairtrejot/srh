<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Catalogo de cursos" caption="Programa" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($course->id_cat_programa_institucional) ? 'Modificar' : 'Agregar ' }} Curso Programa Institucional"
                            route="{{ route('coursesprograma.list') }}" />
                        
                        <br>
                        <form action="{{ route('coursesprograma.edit', $course->id_cat_programa_institucional) }}" method="POST">
                            @csrf
                            <x-template-form.template-form-input-required label="Descripcion" type="text"
                                name="descripcion" placeholder="Descripcion"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                                value="{{ $course->descripcion }}" />

                                <x-template-form.template-form-input-required label="Nombre" type="text"
                            name="nombre" placeholder="Nombre"
                            grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                            value="{{ $course->nombre }}" />
                            
                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" {{ $course->estatus ? 'checked' : '' }}>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('coursesprograma.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>