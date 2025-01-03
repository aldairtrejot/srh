<!-- TEMPLATE APP -->
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Alfresco" caption="Nube" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tipocursos) ? 'Cargar' : '' }} Subir Archivos"
                            route="{{ route('tableinstructor.list') }}" />
                        
                        <br>
                        <form action="{{ route('alfresco.upload.file') }}" method="POST" enctype="multipart/form-data">
                            @csrf  <!-- Campo CSRF para proteger la solicitud -->

                            <x-template-form.template-form-input-required label="Curp" type="text"
                                name="name" placeholder="Curp"
                                grid="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3" autocomplete=""
                                value="" />
                            <br><br>

                            <!-- Subir CV -->
                            <label for="file_cv">Sube tu CV:</label>
                            <input type="file" name="file_cv" id="file_cv" required onchange="showFileNameAndPreview('cv')"><br><br>
                            
                            <p id="file-name-cv"></p>
                            <div id="image-preview-container-cv" style="display:none; max-width: 150px; max-height: 150px;">
                                <img id="image-preview-cv" src="" alt="Vista previa de imagen" style="width: 100%; height: 100%; object-fit: contain;" />
                            </div>
                            <div id="pdf-preview-container-cv" style="display:none; width: 150px; height: 150px; overflow: hidden;">
                                <embed id="pdf-preview-cv" src="" type="application/pdf" width="100%" height="100%" />
                            </div>

                            <br><br>

                            <!-- Subir Constancia -->
                            <label for="file_constancia">Sube tu Constancia:</label>
                            <input type="file" name="file_constancia" id="file_constancia" required onchange="showFileNameAndPreview('constancia')"><br><br>
                            
                            <p id="file-name-constancia"></p>
                            <div id="image-preview-container-constancia" style="display:none; max-width: 150px; max-height: 150px;">
                                <img id="image-preview-constancia" src="" alt="Vista previa de imagen" style="width: 100%; height: 100%; object-fit: contain;" />
                            </div>
                            <div id="pdf-preview-container-constancia" style="display:none; width: 150px; height: 150px; overflow: hidden;">
                                <embed id="pdf-preview-constancia" src="" type="application/pdf" width="100%" height="100%" />
                            </div>
                            <br><br>

                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                <label for="estatus">Estatus</label>
                                <input type="checkbox" id="estatus" name="estatus" class="toggle-switch" checked>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('alfresco.upload.form') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/app/courses/alfresco/table.js') }}"></script>
</x-template-app.app-layout>

