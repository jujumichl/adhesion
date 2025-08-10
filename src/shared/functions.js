/**
 * 
 * @returns 
 * /adhesion/src/selectionjs/
 */
export function getPathFromserver() {
  var pathArray = location.pathname.split('/');
  var appPath = "/";
  for (var i = 1; i < pathArray.length - 1; i++) {
    appPath += pathArray[i] + "/";
  }
  return appPath;
}

/**
 * 
 * @returns Get current app path http://host/app
 * https://localhost/adhesion 
 */
export function getAppPath() {
  let appName = '';
  if ((window.location.hostname == 'localhost') || (window.location.hostname == '127.0.0.1')) {
    var path = location.pathname.split('/');
    if (path[0] == "")
      appName = path[1]
    else
      appName = path[0]
    return window.location.protocol + "//" + window.location.hostname + '/' + appName;

  } else {
    return window.location.protocol + "//" + window.location.hostname;
  }
}

/**
 * Add an event listened  to  a list of HTML document (by class name)
 * @param {*} elementClass  : the .XXXX class identifier of the element list 
 * @param {*} functionOfEvent  = the function used when the event is fired
 */
export function addMultipleEnventListener(elementClass, functionOfEvent) {
  const cbox = document.querySelectorAll(elementClass);
  for (let i = 0; i < cbox.length; i++) {
    cbox[i].addEventListener("change", functionOfEvent);
  }
}
/**
 * 
 * @param {*} htmlPartId 
 */
export function headerViewDisplaySelectionjs(htmlPartId) {
  let initString = `<nav class="navbar navbar-expand-xxxl  fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center" style="background-color:#eeeeed">
    
    <!-- Logo à gauche -->
    <a class="navbar-brand" href="#">
      <img src="../../images/logo-bandeau-blanc.jpg" alt="Logo" width="200" height="auto">
       <span class="fs-4" style="margin-left:20px; color:#d07d29">DYB App</span>
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
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=selec">Recherche</a></li>
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=selecjs">Liste de personnes</a></li>
      <!-- <li><a class="dropdown-item" href="${getAppPath()}/ndex.php?uc=crea">Création personne</a></li> -->
      <!-- <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=selecv2">Sélection V2</a></li> -->

      <hr />
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=TB">Tableau de bord</a></li>
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=mooc">horaires animateurs</a></li>
      <hr />
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=CSV">Intégration fichier CSV</a></li>
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=integ">IntégrationHelloAsso</a></li>
      <hr />
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=mooc">MOOC</a></li>
      <li><a class="dropdown-item" href="${getAppPath()}/index.php?uc=log">Historique</a></li>
      <hr />
      <li>Cercle Celtique de Rennes.</br> Application DYB version 1.1 du 10/08/2025</li>
    </ul>
  </div >
  
</div >
    `;
  document.querySelector("#" + htmlPartId).innerHTML = initString;
}

export function getappRelease() {
  return

}