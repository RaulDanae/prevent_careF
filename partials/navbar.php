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
            <a href="#" class="nav-link dropdown-toggle" id="registros" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">Registros
            </a>

            <?php if ($tipo_menu === 'general'): ?>
              <ul class="dropdown-menu" aria-labelledby="ABM">
                <?php if ($perfil === 'Adminis' || $perfil == 'Supervi'): ?>
                  <li><a href="<?= BASE_URL ?>/views/menuA.php" class="dropdown-item">Administracion</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Laboratorio' || $perfil === 'Caplab'): ?>
                  <li><a href="<?= BASE_URL ?>/views/menuL.php" class="dropdown-item">Laboratorio</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Avisual' || 
                          $perfil === 'Snutric' || $perfil === 'Afisica' || $perfil === 'Ccorpor' ||
                          $perfil === 'Tmuestr' || $perfil === 'Svitale' || $perfil === 'Pulmvit' || 
                          $perfil === 'Comodin' || $perfil === 'Cauditiva' || $perfil === 'Relajacion'): ?>
                  <li><a href="<?= BASE_URL ?>/views/menuF.php" class="dropdown-item">Feria</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/middleware/logout.php" class="dropdown-item">Salir</a></li> 
              </ul>
            <?php endif; ?>

            <?php if ($tipo_menu === 'admin'): ?>
              <ul class="dropdown-menu" aria-labelledby="ABM">
                <li><a href="<?= BASE_URL ?>/views/fALBA.php" class="dropdown-item">Staff</a></li>
                <li><a href="<?= BASE_URL ?>/views/fEMP.php" class="dropdown-item">Empresas</a></li>
                <li><a href="<?= BASE_URL ?>/views/fEVE.php" class="dropdown-item">Eventos</a></li>
                <li><a href="<?= BASE_URL ?>/views/fREG.php" class="dropdown-item">Pacientes</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menu_gral.php" class="dropdown-item">Salir</a></li> 
              </ul>
            <?php endif; ?>

            <?php if ($tipo_menu === 'laboratorio'): ?>
              <ul class="dropdown-menu" aria-labelledby="ABM">
                <li><a href="<?= BASE_URL ?>/views/fEST.php" class="dropdown-item">Estudios</a></li>
                <li><a href="<?= BASE_URL ?>/views/fREC.php" class="dropdown-item">Recepcion</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menu_gral.php" class="dropdown-item">Salir</a></li> 
              </ul>
            <?php endif; ?>

            <?php if ($tipo_menu === 'feria'): ?>
              <ul class="dropdown-menu" aria-labelledby="ABM">
                <?php if ($perfil === 'Adminis' || $perfil == 'Supervi'): ?>
                  <li><a href="<?= BASE_URL ?>/views/fREG.php" class="dropdown-item">Agendar</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Svitale' || $perfil === 'Comodin'): ?>
                  <li><a href="<?= BASE_URL ?>/views/fTSV.php" class="dropdown-item">Toma Signos Vitales</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Tmuestr' || $perfil === 'Comodin'): ?>                  
                  <li><a href="<?= BASE_URL ?>/views/fTDM.php" class="dropdown-item">Toma de Muestras</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Ccorpor'): ?>                
                  <li><a href="<?= BASE_URL ?>/views/fCCO.php" class="dropdown-item">Composicion Corporal</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Snutric'): ?>    
                  <li><a href="<?= BASE_URL ?>/views/fSNU.php" class="dropdown-item">Salud Nutricional</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Comodin' || $perfil === 'Cauditiva'): ?> 
                  <li><a href="<?= BASE_URL ?>/views/fCAU.php" class="dropdown-item">Capacidad Auditiva</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Pulmvit' || $perfil === 'Comodin'): ?>                
                  <li><a href="<?= BASE_URL ?>/views/fCPU.php" class="dropdown-item">Capacidad Pulmonar</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Avisual' || $perfil === 'Comodin'): ?>                
                  <li><a href="<?= BASE_URL ?>/views/fAVI.php" class="dropdown-item">Agudeza Visual</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Afisica'): ?>
                  <li><a href="<?= BASE_URL ?>/views/fAFI.php" class="dropdown-item">Activacion Fisica</a></li>
                <?php endif; ?>
                <?php if ($perfil === 'Adminis' || $perfil === 'Supervi' || $perfil === 'Comodin' || $perfil === 'Relajacion'): ?>                
                  <li><a href="<?= BASE_URL ?>/views/fREL.php" class="dropdown-item">Relajacion</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a href="<?= BASE_URL ?>/views/menu_gral.php" class="dropdown-item">Salir</a></li> 
              </ul>
            <?php endif; ?>
          </li>
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
        <?php if ($tipo_menu === 'admin'): ?>
          <a href="<?= BASE_URL ?>/views/menu_gral.php" class="btn btn-danger">
            Salir
          </a>
        <?php elseif ($tipo_menu === 'laboratorio'): ?>
          <a href="<?= BASE_URL ?>/views/menu_gral.php" class="btn btn-danger">
            Salir
          </a>
        <?php elseif ($tipo_menu === 'feria'): ?>
          <a href="<?= BASE_URL ?>/views/menu_gral.php" class="btn btn-danger">
            Salir
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/middleware/logout.php" class="btn btn-danger">
            Salir
          </a>
        <?php endif; ?>  
      </div>
    </div>
  </div>
</nav>