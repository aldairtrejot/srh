<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Config cloud" caption="Cloud" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_config_cloud) ? 'Modificar' : 'Agregar' }} Cloud"
                            route="{{ route('configcloud.list') }}" />
                        
                        <br>

                        <form action="{{ route('configcloud.save') }}" method="POST" class="form-sample" id="myForm">
                            @csrf

                            <x-template-form.template-form-input-hidden name="id_config_cloud"
                                value="{{ optional($item)->id_config_cloud ?? '' }}" />

                                <x-template-form.template-form-input-required label="Nombre" type="text"
                                name="nombre" placeholder="Nombre"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                                value="{{ optional($item)->nombre ?? '' }}" />

                            <x-template-form.template-form-input-required label="Descripción" type="text"
                                name="descripcion" placeholder="Descripción"
                                grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" autocomplete=""
                                value="{{ optional($item)->descripcion ?? '' }}" />

                            <x-template-form.template-form-input-required label="Valor" type="text"
                                name="valor" placeholder="Valor"
                                grid="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4" autocomplete=""
                                value="{{ optional($item)->valor ?? '' }}" />

                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" checked>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('configcloud.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</x-template-app.app-layout>