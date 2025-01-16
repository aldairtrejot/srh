<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Gestión de Cursos" caption="Cursos" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_cursos) ? 'Modificar' : 'Agregar ' }} Cursos"
                            route="{{ route('tablecourses.list') }}" />
                            <br>
                            <div class="row">

                                <x-template-form.template-form-select-required :selectValue="$selectTipocurso"
                                :selectEdit="$selectTipoCursoEdit" name="id_cat_tipo_cursos" tittle="Tipo Curso"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectCoordinacion"
                                :selectEdit="$selectCoordinacionEdit" name="id_cat_coordinacion" tittle="Coordinacion"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectEstatuto"
                                :selectEdit="$selectEstatutoEdit" name="id_cat_estatuto_organico" tittle="Estatuto Organico"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                            </div>
                            
                            <div class="row">

                                <x-template-form.template-form-select-required :selectValue="$selectBeneficio"
                                :selectEdit="$selectBeneficioEdit" name="id_cat_beneficio" tittle="Beneficio"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectOrganizacion"
                                :selectEdit="$selecOrganizacionEdit" name="id_cat_organizacion" tittle="Organizacion"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectTipoaccion"
                                :selectEdit="$selectTipoAccionEdit" name="id_cat_tipo_accion" tittle="Tipo Accion"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                            </div>
                            <div class="row">
                                <x-template-form.template-form-select-required :selectValue="$selectModalidad"
                                :selectEdit="$selectModalidadEdit" name="id_cat_modalidad" tittle="Modalidad"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />

                                <x-template-form.template-form-select-required :selectValue="$selectCategoria"
                                :selectEdit="$selectCategoriaEdit" name="id_cat_categoria" tittle="Categoria"
                                grid="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4" />
                            </div>

                            <div class="row">
                                <x-template-form.template-form-select-required :selectValue="$selectNomaccion"
                            :selectEdit="$selectNomaccionEdit" name="id_cat_nombre_accion" tittle="Nombre Accion"
                            grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" />
                            </div>

                            <div class="row">
                                <x-template-form.template-form-select-required :selectValue="$selectPrograma"
                            :selectEdit="$selectProgramaEdit" name="id_cat_programa_institucional" tittle="Programa Institucional"
                            grid="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" />
                            </div>

                             <x-template-button.button-form-footer routeBack="{{ route('tablecourses.list') }}" />
                         </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </x-template-app.app-layout>