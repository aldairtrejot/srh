// public/assets/js/app/letter/letter/deps-areas.js — FINAL ÚNICO
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

  // CSRF
  const token = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '';

  // Endpoint
  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  /* ============== Helpers selectpicker / reset ============== */
  const useBS = !!$.fn.selectpicker;
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
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: JSON.stringify(body),
    });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
  }

  /* ======================= Guards anti-race ======================= */
  let reqA2 = 0;
  let reqA3 = 0;

  /* ====================== Carga jerárquica ======================= */
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    reqA2++; const myReq = reqA2;

    resetArea2();
    resetArea3(); // Al cambiar A1, A3 queda inválida
    if (!area1Id) return;

    setPickerLoading('#id_cat_area_2');
    pickerDisable('#id_cat_area_2', true);
    pickerDisable('#id_cat_area', true);

    try {
      const json = await postJSON(COLLECTION_AREA_URL, { by: 'area2_by_area1', id_cat_area_1: area1Id });
      if (myReq !== reqA2) return; // respuesta vieja

      setPickerEmpty('#id_cat_area_2');
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        const frag = document.createDocumentFragment();
        json.value.forEach(opt => {
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

    resetArea3();
    if (!area2Id) return;

    setPickerLoading('#id_cat_area');
    pickerDisable('#id_cat_area', true);

    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: area2Id,
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (myReq !== reqA3) return; // respuesta vieja

      setPickerEmpty('#id_cat_area');
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        const frag = document.createDocumentFragment();
        json.value.forEach(opt => {
          const op = document.createElement('option');
          op.value = String(opt.id ?? '');
          op.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) op.selected = true;
          frag.appendChild(op);
        });
        $area3.appendChild(frag);
        if (!selectedId) $('#id_cat_area').val(''); // evita auto-selección
        pickerRefresh('#id_cat_area');
      } else {
        resetArea3();
      }
    } catch (e) {
      console.error('AREA3_LOAD_ERROR:', e);
      resetArea3();
    } finally {
      pickerDisable('#id_cat_area', false);
    }
  }

  function initials() { return (window.LETTER && window.LETTER.initials) || null; }

  /* ========================= Listeners ÚNICOS ========================= */
  function onA1Change(v) {
    const id = v || '';
    resetArea3();
    cargarArea2PorArea1(id, null);
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(id, initials());
  }
  function onA2Change(v) {
    const id = v || '';
    resetArea3();
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
        window.__DEPS_AREAS_PRIMED__ = true; // marca que ya primereamos
      });
  } else {
    resetArea2();
    resetArea3();
    window.__DEPS_AREAS_PRIMED__ = true;
  }

  /* ===== Salvaguarda: si A3 quedó inválido, no enviar valor “stale” ===== */
  const form = document.getElementById('myForm') || document.getElementById('formulario');
  if (form) {
    form.addEventListener('submit', function () {
      const valA3 = $area3.value;
      const hasOpt = !!($('#id_cat_area option').toArray().find(o => o.value === valA3));
      if (!valA3 || !hasOpt) {
        $area3.value = '';
        $('#id_cat_area').val('').trigger('change');
      }
    });
  }
});

