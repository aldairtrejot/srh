<!--
<style>
    
    .bootstrap-select .dropdown-menu {
    max-height: 200px; 
    overflow-y: auto; 
}
</style>
-->

<div class="{{ $grid }}">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" style="font-size: 1rem; color: #333;">{{ $tittle ?? 'Área' }}</label>
        <div class="col-sm-9">
            <div class="col-md-12">
                <select  class="form-control custom-select selectpicker" data-style="input-select-selectpicker"
                    aria-label="Default select example" data-live-search="true" data-none-results-text="Sin resultados"
                    name="{{ $name }}" id="{{ $name }}">
                    <option value="">SELECCIONE</option>
                    @foreach ($selectValue as $select)
                        <option value="{{ $select->id }}" 
                            @if (old($name, isset($selectEdit->id) ? $selectEdit->id : '') == $select->id)
                                selected
                            @endif>
                            {{ $select->descripcion }}
                        </option>
                    @endforeach
                </select>
                @error($name)
                    <small style="color:red; font-family: Arial, sans-serif;">
                        <i class="fas fa-exclamation-circle" style="color:red;"></i>
                        {{ $message }}
                    </small>
                @enderror
            </div>
        </div>
    </div>
</div>