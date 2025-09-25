<!-- resources/views/components/template-form/template-form-select-required.blade.php -->

{{-- ===== normalización mínima ===== --}}
@php
    // 1) lee el id seleccionado desde selectEdit (objeto o [obj])
    $editObj = null;
    if (!empty($selectEdit)) {
        $editObj = is_array($selectEdit) ? (collect($selectEdit)->first()) : $selectEdit;
    }
    $selectedFromEdit = is_object($editObj) ? ($editObj->id ?? null) : null;

    // 2) prioridad: old() -> valueSelected (opcional) -> selectedFromEdit
    $valueSelected = isset($valueSelected) ? (string)$valueSelected : null;
    $selectedId = old($name, $valueSelected ?? $selectedFromEdit);
    $selectedId = isset($selectedId) ? (string)$selectedId : null;

    // 3) normaliza la lista a id/label (acepta descripcion o name)
    $options = collect($selectValue ?? [])->map(function ($r) {
        if (is_array($r)) $r = (object)$r;
        $r->id    = isset($r->id) ? (string)$r->id : null;
        $r->label = isset($r->descripcion) ? (string)$r->descripcion : ((isset($r->name) ? (string)$r->name : ''));
        return $r;
    });

    // 4) si el seleccionado no está en options, lo inyectamos con su etiqueta
    if ($selectedId !== null && $selectedId !== '' && $options->where('id', $selectedId)->count() === 0) {
        $editLabel = is_object($editObj) ? ($editObj->descripcion ?? $editObj->name ?? null) : null;
        $options->prepend((object)[
            'id'    => (string)$selectedId,
            'label' => strtoupper($editLabel ?: 'VALOR SELECCIONADO'),
        ]);
        $options = $options->unique('id')->values();
    }

    // 5) orden natural por etiqueta
    $options = $options->sortBy('label', SORT_NATURAL|SORT_FLAG_CASE)->values();
@endphp
{{-- ===== fin normalización ===== --}}

<!--
<style>
    .bootstrap-select .dropdown-menu { max-height: 200px; overflow-y: auto; }
</style>
-->

<div class="{{ $grid }}">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" style="font-size: 1rem; color: #333;">
            {{ $tittle ?? 'Área' }}
        </label>
        <div class="col-sm-9">
            <div class="col-md-12">
                <select class="form-control custom-select selectpicker"
                        data-style="input-select-selectpicker"
                        aria-label="Default select example"
                        data-live-search="true"
                        data-none-results-text="Sin resultados"
                        name="{{ $name }}" id="{{ $name }}">
                    <option value="">SELECCIONE</option>

                    @foreach ($options as $select)
                        <option value="{{ $select->id }}"
                            @if ($selectedId !== null && (string)$selectedId === (string)$select->id) selected @endif>
                            {{ $select->label }}
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

