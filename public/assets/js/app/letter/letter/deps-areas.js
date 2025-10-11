// assets/js/app/letter/letter/deps-areas.js
// -------------------------------------------------------------
// Flujo:
// - Cambiar CRH (A1)  => carga opciones de CRHTOD (A2), limpia A3 y dependientes, y AUTOLLENA dependientes con A1
// - Cambiar CRHTOD(A2)=> carga opciones de Área (A3), limpia dependientes, y AUTOLLENA dependientes con A2
// - Cambiar Área (A3) => AUTOLLENA dependientes con A3
// NOTA: el bloqueo "RETORNADO" lo maneja la vista Blade en el script inline.
// -------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
  const $area1 = document.getElementById('id_cat_area_1');
  const $area2 = document.getElementById('id_cat_area_2');
  const $area3 = document.getElementById('id_cat_area');
  if (!$area1 || !$area2 || !$area3) return;

  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const token   = tokenEl ? tokenEl.getAttribute('content') : '';

  const COLLECTION_AREA_URL =
    (window.LETTER && window.LETTER.collectionAreaUrl) ||
    (typeof URL_DEFAULT !== 'undefined'
      ? URL_DEFAULT.concat('/letter/collection/collectionArea')
      : '/letter/collection/collectionArea');

  /* ===================== helpers selectpicker ===================== */
  function setPickerEmpty(selector) {
    if (typeof $ === 'undefined') return;
    $(selector).html('<option value="">SELECCIONE</option>').selectpicker('refresh');
  }
  function refreshPicker(selector) {
    if (typeof $ !== 'undefined' && $.fn.selectpicker) $(selector).selectpicker('refresh');
  }
  function getVal(el) { return (el && el.value) ? String(el.value) : ''; }

  function clearDependientes() {
    setPickerEmpty('#id_usuario_area');
    setPickerEmpty('#id_usuario_enlace');
    setPickerEmpty('#id_cat_unidad');
    setPickerEmpty('#id_cat_coordinacion');
    setPickerEmpty('#id_cat_tramite');
    setPickerEmpty('#id_cat_clave');
    if (typeof clearClaveData === 'function') clearClaveData();
    if (typeof setClaveInNuSystem === 'function') setClaveInNuSystem('-');
  }

  /* ====== fallback para poblar selects si tus helpers no están ====== */
  function fillSelectFallback(selector, list) {
    try {
      const $sel = $(selector);
      let html = '<option value="">SELECCIONE</option>';
      (Array.isArray(list) ? list : []).forEach((it) => {
        const id  = String(it.id ?? it.value ?? '');
        const txt = String(
          it.label ?? it.descripcion ?? it.name ?? it.usuario ?? it.text ?? it.nombre ?? ''
        );
        if (!id || !txt) return;
        html += `<option value="${id}">${txt}</option>`;
      });
      $sel.html(html);
      refreshPicker(selector);
    } catch (_) {}
  }

  /* ===================== POST JSON ===================== */
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

  /* ===================== cadenas de áreas (A2 por A1 / A3 por A2) ===================== */
  async function cargarArea2PorArea1(area1Id, selectedId = null) {
    setPickerEmpty('#id_cat_area_2');
    setPickerEmpty('#id_cat_area');
    if (!area1Id) return;

    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area2_by_area1',
        id_cat_area_1: Number(area1Id),
      });

      if (json.ok && Array.isArray(json.value)) {
        const frag = document.createDocumentFragment();
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          frag.appendChild(option);
        });
        $area2.appendChild(frag);
      }
      refreshPicker('#id_cat_area_2');
    } catch (_) {
      setPickerEmpty('#id_cat_area_2');
      setPickerEmpty('#id_cat_area');
    }
  }

  async function cargarArea3PorArea2(area2Id, selectedId = null) {
    setPickerEmpty('#id_cat_area');
    if (!area2Id) return;

    try {
      const json = await postJSON(COLLECTION_AREA_URL, {
        by: 'area3_by_area2',
        id_cat_area_2: Number(area2Id),
        include_inactive: !!(window.LETTER && window.LETTER.includeInactiveArea3),
      });

      if (json.ok && Array.isArray(json.value)) {
        const frag = document.createDocumentFragment();
        json.value.forEach((opt) => {
          const option = document.createElement('option');
          option.value = String(opt.id ?? '');
          option.textContent = String(opt.label ?? '');
          if (selectedId && String(selectedId) === String(opt.id)) option.selected = true;
          frag.appendChild(option);
        });
        $area3.appendChild(frag);
      }
      refreshPicker('#id_cat_area');
    } catch (_) {
      setPickerEmpty('#id_cat_area');
    }
  }

  /* ===================== Dependientes (con A1, A2 o A3) ===================== */
  function actualizarCamposDerivadosPorAreaId(areaId) {
    if (!areaId) { clearDependientes(); return; }

    $.ajax({
      url: COLLECTION_AREA_URL,
      type: 'POST',
      data: { id: areaId, _token: token }, // LEGACY compatible
      success: function (response) {
        // 1) Intentar con helpers existentes
        let usOK=false,enOK=false,unOK=false,coOK=false,trOK=false;
        if (typeof foreachSelectNull === 'function') {
          usOK = !!foreachSelectNull(response.selectUsuario, '#id_usuario_area');
          enOK = !!foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
          unOK = !!foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
          coOK = !!foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        }
        if (typeof foreachSelect === 'function') {
          trOK = !!foreachSelect(response.selectTramite, '#id_cat_tramite');
        }

        // 2) Fallbacks si no hay helpers o dejaron vacío
        if (!usOK) fillSelectFallback('#id_usuario_area',  response.selectUsuario);
        if (!enOK) fillSelectFallback('#id_usuario_enlace',response.selectEnlace);
        if (!unOK) fillSelectFallback('#id_cat_unidad',    response.selectUnidad);
        if (!coOK) fillSelectFallback('#id_cat_coordinacion',response.selectCoor);
        if (!trOK) fillSelectFallback('#id_cat_tramite',   response.selectTramite);

        // 3) Auto-seleccionar el PRIMER trámite y disparar change para cargar CLAVES
        setTimeout(function () {
          const $tram = $('#id_cat_tramite');
          const $opts = $tram.find('option').not('[value=""]');
          if ($opts.length > 0) {
            const firstVal = $opts.first().val();
            $tram.val(firstVal).selectpicker('refresh').trigger('change'); // select.js llenará Claves
          } else {
            setPickerEmpty('#id_cat_clave');
            if (typeof clearClaveData === 'function') clearClaveData();
          }

          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach((selector) => refreshPicker(selector));
        }, 0);

        if (typeof setClaveInNuSystem === 'function') {
          const clave = (response && typeof response.clave !== 'undefined') ? response.clave : '-';
          setClaveInNuSystem(clave || '-');
        }
      },
      error: function () {
        clearDependientes();
      }
    });
  }

  /* ===================== eventos ===================== */
  $area1.addEventListener('change', async function (e) {
    const area1Id = e.target.value || '';
    // Limpiar niveles hacia abajo + dependientes
    setPickerEmpty('#id_cat_area_2');
    setPickerEmpty('#id_cat_area');
    clearDependientes();

    // Cargar A2 y autollenar dependientes con A1
    await cargarArea2PorArea1(area1Id, null);
    if (area1Id) actualizarCamposDerivadosPorAreaId(area1Id);
  });

  $area2.addEventListener('change', async function (e) {
    const area2Id = e.target.value || '';
    // Limpiar niveles hacia abajo + dependientes
    setPickerEmpty('#id_cat_area');
    clearDependientes();

    // Cargar A3 y autollenar dependientes con A2
    await cargarArea3PorArea2(area2Id, null);
    if (area2Id) actualizarCamposDerivadosPorAreaId(area2Id);
  });

  $area3.addEventListener('change', function (e) {
    const area3Id = e.target.value || '';
    clearDependientes();
    if (area3Id) actualizarCamposDerivadosPorAreaId(area3Id);
  });

  /* ===================== precarga (edición) ===================== */
  const area1Inicial = window.LETTER?.initials?.area1 || null;
  const area2Inicial = window.LETTER?.initials?.area2 || null;
  const area3Inicial = window.LETTER?.initials?.area3 || null;

  (async function precarga() {
    if (area1Inicial) {
      await cargarArea2PorArea1(area1Inicial, area2Inicial);
      if (area2Inicial) {
        await cargarArea3PorArea2(area2Inicial, area3Inicial);
        if (area3Inicial) {
          actualizarCamposDerivadosPorAreaId(area3Inicial);
        } else {
          actualizarCamposDerivadosPorAreaId(area2Inicial); // al menos poblar con A2
        }
      } else {
        actualizarCamposDerivadosPorAreaId(area1Inicial);   // al menos poblar con A1
      }
    } else {
      setPickerEmpty('#id_cat_area_2');
      setPickerEmpty('#id_cat_area');
      clearDependientes();
    }
  })();
});
