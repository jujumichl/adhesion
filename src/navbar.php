<?php
function displayNavbar(){
  return 
  '<nav class="navbar navbar-expand-xxxl  fixed-top pt-0">
  <div class="container-fluid d-flex justify-content-between align-items-center" style="background-color:#eeeeed">
    
    <!-- Logo à gauche -->
    <a class="navbar-brand" href="#">
      <img src="./images/logo-bandeau-blanc.jpg" alt="Logo" width="200" height="auto">
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
  class="offcanvas offcanvas-start"
  tabindex="-1"
  id="offcanvass"
  aria-labelledby="offcanvassLabel"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvassLabel" style="color:#d07d29">CC Rennes</h5>
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>
  <div class="offcanvas-body">
<hr/>
    <ul class="list-unstyled">
      <li><a class="dropdown-item" href="index.php?uc=selec">Sélection personnes</a></li>
      <li><a class="dropdown-item" href="index.php?uc=crea">Création personne</a></li>
      <hr/>
      <li><a class="dropdown-item" href="index.php?uc=TB">Tableau de bord</a></li>
      <li><a class="dropdown-item" href="index.php?uc=mooc">horaires animateurs</a></li>
      <hr/>
      <li><a class="dropdown-item" href="index.php?uc=CSV">Intégration fichier CSV</a></li>
      <li><a class="dropdown-item" href="index.php?uc=integ">IntégrationHelloAsso</a></li>
      <hr/>
      <li><a class="dropdown-item" href="index.php?uc=mooc">MOOC</a></li>
      <li><a class="dropdown-item" href="index.php?uc=log">Historique</a></li>
      <hr/>
    </ul>
  </div>
</div>
';
}
