<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Catálogo" caption="Documentos" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_cat_documento) ? 'Modificar' : 'Agregar' }} Documento"
                            route="{{ route('filesdocument.list') }}" />

                        <br>

                        <form id="myForm" action="{{ route('filesdocument.save') }}" method="POST" class="form-sample">

                            @csrf

                            {{-- Campo oculto para edición --}}
                            @if(isset($item->id_cat_documento))
                                <input type="hidden" name="id_cat_documento" value="{{ $item->id_cat_documento }}">
                            @endif

                            <div class="row">
                                <x-template-form.template-form-input-required 
                                    label="Descripción"
                                    type="text"
                                    name="descripcion"
                                    id="descripcion"
                                    placeholder="Descripción"
                                    grid="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8"
                                    autocomplete=""
                                    value="{{ optional($item)->descripcion ?? '' }}" />


                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                    <label for="estatus">Estatus</label>
                                    <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" 
                                        {{ isset($item->estatus) && $item->estatus ? 'checked' : '' }}>
                                </div>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('filesdocument.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <script src="{{ asset('assets/js/app/files/filesdocuments/validate.js') }}"></script>
</x-template-app.app-layout>
