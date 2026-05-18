<form id="formGral" class="busqueda-barra">
  <div class="d-flex align-items-center position-relative">

    <!-- BOTONES (ahora a la izquierda) -->
    <div class="d-flex gap-2">
      <?php foreach ($acciones as $accion): ?>

        <?php if ($accion['id'] === 'nuevo'): ?>
          <button type="button"
            class="btn btn-primary btn-sm btn-toolbar"
            data-bs-toggle="modal"
            data-bs-target="<?= $accion['modal'] ?>">
            <i class="<?= $accion['icon'] ?>"></i>
          </button>

        <?php elseif ($accion['id'] === 'excel'): ?>
          <button type="button"
            class="btn btn-primary btn-sm btn-toolbar js-subir-excel"
            id="btnExcel">
            <i class="<?= $accion['icon'] ?>"></i>
          </button>

          <input type="file"
            id="excelFile"
            accept=".xlsx,.xls"
            style="display:none">

        <?php elseif ($accion['id'] === 'descargar'): ?>
          <button type="button"
            class="btn btn-primary btn-sm btn-toolbar js-activar-excel">
            <i class="<?= $accion['icon'] ?>"></i>
          </button>

        <?php endif; ?>

      <?php endforeach; ?>
    </div>

    <!-- EVENTO CENTRADO -->
    <?php if (!empty($nomevento)): ?>
      <div class="position-absolute start-50 translate-middle-x fw-bold">
        Evento: <?= htmlspecialchars($nomevento) ?>
      </div>
    <?php endif; ?>

  </div>
</form>