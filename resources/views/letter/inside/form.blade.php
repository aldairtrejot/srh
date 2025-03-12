<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <x-template-tittle.tittle-header tittle="Instructor" caption="Instructor" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                        <x-template-tittle.tittle-caption
                            tittle="{{ isset($item->id_tbl_instructores) ? 'Modificar' : 'Agregar' }} Instructor"
                            route="{{ route('tableinstructor.list') }}" />

                        <form action="{{ route('tableinstructor.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_tbl_instructores" value="{{ $item->id_tbl_instructores ?? '' }}">

                            <div class="form-group">
                                <label for="curp">CURP</label>
                                <input type="text" name="curp" id="curp" class="form-control"
                                       value="{{ old('curp', $item->curp ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="estatus">Estatus</label>
                                <input type="hidden" name="estatus" value="0">
                                <input type="checkbox" id="estatus" name="estatus" value="1"
                                       {{ old('estatus', $item->estatus ?? false) ? 'checked' : '' }}>
                            </div>

                            <x-template-button.button-form-footer routeBack="{{ route('tableinstructor.list') }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-template-app.app-layout>
