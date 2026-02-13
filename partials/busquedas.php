<?php

  $perfil  = $_SESSION['perfil'] ?? null;
  $modulo = $modulo ?? null;

?>


<!-- Menu Busquedas -->
<form id="formGral" class="busqueda-barra">
  <div class="busqueda-flex">

    <!-- Nuevo -->
    <!-- Nuevo (solo admin) -->
    <?php if (in_array($perfil, ['Adminis', 'Supervi']) && $modulo === 'Registro'): ?>
     <button
      type="button"
      id="btnNuevoM"
      class="btn btn-primary btn-sm btn-toolbar"
      data-bs-toggle="modal"
      data-bs-target="#modalNuevo"
      title="Nuevo">
      <i class="fa fa-plus"></i>
     </button>

    <!-- Carga de archivos -->
     <button
      type="button"
      id="btnExcel"
      class="btn btn-primary btn-sm btn-toolbar"
      title="Carga">
      <i class="fa fa-file-import"></i>
     </button>      

     <input type="file"
       id="excelFile"
       accept=".xlsx,.xls"
       style="display:none">

    <?php endif; ?>

    <!-- Descargar -->
     <button
      type="button"
      class="btn btn-primary btn-sm btn-toolbar js-activar-excel"
      id="btndescargar"
      title="Descargar">
      <i class="fa fa-download"></i>
    </button>

  </div>
</form>
<!-- /Menu Busquedas -->