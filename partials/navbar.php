<nav class="navbar navbar-expand-lg bg-primary-subtle">
  <div class = "container-fluid d-flex justify-content-between align-items-center">

    <div class = "d-flex align-items-center">
      <a href="#" class = "navbar-brand me-4">
        <img src="../assets/img/preventcare_icon3.png" class = "logo_marco" alt="logo">
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
            <ul class="dropdown-menu" aria-labelledby="ABM">
              <li><a href="../views/fREG.php" class="dropdown-item">Registro</a></li>
              <li><a href="../views/fTSV.php" class="dropdown-item">Toma Signos Vitales</a></li>
              <li><a href="../views/fTDM.php" class="dropdown-item">Toma de Muestras</a></li>
              <li><a href="../views/fCCO.php" class="dropdown-item">Composicion Corporal</a></li>
              <li><a href="../views/fSNU.php" class="dropdown-item">Salud Nutricional</a></li>
              <li><a href="../views/fCAU.php" class="dropdown-item">Capacidad Auditiva</a></li>
              <li><a href="../views/fCPU.php" class="dropdown-item">Capacidad Pulmonar</a></li>
              <li><a href="../views/fAVI.php" class="dropdown-item">Agudeza Visual</a></li>
              <li><a href="../views/fAFI.php" class="dropdown-item">Activacion Fisica</a></li>
              <li><a href="../views/fREL.php" class="dropdown-item">Relajacion</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a href="../middleware/logout.php" class="dropdown-item">Salir</a></li> 
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="acciones" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">Acciones
            </a>
            <ul class="dropdown-menu" aria-labelledby="CD">
              <li><a href="../views/FCargaGral.php" class="dropdown-item" >Carga General</a></li>
              <li><a href="../views/FDescarga.php" class="dropdown-item" >Descargar</a></li>
            </ul>                            
          </li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="sobre" role="button" data-bs-toggle="dropdown"
               aria-expanded="false">About
            </a>
            <ul class="dropdown-menu" aria-labelledby="AC">
              <li><a href="../views/fA.php" class="dropdown-item">Acerca de</a></li>
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
        <a href="../middleware/logout.php" class="btn btn-danger">
          Salir
        </a>
<!--        <button type="button" class="btn btn-danger" action="../middleware/logout.php">
          Salir
        </button> -->
      </div>
    </div>
  </div>
</nav>