<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Catálogo" caption="Dependencia por Área" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_rel_dependencia_area) ? 'Modificar' : 'Agregar ' }} Dependencia por Área"
                            route="{{ route('reldependenciarea.list') }}" />
                            <br>
                            <br>
                            <form id="myForm" action="{{ route('reldependenciarea.save') }}" method="POST" class="form-sample">
                                @csrf
                            <x-template-form.template-form-input-hidden name="id_rel_dependencia_area"
                                    value="{{ optional($item)->id_rel_dependencia_area ?? '' }}" />

                                <div class="row">
                                    <x-template-form.template-form-select-required :selectValue="$selectDependencia"
                                :selectEdit="$selectDependenciaEdit" name="id_cat_dependencia" tittle="Dependencia General"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectDependenciarea"
                                :selectEdit="$selectDependenciareaEdit" name="id_cat_dependencia_area" tittle="Dependencia Especifica"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                                </div>

                                
                             <x-template-button.button-form-footer routeBack="{{ route('reldependenciarea.list') }}" />
                         </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <style>
            .bootstrap-select .dropdown-menu {
                max-height: 200px;
                /* Altura máxima para scroll vertical */
                overflow-y: auto;
                /* Scroll vertical si el contenido es más largo */
                max-width: 300px;
                /* Establece el ancho máximo, ajusta según tus necesidades */
                overflow-x: auto;
                /* Scroll horizontal si el contenido es más ancho */
            }
        </style>
<!-- CODE SCRIPT-->
<script src="/srh/public/assets/js/app/letter/reldependencia/validate.js"></script>

     </x-template-app.app-layout>