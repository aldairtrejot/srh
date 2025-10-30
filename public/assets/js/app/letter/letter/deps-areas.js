// public/assets/js/app/letter/letter/deps-areas.js — FINAL ÚNICO (edición segura)
// Maneja TODA la jerarquía A1 -> A2 -> A3 y dispara fillDependentsFromArea
// desde aquí. select.js SOLO define la función fillDependentsFromArea y
// los encadenamientos (Unidad->Coordinación, Trámite->Clave, etc).

document.addEventListener('DOMContentLoaded', function () {
  // Evita doble inicialización si el script se carga 2 veces
  if (window.__DEPS_AREAS_INIT__) return;
  window.__DEPS_AREAS_INIT__ = true;

  const $area1 = document.getElementById('id_cat_area_1'); // CRH
  const $area2 = document.getElementById('id_cat_area_2'); // CRHTOD
  const $area3 = document.getElementById('id_cat_area');   // Área final
  if (!$area1 || !$area2 || !$area3) return;

  // Flag global opcional desde Blade: <script>window.IS_EDIT = true|false;</script>
  const IS_EDIT = !!window.IS_EDIT;

  // CSRF
  const token = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '';

  // Endpoint
  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  /* ============== Helpers selectpicker / reset ============== */
  const useBS = !!$.fn?.selectpicker;
  const R_PLACE = '<option value="">SELECCIONE</option>';
  const R_LOAD  = '<option value="">Cargando…</option>';

  function pickerRefresh(sel) { useBS && $(sel).selectpicker('refresh'); }
  function pickerDisable(sel, on) { $(sel).prop('disabled', !!on); pickerRefresh(sel); }

  function setPickerEmpty(sel) {
    const $el = $(sel);
    $el.empty().append(R_PLACE).val('');
    pickerRefresh(sel);
  }
  function setPickerLoading(sel) {
    const $el = $(sel);
    $el.empty().append(R_LOAD).val('');
    pickerRefresh(sel);
  }
  function resetArea2() { setPickerEmpty('#id_cat_area_2'); }
  function resetArea3() { setPickerEmpty('#id_cat_area'); }

  /* ========================= Ajax helper ========================= */
  async function postJSON(url, body) {
    const resp = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
  }

  /* === Normalizadores para inyectar A3 si falta opción === */
  function coerceOne(json) {
    const arr =
      (Array.isArray(json) && json) ||
      (Array.isArray(json.value) && json.value) ||
      (Array.isArray(json.selectArea) && json.selectArea) ||
      (Array.isArray(json.options) && json.options) || [];
    const it = arr[0] || null;
    if (it && typeof it === 'object') {
      const id = String(it.id ?? it.value ?? it.key ?? it.id_cat_area ?? it.id_area ?? '');
      const label = String(it.label ?? it.text ?? it.descripcion ?? it.name ?? it.nombre ?? '');
      return { id, label };
    }
    return null;
  }
  async function loadArea3LabelById(area3Id) {
    const tries = [
      { by: 'area_by_id',  id_cat_area: area3Id },
      { by: 'area3_by_id', id_cat_area: area3Id },
      { id: area3Id }
    ];
    for (const body of tries) {
      try {
        const json = await postJSON(COLLECTION_AREA_URL, body);
        const one = coerceOne(json);
        if (one?.label) return one.label;
        const label2 = json.label ?? json.descripcion ?? json.name ?? json.text ?? json.nombre;
        if (label2) return String(label2);
      } catch (_) {}
    }
    return null;
  }
  async function ensureArea3Visible(area3Id) {
    const $sel = $('#id_cat_area');
    const val = String(area3Id);
    if ($sel.find('option[value="' + val + '"]').length === 0) {
      const label = await loadArea3LabelById(val);
      const text  = label || ('[Área ' + val + ']');
      $sel.append($('<option>', { value: val, text }));
    }
    $sel.val(val);
    if (useBS) $sel.selectpicker('refresh');
  }

  /* ======================= Guards anti-race ======================= */
  let reqA2 = 0;
  let reqA3 = 0;

  /* ====================== Carga jerárquica ======================= */
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    reqA2++; const myReq = reqA2;

    resetArea2();
    // En edición, no limpies A3 si ya viene seteado y solo quieres cambiar estatus
    if (!IS_EDIT) resetArea3();
    if (!area1Id) return;

    setPickerLoading('#id_cat_area_2');
    pickerDisable('#id_cat_area_2', true);
    pickerDisable('#id_cat_area', true);

    try {
      const json = await postJSON(COLLECTION_AREA_URL, { by: 'area2_by_area1', id_cat_area_1: area1Id });
      if (myReq !== reqA2) return;

      setPickerEmpty('#id_cat_area_2');
      const list = Array.isArray(json?.value) ? json.value : [];
      if (list.length) {
        const frag = document.createDocumentFragment();
        list.forEach(opt => {
          const op = document.createElement('option');
          op.value = String(opt.id ?? '');
          op.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) op.selected = true;
          frag.appendChild(op);
        });
        $area2.appendChild(frag);
        if (!selectedId) $('#id_cat_area_2').val('');
        pickerRefresh('#id_cat_area_2');
      } else {
        resetArea2();
      }
    } catch (e) {
      console.error('AREA2_LOAD_ERROR:', e);
      resetArea2();
    } finally {
      pickerDisable('#id_cat_area_2', false);
      // A3 queda deshabilitada hasta que haya A2 válido
    }
  }

  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    reqA3++; const myReq = reqA3;

    // En edición, si ya traes A3Inicial y solo mueves estatus, no limpies la selección
    if (!IS_EDIT) resetArea3();
    if (!area2Id) return;

    setPickerLoading('#id_cat_area');
    pickerDisable('#id_cat_area', true);

    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: area2Id,
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (myReq !== reqA3) return;

      setPickerEmpty('#id_cat_area');
      const list = Array.isArray(json?.value) ? json.value : [];
      if (list.length) {
        const frag = document.createDocumentFragment();
        list.forEach(opt => {
          const op = document.createElement('option');
          op.value = String(opt.id ?? '');
          op.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) op.selected = true;
          frag.appendChild(op);
        });
        $area3.appendChild(frag);
        if (!selectedId && !IS_EDIT) $('#id_cat_area').val('');
        pickerRefresh('#id_cat_area');
      } else {
        // En edición, mantener A3 aunque no regrese en el catálogo
        if (!IS_EDIT) resetArea3();
      }
    } catch (e) {
      console.error('AREA3_LOAD_ERROR:', e);
      if (!IS_EDIT) resetArea3();
    } finally {
      pickerDisable('#id_cat_area', false);
    }
  }

  function initials() { return (window.LETTER && window.LETTER.initials) || null; }

  /* ========================= Listeners ÚNICOS ========================= */
  function onA1Change(v) {
    const id = v || '';
    if (!IS_EDIT) resetArea3();
    cargarArea2PorArea1(id, null);
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(id, initials());
  }
  function onA2Change(v) {
    const id = v || '';
    if (!IS_EDIT) resetArea3();
    cargarArea3PorArea2(id, null);
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(id, initials());
  }
  function onA3Change(v) {
    const id = v || '';
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(id, initials());
  }

  // ── A1
  if (useBS) {
    $('#id_cat_area_1').off('changed.bs.select.__deps').on('changed.bs.select.__deps', function () {
      onA1Change($(this).val());
    });
  } else {
    $area1.addEventListener('change', (e) => onA1Change(e.target.value));
  }

  // ── A2
  if (useBS) {
    $('#id_cat_area_2').off('changed.bs.select.__deps').on('changed.bs.select.__deps', function () {
      onA2Change($(this).val());
    });
  } else {
    $area2.addEventListener('change', (e) => onA2Change(e.target.value));
  }

  // ── A3 (solo aquí, NO en select.js)
  if (useBS) {
    $('#id_cat_area').off('changed.bs.select.__deps').on('changed.bs.select.__deps', function () {
      onA3Change($(this).val());
    });
  } else {
    $area3.addEventListener('change', (e) => onA3Change(e.target.value));
  }

  /* ======================= Precarga en edición ======================= */
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  if (area1Inicial) {
    cargarArea2PorArea1(area1Inicial, area2Inicial)
      .then(() => {
        const a2 = $('#id_cat_area_2').val() || area2Inicial;
        if (a2) return cargarArea3PorArea2(a2, area3Inicial);
      })
      .finally(() => {
        const target = area3Inicial || area2Inicial || area1Inicial;
        if (target && window.fillDependentsFromArea) {
          window.fillDependentsFromArea(target, initials());
        }
        window.__DEPS_AREAS_PRIMED__ = true;
      });
  } else {
    // Siempre limpia A2
    resetArea2();

    // Si vienes con SOLO A3 (edición), no lo borres: inyecta y selecciona
    if (!area3Inicial) {
      resetArea3();
      window.__DEPS_AREAS_PRIMED__ = true;
    } else {
      ensureArea3Visible(String(area3Inicial))
        .then(() => {
          if (window.fillDependentsFromArea) {
            window.fillDependentsFromArea(String(area3Inicial), initials());
          }
          // En edición, evita bloquear selects si no hay A1/A2
          if (IS_EDIT) {
            pickerDisable('#id_cat_area_1', false);
            pickerDisable('#id_cat_area_2', false);
            pickerDisable('#id_cat_area', false);
          }
        })
        .finally(() => { window.__DEPS_AREAS_PRIMED__ = true; });
      return;
    }
  }

  /* ===== Salvaguarda: si A3 quedó inválido, crea la opción al vuelo antes de enviar ===== */
  const form = document.getElementById('myForm') || document.getElementById('formulario');
  if (form) {
    form.addEventListener('submit', function () {
      const valA3 = $area3.value;
      if (!valA3) return; // si no hay valor, no hacemos nada

      const $sel = $('#id_cat_area');
      const hasOpt = $sel.find('option[value="' + String(valA3) + '"]').length > 0;

      if (!hasOpt) {
        // crear opción temporal para que el valor viaje en el POST
        $sel.append($('<option>', { value: String(valA3), text: '[Área ' + String(valA3) + ']' }));
        if (useBS) $sel.selectpicker('refresh');
      }
    });
  }
});
