<div class="{{ $grid }}">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" style="font-size: 1rem; color: #333;">{{ $label }}</label>
        <div class="col-sm-9">
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}"
                autocomplete="{{ $autocomplete }}" value="{{ old($name, $value) }}" class="form-control"
                style="font-size: 1rem;" />

            @error($name)
                <small style="color:red; font-family: Arial, sans-serif;">
                    <i class="fas fa-exclamation-circle" style="color:red;"></i>
                    {{ $message }}
                </small>
            @enderror
        </div>
    </div>
</div>