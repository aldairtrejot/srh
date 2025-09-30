// Dependencias: Área 2 (por Área 1) y Área 3 (por Área 2)
// Además: cuando cambian Área 1 o Área 2, actualizamos
// Usuario, Enlace, Unidad, Coordinación y Trámite usando tu endpoint
// /letter/collection/collectionArea (CollectionAreaC@collection),
// y auto-seleccionamos el PRIMER Trámite (disparamos change para que
// select.js cargue Claves). Si no hay trámites, queda "SELECCIONE".
// IMPORTANTe: aquí ya NO llamamos /letter/collection/area con
// by: 'tramite_by_area1'/'tramite_by_area2' (eso causaba 422).

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1');
  const $area2 = document.getElementById('id_cat_area_2');
  const $area3 = document.getElementById('id_cat_area');
  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token = tokenEl ? tokenEl.getAttribute('content') : '';

  // ===== Helpers selectpicker =====
  function setPickerEmpty(selector) {
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }
  function refreshPicker(id) {
    if (typeof $ !== 'undefined' && $.fn.selectpicker) {
      $(id).selectpicker('refresh');
    }
  }

  // ===== POST JSON a LetterC@collectionArea (solo para cadenas de Áreas) =====
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

  // ===== helpers limpiar selects encadenados =====
  function resetArea2() { setPickerEmpty('#id_cat_area_2'); }
  function resetArea3() { setPickerEmpty('#id_cat_area'); }

  // ===== Repoblado de dependientes (Usuario/Enlace/Unidad/Coord/Trámite) =====
  // Usa tu endpoint existente: /letter/collection/collectionArea (CollectionAreaC@collection)
  function actualizarCamposDerivadosPorAreaId(areaId) {
    // Si no hay área -> todo a "SELECCIONE" y limpiar claves
    if (!areaId) {
      setPickerEmpty('#id_usuario_area');
      setPickerEmpty('#id_usuario_enlace');
      setPickerEmpty('#id_cat_unidad');
      setPickerEmpty('#id_cat_coordinacion');
      setPickerEmpty('#id_cat_tramite');
      setPickerEmpty('#id_cat_clave');
      if (typeof clearClaveData === 'function') clearClaveData();
      if (typeof setClaveInNuSystem === 'function') setClaveInNuSystem('-');
      return;
    }

    $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
      type: 'POST',
      data: { id: areaId, _token: token },
      success: function (response) {
        // Estas funciones vienen en tu proyecto
        if (typeof foreachSelectNull === 'function') {
          foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
          foreachSelectNull(response.selectUsuario, '#id_usuario_area');
          foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
          foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        }
        if (typeof foreachSelect === 'function') {
          // Poblar Trámite con tus helpers
          foreachSelect(response.selectTramite, '#id_cat_tramite');
        }

        // Refrescar y asegurar "SELECCIONE" si quedaron vacíos
        setTimeout(function () {
          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach((selector) => {
              const $sel = $(selector);
              if ($sel.find('option').length === 0) setPickerEmpty(selector);
              else $sel.selectpicker('refresh');
            });

          // === AUTOS ELECCIÓN PRIMER TRÁMITE + disparo change ===
          const $tram = $('#id_cat_tramite');
          const tramOptions = $tram.find('option').not('[value=""]');
          if (tramOptions.length > 0) {
            // Seleccionar el primero y disparar change para que select.js cargue Claves
            const firstVal = tramOptions.first().val();
            $tram.val(firstVal).selectpicker('refresh').trigger('change');
          } else {
            // Sin trámites -> Trámite y Clave a "SELECCIONE"
            setPickerEmpty('#id_cat_tramite');
            setPickerEmpty('#id_cat_clave');
            if (typeof clearClaveData === 'function') clearClaveData();
          }
        }, 0);

        // Encabezado/num_turno_sistema (si lo regresa tu backend)
        if (typeof setClaveInNuSystem === 'function') {
          setClaveInNuSystem(response.clave || '-');
        }
        if (typeof clearClaveData === 'function') clearClaveData();
      },
      error: function () {
        // En error, dejar todo coherente
        setPickerEmpty('#id_usuario_area');
        setPickerEmpty('#id_usuario_enlace');
        setPickerEmpty('#id_cat_unidad');
        setPickerEmpty('#id_cat_coordinacion');
        setPickerEmpty('#id_cat_tramite');
        setPickerEmpty('#id_cat_clave');
        if (typeof clearClaveData === 'function') clearClaveData();
      }
    });
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
    // Encadenado de áreas
    cargarArea2PorArea1(area1Id, null);
    resetArea3(); // al cambiar Área 1, limpia Área 3
    // Dependientes + Trámite (autoselección primer ítem)
    actualizarCamposDerivadosPorAreaId(area1Id);
  });

  $area2.addEventListener('change', function (e) {
    const area2Id = e.target.value || '';
    // Encadenado de áreas
    cargarArea3PorArea2(area2Id, null);
    // Dependientes + Trámite (autoselección primer ítem)
    actualizarCamposDerivadosPorAreaId(area2Id);
  });

  // Nota: cuando cambias Área 3, tu select.js ya actualiza
  // Usuario/Enlace/Unidad/Coordinación/Trámite/Clave correctamente.

  // ===== Precarga en edición =====
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  if (area1Inicial) {
    // Cargar cadena Área2/Área3
    cargarArea2PorArea1(area1Inicial, area2Inicial).then(() => {
      const a2 = $area2.value || area2Inicial;
      if (a2) {
        cargarArea3PorArea2(a2, area3Inicial);
      }
    });
    // Precargar dependientes + Trámite (auto 1º) con base en Área 1
    actualizarCamposDerivadosPorAreaId(area1Inicial);
  } else {
    // create -> vacíos
    resetArea2();
    resetArea3();
    setPickerEmpty('#id_usuario_area');
    setPickerEmpty('#id_usuario_enlace');
    setPickerEmpty('#id_cat_unidad');
    setPickerEmpty('#id_cat_coordinacion');
    setPickerEmpty('#id_cat_tramite');
    setPickerEmpty('#id_cat_clave');
  }
});







