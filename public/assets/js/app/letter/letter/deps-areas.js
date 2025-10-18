document.addEventListener('DOMContentLoaded', function () {
  
  const $area1 = document.getElementById('id_cat_area_1'); // CRH
  const $area2 = document.getElementById('id_cat_area_2'); // CRHTOD
  const $area3 = document.getElementById('id_cat_area');   // Área final

  if (!$area1 || !$area2 || !$area3) return;  // Asegúrate de que los elementos existan

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token = tokenEl ? tokenEl.getAttribute('content') : '';

  const COLLECTION_AREA_URL =
    window.LETTER?.collectionAreaUrl ||
    (typeof URL_DEFAULT !== 'undefined' ? URL_DEFAULT.concat('/letter/collection/collectionArea') : '/letter/collection/collectionArea');

  // ===== Helpers selectpicker =====
  function setPickerEmpty(selector) {
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }

  // <<< AÑADIDO: Definición de la función que faltaba para resetear el área 2 >>>
  function resetArea2() {
    setPickerEmpty('#id_cat_area_2');
  }

  // <<< AÑADIDO: Definición de la función que faltaba para resetear el área 3 >>>
  function resetArea3() {
    setPickerEmpty('#id_cat_area');
  }

  function refreshPicker(id) {
    if ($.fn.selectpicker) {
      $(id).selectpicker('refresh');
    }
  }

  function getVal(el) { return el && el.value ? String(el.value) : ''; }

  // ===== Bloqueo/Desbloqueo Turnar A por Returnado =====
  let __lockedByReturnado = false;

  function lockTurnarAAndSetReturnado(idReturnado) {
    if (__lockedByReturnado) return;
    __lockedByReturnado = true;

    TURNAR_SELECTS.forEach((s) => $(s).prop('disabled', true));
    TURNAR_SELECTS.forEach((s) => refreshPicker(s));
    $('#id_cat_estatus').val(String(idReturnado || 8)).prop('disabled', true).selectpicker('refresh');
  }

  function unlockTurnarAIfLocked() {
    if (!__lockedByReturnado) return;
    __lockedByReturnado = false;

    TURNAR_SELECTS.forEach((s) => $(s).prop('disabled', false));
    TURNAR_SELECTS.forEach((s) => refreshPicker(s));
    $('#id_cat_estatus').prop('disabled', false).selectpicker('refresh');
  }

  // ===== POST JSON a LetterC@collectionArea =====
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

  // ===== Repoblado de dependientes (Usuario/Enlace/Unidad/Coord/Trámite) =====
  function actualizarCamposDerivadosPorAreaId(areaId) {
    const init = window.LETTER.initials || {};

    if (!areaId) {
      setPickerEmpty('#id_usuario_area');
      setPickerEmpty('#id_usuario_enlace');
      setPickerEmpty('#id_cat_unidad');
      setPickerEmpty('#id_cat_coordinacion');
      setPickerEmpty('#id_cat_tramite');
      setPickerEmpty('#id_cat_clave');
      if (typeof clearClaveData === 'function') clearClaveData();
      return;
    }

    $.ajax({
      url: COLLECTION_AREA_URL,
      type: 'POST',
      data: { id: areaId, _token: token },
      success: function (response) {
  
        if (typeof foreachSelectNull === 'function') {
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area');
          foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
          //foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        }
        if (typeof foreachSelect === 'function') {
          //foreachSelect(response.selectTramite, '#id_cat_tramite');
        }

        setTimeout(function () {
          // ====== EDIT: seleccionar guardados si existen ======
          if (init.usuario_enlace)  { $('#id_usuario_enlace').val(String(init.usuario_enlace)); }
          if (init.usuario_area)    { $('#id_usuario_area').val(String(init.usuario_area)); }
          if (init.unidad)          { $('#id_cat_unidad').val(String(init.unidad)); }
          if (init.coordinacion)    { $('#id_cat_coordinacion').val(String(init.coordinacion)); }
          
          // refrescar pickers (usuarios / unid / coord)
          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach(function (s) { $(s).selectpicker('refresh'); });

          // Trámite
          if (init.tramite) {
           $('#id_cat_tramite').val(String(init.tramite)).selectpicker('refresh').trigger('change');
            // esperar a que select.js llene claves
            setTimeout(function () {
              if (init.clave) {
              $('#id_cat_clave').val(String(init.clave)).selectpicker('refresh');
              }
            }, 150);
          }
        }, 0);
      },
      error: function () {
        // En error, dejar todo coherente
        setPickerEmpty('#id_usuario_area');
        setPickerEmpty('#id_usuario_enlace');
        setPickerEmpty('#id_cat_unidad');
        setPickerEmpty('#id_cat_coordinacion');
        setPickerEmpty('#id_cat_tramite');
        setPickerEmpty('#id_cat_clave');
      }
    });
  }

  // ===== Cargar área 2 por área 1 =====
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    resetArea2();
    if (!area1Id) return;
    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area2_by_area1',
        id_cat_area_1: area1Id,
      });
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

  // ===== Cargar área 3 por área 2 =====
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

  // ===== Eventos =====
  $area1.addEventListener('change', function (e) {
    const area1Id = e.target.value || '';
    cargarArea2PorArea1(area1Id, null);
    resetArea3(); 
    actualizarCamposDerivadosPorAreaId(area1Id);
  });

  $area2.addEventListener('change', function (e) {
    const area2Id = e.target.value || '';
    cargarArea3PorArea2(area2Id, null);
    actualizarCamposDerivadosPorAreaId(area2Id);
  });

  $area3.addEventListener('change', function (e) {
    const area3Id = e.target.value || '';
    actualizarCamposDerivadosPorAreaId(area3Id);
  });

  // Precarga en edición
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  if (area1Inicial) {
    cargarArea2PorArea1(area1Inicial, area2Inicial)
      .then(() => {
        const a2 = $area2.value || area2Inicial;
        if (a2) {
          return cargarArea3PorArea2(a2, area3Inicial);
        }
      })
      .finally(() => {
        const targetAreaId = area3Inicial || area2Inicial || area1Inicial;
        if (targetAreaId) {
          actualizarCamposDerivadosPorAreaId(targetAreaId);
        }
      });
  }
});