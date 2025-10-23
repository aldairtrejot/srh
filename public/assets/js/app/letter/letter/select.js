// public/assets/js/app/letter/letter/select.js — FINAL
/* =========================================================
   Llenado de dependientes (Usuario, Enlace, Unidad,
   Coordinación, Trámite y Clave) a partir de un id de área.
   - NO registra listeners de A3 (eso está en deps-areas.js)
   - Encadenamientos: Unidad->Coordinación, Trámite->Clave, Clave->Header
   ========================================================= */

(function () {
  // Evita doble inicialización si el script se carga más de una vez
  if (window.__SELECT_JS_INIT__) return;
  window.__SELECT_JS_INIT__ = true;

  // ===== CSRF seguro (usa global si existe; si no, <meta>) =====
  const __csrf =
    (typeof token !== 'undefined' && token) ||
    (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

  // ===== Helpers de selectpicker y placeholders =====
  function pickerRefresh(selector) {
    if ($.fn.selectpicker) $(selector).selectpicker('refresh');
  }
  function setPickerEmpty(selector) {
    $(selector).html('<option value="">SELECCIONE</option>').val('');
    pickerRefresh(selector);
  }
  function ensurePickerHasPlaceholder(selector) {
    const $sel = $(selector);
    if ($sel.find('option').length === 0) setPickerEmpty(selector);
    else pickerRefresh(selector);
  }
  function setPickerLoading(selector) {
    $(selector).html('<option value="">Cargando…</option>').val('');
    pickerRefresh(selector);
  }

  // ===== Helpers encabezado (UNA sola definición) =====
  function clearClaveData() {
    $('#_labClave').text('_');
    $('#_labClaveCodigo').text('_');
    $('#_labClaveRedaccion').text('_');
  }
  function setClaveInNuSystem(value) {
    const val = (value == null || value === '') ? '-' : String(value);
    let num_turno_sistema = $('#num_turno_sistema').val() || '';
    if (!num_turno_sistema) return;
    // Reemplaza el prefijo antes de la primera barra
    let result = num_turno_sistema.replace(/^[^/]+/, val);
    $('#num_turno_sistema').val(result);
    $('#_labNoCorrespondencia').text(result);
  }

  // ====== ÚNICA función pública: llena dependientes desde un id de área ======
  window.__FILL_INFLIGHT = null; // { areaId, xhr }
  window.fillDependentsFromArea = function (areaId, initials) {
    // Si no hay área, limpia todo
    if (!areaId) {
      setPickerEmpty('#id_usuario_area');
      setPickerEmpty('#id_usuario_enlace');
      setPickerEmpty('#id_cat_tramite');
      setPickerEmpty('#id_cat_unidad');
      setPickerEmpty('#id_cat_coordinacion');
      setPickerEmpty('#id_cat_clave');
      clearClaveData();
      setClaveInNuSystem('-');
      return;
    }

    // Cancela petición previa (anti-race)
    if (window.__FILL_INFLIGHT && window.__FILL_INFLIGHT.xhr) {
      try { window.__FILL_INFLIGHT.xhr.abort(); } catch (_) {}
      window.__FILL_INFLIGHT = null;
    }

    // Estado visual de carga
    setPickerLoading('#id_usuario_enlace');
    setPickerLoading('#id_usuario_area');
    setPickerLoading('#id_cat_unidad');
    setPickerLoading('#id_cat_coordinacion');
    setPickerLoading('#id_cat_tramite');
    setPickerEmpty('#id_cat_clave'); // Clave depende de Trámite

    const xhr = $.ajax({
      url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
      type: 'POST',
      data: { id: areaId, _token: __csrf },
      success: function (response) {
        // Llenado de dependientes
        foreachSelectNull(response.selectEnlace,  '#id_usuario_enlace');
        foreachSelectNull(response.selectUsuario, '#id_usuario_area');
        foreachSelectNull(response.selectUnidad,  '#id_cat_unidad');
        foreachSelectNull(response.selectCoor,    '#id_cat_coordinacion');
        foreachSelect(response.selectTramite,     '#id_cat_tramite');

        // Placeholders/refrescos
        ensurePickerHasPlaceholder('#id_usuario_enlace');
        ensurePickerHasPlaceholder('#id_usuario_area');
        ensurePickerHasPlaceholder('#id_cat_unidad');
        ensurePickerHasPlaceholder('#id_cat_coordinacion');
        ensurePickerHasPlaceholder('#id_cat_tramite');

        // Restaurar valores en edición (si los hay)
        if (initials) {
          if (initials.usuario_enlace)  $('#id_usuario_enlace').val(String(initials.usuario_enlace));
          if (initials.usuario_area)    $('#id_usuario_area').val(String(initials.usuario_area));
          if (initials.unidad)          $('#id_cat_unidad').val(String(initials.unidad));
          if (initials.coordinacion)    $('#id_cat_coordinacion').val(String(initials.coordinacion));
          ['#id_usuario_enlace','#id_usuario_area','#id_cat_unidad','#id_cat_coordinacion']
            .forEach(s => $(s).selectpicker('refresh'));
        }

        // === Seleccionar Trámite ===
        const $tram = $('#id_cat_tramite');
        const tramOptions = $tram.find('option').not('[value=""]');

        if (initials && initials.tramite) {
          $tram.val(String(initials.tramite)).selectpicker('refresh').trigger('change');
          // Dar tiempo a que el handler de Trámite llene Clave y luego fijar la inicial si existe
          setTimeout(function () {
            if (initials.clave) $('#id_cat_clave').val(String(initials.clave))
              .selectpicker('refresh').trigger('change');
          }, 150);
        } else if (tramOptions.length > 0) {
          const firstVal = tramOptions.first().val();
          $tram.val(firstVal).selectpicker('refresh').trigger('change'); // dispara carga de Clave
        } else {
          setPickerEmpty('#id_cat_tramite');
          setPickerEmpty('#id_cat_clave');
          clearClaveData();
          setClaveInNuSystem('-');
        }

        // Prefijo de clave en No. Turno (si backend lo envía)
        setClaveInNuSystem(response.clave);
      },
      complete: function () {
        window.__FILL_INFLIGHT = null;
      }
    });

    window.__FILL_INFLIGHT = { areaId, xhr };
  };

  // ===== Encadenamientos adicionales =====

  // Unidad -> Coordinación
  $('#id_cat_unidad').off('change.__sel').on('change.__sel', function () {
    let idValue = $(this).val();
    if (idValue) {
      $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/collectionUnidad'),
        type: 'POST',
        data: { id: idValue, _token: __csrf },
        success: function (response) {
          foreachSelectNull(response.selectCoordinacion, '#id_cat_coordinacion');
          ensurePickerHasPlaceholder('#id_cat_coordinacion');
        },
      });
    } else {
      setPickerEmpty('#id_cat_coordinacion');
    }
  });

  // Trámite -> Clave
  $('#id_cat_tramite').off('change.__sel').on('change.__sel', function () {
    let idValue = $(this).val();
    if (idValue) {
      $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
        type: 'POST',
        data: { id: idValue, _token: __csrf },
        success: function (response) {
          foreachSelectNull(response.selectClave, '#id_cat_clave');
          ensurePickerHasPlaceholder('#id_cat_clave');
        },
      });
    } else {
      setPickerEmpty('#id_cat_clave');
      clearClaveData();
      setClaveInNuSystem('-');
    }
  });

  // Clave -> Encabezado (labels)
  $('#id_cat_clave').off('change.__sel').on('change.__sel', function () {
    let idValue = $(this).val();
    if (idValue) {
      $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/collectionClave'),
        type: 'POST',
        data: { id: idValue, _token: __csrf },
        success: function (response) {
          let valueClave = response.valueOfClave;
          $('#_labClave').text(valueClave._labClave);
          $('#_labClaveCodigo').text(valueClave._labClaveCodigo);
          $('#_labClaveRedaccion').text(valueClave._labClaveRedaccion);
        },
      });
    } else {
      clearClaveData();
      setClaveInNuSystem('-');
    }
  });

})(); // IIFE


