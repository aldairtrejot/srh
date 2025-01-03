<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Catalogo de cursos" caption="Auditoria" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_auditoria) ? 'Modificar' : 'Agregar ' }} Curso Auditoria"
                            route="{{ route('coursesauditoria.list') }}" />
                        
                        <br>
                       <!--<x-template-tittle.tittle-caption-secon tittle="Información Catalogo Beneficio" />-->

                       <form action="{{ route('coursesauditoria.save') }}" method="POST" class="form-sample">
                        @csrf
                        <x-template-form.template-form-input-required label="Descripcion" type="text"
                            name="descripcion" placeholder="Descripcion"
                            grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                            value="{{ optional($item)->descripcion ?? '' }}" />
                    
                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" checked>
                            </div>
                    
                        <x-template-button.button-form-footer routeBack="{{ route('coursesauditoria.list') }}" />
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>