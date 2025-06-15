<div class="container">
<?php
    try {
        require_once(__DIR__ . '/vues/header.php');
        require_once(__DIR__ . '/outils/utils.php');

       require_once(__DIR__ . '/vues/selectionVC.php');

        $uc = lireDonneeUrl('uc');
        switch ($uc) {
            case 'selec':
                include(__DIR__ . '/vues/navbar.php');
                
                print displaySelectionHeader();
                print selectionController();

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


    } catch(Exception $exp) {
        print '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top:50px">' . "Erreur : ".  $exp->getMessage().'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>';
    }

?>
</div>