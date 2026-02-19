<?php
  $modulo = $modulo ?? '';
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

        <?php if ($modulo): ?>
          <span class="navbar-text fw-bold text-primary-emphasis me-4">
            <?= htmlspecialchars($modulo) ?>
          </span> 
        <?php endif; ?>

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="registros" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">Registros
            </a>
            <ul class="dropdown-menu" aria-labelledby="ABM">
              <li><a href="<?= BASE_URL ?>/middleware/logout.php" class="dropdown-item">Salir</a></li> 
            </ul>
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
        <a href="<?= BASE_URL ?>/views/menu.php" class="btn btn-danger">
          Salir
        </a>
<!--        <button type="button" class="btn btn-danger" action="../middleware/logout.php">
          Salir
        </button> -->
      </div>
    </div>
  </div>
</nav>