<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Catálogo de dependencia" caption="Dependencia" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_cat_dependencia) ? 'Modificar' : 'Agregar' }} Dependencia"
                            route="{{ route('dependencia.list') }}" />
                        
                        <br>

                        <form action="{{ route('dependencia.save') }}" method="POST" class="form-sample" id="myForm">
                            @csrf

                            <x-template-form.template-form-input-hidden name="id_cat_dependencia"
                                value="{{ optional($item)->id_cat_dependencia ?? '' }}" />

                            <x-template-form.template-form-input-required label="Descripción" type="text"
                                name="descripcion" placeholder="Descripción"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                                value="{{ optional($item)->descripcion ?? '' }}" />


                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch"
                                    {{ optional($item)->estatus ? 'checked' : '' }}>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('dependencia.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/srh/public/assets/js/app/letter/dependencia/validate.js"></script>
</x-template-app.app-layout>