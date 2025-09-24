// public/assets/js/app/letter/letter/files-toggle.js
(function ($) {
  'use strict';

  // Maestro
  var $boxMaster   = $('#habilitar_carga_box');     // checkbox del componente
  var $hiddenFlag  = $('#habilitar_carga');         // hidden que viaja al backend
  var $container   = $('#contenedor_carga_archivos');
  var $idRegistro  = $('#id_tbl_correspondencia');  // si está vacío => crear

  // Oficio
  var $cbOficio    = $('#oficio_enable_box');
  var $lblOficio   = $('#label_oficio_entrada');
  var $inOficio    = $('#file_oficio_entrada');
  var $boxOficioVacio = $('#container_oficio_entrada_vacio');
  var $boxOficioList  = $('#container_oficio_entrada');

  // Anexos
  var $cbA1 = $('#anexo1_enable_box'), $lblA1 = $('#label_anexo_entrada_1'), $inA1 = $('#file_anexo_entrada_1');
  var $cbA2 = $('#anexo2_enable_box'), $lblA2 = $('#label_anexo_entrada_2'), $inA2 = $('#file_anexo_entrada_2');
  var $cbA3 = $('#anexo3_enable_box'), $lblA3 = $('#label_anexo_entrada_3'), $inA3 = $('#file_anexo_entrada_3');
  var $boxAnexoVacio = $('#container_anexo_entrada_vacio');
  var $boxAnexoList  = $('#container_anexo_entrada');

  function setEnabled($label, $input, enabled) {
    if (!$label.length || !$input.length) return;
    if (enabled) {
      $label.css({ opacity: 1, pointerEvents: 'auto' });
    } else {
      $label.css({ opacity: 0.5, pointerEvents: 'none' });
      $input.val('');
    }
  }

  function renderPreview($listBox, $emptyBox, filesArray) {
    if (!$listBox.length || !$emptyBox.length) return;
    $listBox.empty();
    if (!filesArray || filesArray.length === 0) {
      $emptyBox.show();
      return;
    }
    $emptyBox.hide();
    filesArray.forEach(function (f) {
      var $item = $('<div style="font-size:.9rem; margin:4px 0;"></div>').text('• ' + f.name);
      $listBox.append($item);
    });
  }

  function collectSelectedFiles() {
    var arr = [];
    [$inOficio[0], $inA1[0], $inA2[0], $inA3[0]].forEach(function (el) {
      if (el && el.files && el.files.length) arr.push(el.files[0]);
    });
    return arr;
  }

  function syncMasterUI() {
    var enabled = false;

    // Estado por old() o por el checkbox
    if ($hiddenFlag.val() && String($hiddenFlag.val()).trim() !== '') enabled = true;
    if ($boxMaster.is(':checked')) enabled = true;

    if (enabled) {
      $container.show();
      $hiddenFlag.val('1');
    } else {
      $container.hide();
      $hiddenFlag.val('');
      // Limpieza total si se desactiva
      [$inOficio, $inA1, $inA2, $inA3].forEach(function ($i) { $i.val(''); });
      renderPreview($boxOficioList, $boxOficioVacio, []);
      renderPreview($boxAnexoList, $boxAnexoVacio, []);
      // Deshabilitar botones
      setEnabled($lblOficio, $inOficio, false);
      setEnabled($lblA1, $inA1, false);
      setEnabled($lblA2, $inA2, false);
      setEnabled($lblA3, $inA3, false);
      // Apagar toggles
      [$cbOficio, $cbA1, $cbA2, $cbA3].forEach(function ($c) { $c.prop('checked', false); });
    }
  }

  function bindEnablers() {
    if ($cbOficio.length) {
      $cbOficio.on('change', function () {
        setEnabled($lblOficio, $inOficio, $(this).is(':checked'));
      }).trigger('change');
    }
    if ($cbA1.length) {
      $cbA1.on('change', function () {
        setEnabled($lblA1, $inA1, $(this).is(':checked'));
      }).trigger('change');
    }
    if ($cbA2.length) {
      $cbA2.on('change', function () {
        setEnabled($lblA2, $inA2, $(this).is(':checked'));
      }).trigger('change');
    }
    if ($cbA3.length) {
      $cbA3.on('change', function () {
        setEnabled($lblA3, $inA3, $(this).is(':checked'));
      }).trigger('change');
    }
  }

  function bindLabelClicks() {
    // nada extra: los <label for="..."> abren el input; solo controlamos enable/disable con CSS pointerEvents
  }

  function bindFilePreviews() {
    $inOficio.on('change', function () {
      var files = this.files ? Array.from(this.files) : [];
      renderPreview($boxOficioList, $boxOficioVacio, files);
    });
    [$inA1, $inA2, $inA3].forEach(function ($inp) {
      $inp.on('change', function () {
        var all = [];
        [$inA1[0], $inA2[0], $inA3[0]].forEach(function (el) {
          if (el && el.files && el.files.length) all = all.concat(Array.from(el.files));
        });
        renderPreview($boxAnexoList, $boxAnexoVacio, all);
      });
    });
  }

  function bindMaster() {
    $boxMaster.on('change', function () {
      $hiddenFlag.val($(this).is(':checked') ? '1' : '');
      syncMasterUI();
    });
  }

  function bindSubmitValidation() {
    var $form = $('#myForm');
    $form.on('submit', function (e) {
      var isCreate = !$idRegistro.val();   // crear si no hay id
      var enabled  = !!$hiddenFlag.val();
      if (isCreate && enabled) {
        var anyFile = collectSelectedFiles().length > 0;
        if (!anyFile) {
          e.preventDefault();
          if (window.notyfEM && typeof window.notyfEM.error === 'function') {
            window.notyfEM.error('Adjunta al menos un archivo (Oficio o Anexo).');
          } else {
            alert('Adjunta al menos un archivo (Oficio o Anexo).');
          }
          return false;
        }
      }
    });
  }

  $(document).ready(function () {
    // tooltips si los tienes
    if (typeof window.tooltip === 'function') {
      window.tooltip('#habilitar_carga_archivos', 'Habilita la sección para subir oficios y anexos');
    }

    // Estado inicial
    syncMasterUI();

    // Binds
    bindMaster();
    bindEnablers();
    bindLabelClicks();
    bindFilePreviews();
    bindSubmitValidation();
  });

})(jQuery);
