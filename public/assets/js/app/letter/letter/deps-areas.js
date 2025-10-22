// public/assets/js/app/letter/letter/deps-areas.js
// Maneja SOLO la jerarquía A1 -> A2 -> A3 y dispara fillDependentsFromArea
// en A1 y A2. A3 lo maneja select.js.

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1'); // CRH
  const $area2 = document.getElementById('id_cat_area_2'); // CRHTOD
  const $area3 = document.getElementById('id_cat_area');   // Área final

  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token = tokenEl ? tokenEl.getAttribute('content') : '';

  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  // ----- Helpers selectpicker -----
  function setPickerEmpty(selector) {
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }
  function refreshPicker(id) { if ($.fn.selectpicker) $(id).selectpicker('refresh'); }
  function resetArea2() { setPickerEmpty('#id_cat_area_2'); }
  function resetArea3() { setPickerEmpty('#id_cat_area'); }

  // ----- Ajax helper -----
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

  // ----- Carga jerárquica -----
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    resetArea2();
    if (!area1Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, { by: 'area2_by_area1', id_cat_area_1: area1Id });
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area2.appendChild(option);
        });
        refreshPicker('#id_cat_area_2');
      } else {
        resetArea2();
      }
    } catch (e) {
      console.error('AREA2_LOAD_ERROR:', e);
      resetArea2();
    }
  }

  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    resetArea3();
    if (!area2Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: area2Id,
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (json.ok && Array.isArray(json.value) && json.value.length) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area3.appendChild(option);
        });
        refreshPicker('#id_cat_area');
      } else {
        resetArea3();
      }
    } catch (e) {
      console.error('AREA3_LOAD_ERROR:', e);
      resetArea3();
    }
  }

  function initials() { return (window.LETTER && window.LETTER.initials) || null; }

  // ----- Eventos -----
  function onA1Change(val) {
    const area1Id = val || '';
    cargarArea2PorArea1(area1Id, null);
    resetArea3();
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(area1Id, initials());
  }
  function onA2Change(val) {
    const area2Id = val || '';
    cargarArea3PorArea2(val, null);
    if (window.fillDependentsFromArea) window.fillDependentsFromArea(area2Id, initials());
  }

  // DOM nativo y bootstrap-select
  $area1.addEventListener('change', (e) => onA1Change(e.target.value));
  $('#id_cat_area_1').on('changed.bs.select', function(){ onA1Change($(this).val()); });

  $area2.addEventListener('change', (e) => onA2Change(e.target.value));
  $('#id_cat_area_2').on('changed.bs.select', function(){ onA2Change($(this).val()); });

  // A3 no lo disparamos aquí para no duplicar (lo maneja select.js)

  // ----- Precarga en edición -----
  const area1Inicial = (window.LETTER && window.LETTER.initials && window.LETTER.initials.area1) || null;
  const area2Inicial = (window.LETTER && window.LETTER.initials && window.LETTER.initials.area2) || null;
  const area3Inicial = (window.LETTER && window.LETTER.initials && window.LETTER.initials.area3) || null;

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
      });
  }
});
