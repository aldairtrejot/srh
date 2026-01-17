<!-- TAMPLATE FOR CHECK -->
<div class="form-check">
    <label class="form-check-label">
        <input name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" type="checkbox" class="form-check-input" checked>
        {{ $text }}
    </label>
</div>