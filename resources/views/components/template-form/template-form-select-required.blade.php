<div class="{{ $grid }}">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" style="font-size: 1rem; color: #333;">{{ $tittle ?? 'Área' }}</label>
        <div class="col-sm-9">
            <div class="col-md-12">
                <select class="form-control custom-select selectpicker" 
                        data-style="input-select-selectpicker"
                        aria-label="Default select example" 
                        data-live-search="true" 
                        data-none-results-text="Sin resultados"
                        name="{{ $name }}" 
                        id="{{ $name }}">
                    <option value="">SELECCIONE</option>
                    @if (!empty($selectValue) && is_iterable($selectValue))
                        @foreach ($selectValue as $option)
                            @php
                                $optionId = is_array($option) ? $option['id'] ?? '' : $option->id ?? '';
                                $optionValue = is_array($option) ? $option['value'] ?? '' : $option->value ?? '';
                            @endphp
                            <option value="{{ $optionId }}">{{ $optionValue }}</option>
                        @endforeach
                    @else
                        <option disabled>No hay opciones disponibles</option>
                    @endif
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
