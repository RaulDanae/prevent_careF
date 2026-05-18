<?php

  $perfil = $_SESSION['perfil'];
  $tipo_menu = $tipo_menu ?? 'general'; // por defecto

?>

<nav class="navbar navbar-expand-lg bg-primary-subtle">
  <div class = "container-fluid d-flex justify-content-between align-items-center">

    <div class = "d-flex align-items-center">
      <a href="#" class = "navbar-brand me-4">
        <img src="<?= BASE_URL ?>/assets/img/preventcare_icon3.png" class = "logo_marco" alt="logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a href="#" class = "nav-link dropdown-toggle" id="registros" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">Registros
            </a>

          <?php if($tipo_menu === 'alba'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <?php if ($perfil === 'Adminis' || $perfil == 'Supervi'): ?>
                  <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <?php endif; ?>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menu_gral.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'empresa'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuA.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'evento'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuA.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'registro'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-subir-excel">Importar<i class="fa fa-file-import"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuA.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'laboratorio'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuL.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'recepcion'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuL.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'signos_vitales'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'toma_muestras'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'composicion_corporal'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'capacidad_pulmonar'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'salud_nutricional'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'relajacion'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'capacidad_auditiva'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'agudeza_visual'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'activacion_fisica'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'filtro'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <?php if($tipo_menu === 'agenda'): ?>
              <ul class="dropdown-menu" aria-labelledby="registros">
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nuevo<i class="fa-solid fa-circle-plus"></i></a></li>
                <li><a href="#" class="dropdown-item d-flex justify-content-between align-items-center js-activar-excel">Descargar<i class="fa fa-download"></i></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menu_gral.php" class="dropdown-item d-flex justify-content-between align-items-center">Salir<i class="fa-solid fa-door-open"></i></a></li>
              </ul>
          <?php endif; ?>

          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="sobre" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">About
            </a>
            <ul class="dropdown-menu" aria-labelledby="AC">
              <li><a href="#" class="dropdown-item" id="btnAcercaDe">Acerca de</a></li>
            </ul>
          </li>
        </ul>

      </div>
    </div>

    <div class="d-flex align-items-center flex-nowrap">
      <div class="container-fluid text-nowrap me-3">
          Bienvenid@ <?= $_SESSION['nombre'] ?>
      </div>
      <div>
          <?php if ($tipo_menu === 'alba'): ?>
            <a href="<?= BASE_URL ?>/views/menu_gral.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'empresa'): ?>
            <a href="<?= BASE_URL ?>/views/menuA.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'evento'): ?>
            <a href="<?= BASE_URL ?>/views/menuA.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'registro'): ?>
            <a href="<?= BASE_URL ?>/views/menuA.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'laboratorio'): ?>
            <a href="<?= BASE_URL ?>/views/menuL.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'recepcion'): ?>
            <a href="<?= BASE_URL ?>/views/menuL.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'signos_vitales'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'toma_muestras'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'composicion_corporal'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'capacidad_pulmonar'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'salud_nutricional'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'relajacion'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'capacidad_auditiva'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'agudeza_visual'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'activacion_fisica'): ?>
            <a href="<?= BASE_URL ?>/views/menuF.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'filtro'): ?>
            <a href="<?= BASE_URL ?>/views/menuA.php" class="btn btn-danger">
              Salir
            </a>
          <?php elseif ($tipo_menu === 'agenda'): ?>
            <a href="<?= BASE_URL ?>/views/menu_gral.php" class="btn btn-danger">
              Salir
            </a>
          <?php endif; ?> 
      </div>
    </div>
  </div>
</nav>