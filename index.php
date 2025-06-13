<div class="container">
<?php

require_once(__DIR__ . '/vues/header.php');
require_once(__DIR__ . '/outils/utils.php');
$uc = lireDonneeUrl('uc');
switch ($uc) {
    case 'selec':
        include(__DIR__ . '/vues/navbar.php');
        // include(__DIR__ . '/vues/searchBar.php');
        include(__DIR__ . '/vues/selection.php');
        break;
    case 'crea':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/creation.php');
        break;
    case 'integ':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/integration.php');
        break;
    case 'log':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/historique.php');
        break;
    case 'TB':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/tableau-bord.php');
        break;
    case 'mooc':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/mooc.php');
        break;
    case 'YB':
        include(__DIR__ . '/vues/navbar.php');
        include(__DIR__ . '/vues/YB.php');
        break;
    default:
        include(__DIR__ . '/vues/connexion.php');
        break;
}
include(__DIR__ . '/vues/footer.php');
?>
</div>