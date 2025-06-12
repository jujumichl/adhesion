<nav class="navbar navbar-expand-xxxl bg-body-tertiary">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    
    <!-- Logo à gauche -->
    <a class="navbar-brand" href="#">
      <img src="./images/favicon.png" alt="Logo" width="30" height="30">
    </a>

    <!-- Bouton Menu à droite -->
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#offcanvass"
      aria-controls="offcanvass"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>
<!-- Offcanvas -->
<div
  class="offcanvas offcanvas-end"
  tabindex="-1"
  id="offcanvass"
  aria-labelledby="offcanvassLabel"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvassLabel">Offcanvas</h5>
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>
  <div class="offcanvas-body">
    <ul class="list-unstyled">
      <li><a class="dropdown-item" href="index.php?uc=selec">Sélection</a></li>
      <li><a class="dropdown-item" href="index.php?uc=crea">Création</a></li>
      <li><a class="dropdown-item" href="index.php?uc=integ">Intégration</a></li>
      <li><a class="dropdown-item" href="index.php?uc=log">Historique</a></li>
      <li><a class="dropdown-item" href="index.php?uc=TB">Tableau de bord</a></li>
      <li><a class="dropdown-item" href="index.php?uc=mooc">MOOC</a></li>
      <li><a class="dropdown-item" href="index.php?uc=YB">YB</a></li>
    </ul>
  </div>
</div>
