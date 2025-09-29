// Dependencias: Área 2 (por Área 1) y Área 3 (por Área 2)
// Reglas:
// - Área 2 y Área 3 arrancan mostrando solo "SELECCIONE"
// - Área 3 en CREATE filtra a3.estatus = true; en EDIT permite activos e inactivos

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1');
  const $area2 = document.getElementById('id_cat_area_2');
  const $area3 = document.getElementById('id_cat_area');
  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token = tokenEl ? tokenEl.getAttribute('content') : '';

  function refreshPicker(id) {
    if (typeof $ !== 'undefined' && typeof $('.selectpicker').selectpicker === 'function') {
      $(id).selectpicker('refresh');
    }
  }

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

  // ===== helpers para limpiar selects =====
  function resetArea2() {
    $area2.innerHTML = '<option value="">SELECCIONE</option>';
    refreshPicker('#id_cat_area_2');
  }
  function resetArea3() {
    $area3.innerHTML = '<option value="">SELECCIONE</option>';
    refreshPicker('#id_cat_area');
  }

  // ===== Área 2 por Área 1 =====
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    resetArea2();
    if (!area1Id) return;

    try {
      const json = await postJSON((window.LETTER || {}).collectionAreaUrl, {
        by: 'area2_by_area1',
        id_cat_area_1: area1Id,
      });
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area2.appendChild(option);
        });
      }
    } catch (e) {
      console.error('AREA2_LOAD_ERROR:', e);
    } finally {
      refreshPicker('#id_cat_area_2');
    }
  }

  // ===== Área 3 por Área 2 (tu regla SQL) =====
  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    resetArea3();
    if (!area2Id) return;

    try {
      const json = await postJSON((window.LETTER || {}).collectionAreaUrl, {
        by: 'area3_by_area2',
        id_cat_area_2: area2Id,
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });
      if (json.ok && Array.isArray(json.value)) {
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          $area3.appendChild(option);
        });
      }
    } catch (e) {
      console.error('AREA3_LOAD_ERROR:', e);
    } finally {
      refreshPicker('#id_cat_area');
    }
  }

  // Eventos
  $area1.addEventListener('change', function (e) {
    const area1Id = e.target.value || '';
    cargarArea2PorArea1(area1Id, null);
    resetArea3(); // al cambiar área1, limpia área3
  });

  $area2.addEventListener('change', function (e) {
    const area2Id = e.target.value || '';
    cargarArea3PorArea2(area2Id, null);
  });

  // Precarga en edición
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  if (area1Inicial) {
    cargarArea2PorArea1(area1Inicial, area2Inicial).then(() => {
      const a2 = $area2.value || area2Inicial;
      if (a2) {
        cargarArea3PorArea2(a2, area3Inicial);
      }
    });
  } else {
    // nuevo (create) -> ambos vacíos
    resetArea2();
    resetArea3();
  }
});


