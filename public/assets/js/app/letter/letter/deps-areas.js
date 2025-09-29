// deps-areas.js
document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1');
  const $area2 = document.getElementById('id_cat_area_2');
  const $area3 = document.getElementById('id_cat_area');
  if (!$area1 || !$area2 || !$area3) return;

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const url = (window.LETTER && window.LETTER.collectionAreaUrl) || '';

  // Helpers
  function refreshPicker(sel) {
    if (typeof $ !== 'undefined' && typeof $('.selectpicker').selectpicker === 'function') {
      $(sel).selectpicker('refresh');
    }
  }

  function resetSelect($el) {
    $el.innerHTML = '<option value="">SELECCIONE</option>';
    refreshPicker('#' + $el.id);
  }

  async function postJSON(body) {
    const resp = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: JSON.stringify(body)
    });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
  }

  function fillOptions($el, items, selectedId) {
    resetSelect($el); // siempre deja primero "SELECCIONE"
    if (Array.isArray(items)) {
      items.forEach(opt => {
        const o = document.createElement('option');
        o.value = String(opt.id ?? '');
        o.textContent = String(opt.label ?? '');
        if (selectedId && String(selectedId) === String(opt.id)) o.selected = true;
        $el.appendChild(o);
      });
    }
    refreshPicker('#' + $el.id);
  }

  // Cargas
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    // NO se deshabilita, solo se limpia
    resetSelect($area2);
    if (!area1Id) return; // si no hay área1, mantenemos solo "SELECCIONE"
    try {
      const json = await postJSON({ by: 'area2_by_area1', id_cat_area_1: area1Id });
      if (json.ok) fillOptions($area2, json.value, selectedId);
    } catch (e) { console.error('AREA2_LOAD_ERROR:', e); }
  }

  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    resetSelect($area3);
    if (!area2Id) return; // si no hay área2, mantenemos solo "SELECCIONE"
    try {
      const json = await postJSON({ by: 'area3_by_area2', id_cat_area_2: area2Id });
      if (json.ok) fillOptions($area3, json.value, selectedId);
    } catch (e) { console.error('AREA3_LOAD_ERROR:', e); }
  }

  // Al cambiar Área 1 -> refresca Área 2; limpia Área 3
  $area1.addEventListener('change', () => {
    const area1Id = $area1.value || '';
    cargarArea2PorArea1(area1Id, null);
    resetSelect($area3); // hasta que escojan Área 2
  });

  // Al cambiar Área 2 -> refresca Área 3
  $area2.addEventListener('change', () => {
    const area2Id = $area2.value || '';
    cargarArea3PorArea2(area2Id, null);
  });

  // Estado inicial:
  // 1) Siempre deja Área 2 y Área 3 con "SELECCIONE"
  resetSelect($area2);
  resetSelect($area3);

  // 2) Si es edición y vienen iniciales, precargar en cascada
  const initials = (window.LETTER && window.LETTER.initials) || {};
  const area1Inicial = initials.area1 || '';
  const area2Inicial = initials.area2 || '';
  const area3Inicial = initials.area3 || '';

  if (area1Inicial) {
    // Carga Área 2 y selecciona la inicial
    cargarArea2PorArea1(area1Inicial, area2Inicial).then(() => {
      // Si además hay Área 2 inicial, carga Área 3 y selecciónala
      if (area2Inicial) cargarArea3PorArea2(area2Inicial, area3Inicial);
    });
  }
});

