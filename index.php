<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestion CC Rennes</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap JS (bundle avec Popper inclus) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <link href="css/custom.css" rel="stylesheet">

</head>
<body>
    <div class="container">
<?php
    ini_set( 'display_errors', 1 ); // a enlever en prod
    try {
        require_once './outils/utils.php';

        require_once './src/selection/selectionMVC.php';
 
        require_once './src/navbar.php';

        require_once './src/integrationCSV/integrationCSV.php';

        require_once './src/selection/brevo/brevo-modal.php';

        require_once './config.php';
        $pdo = init_pdo($dbHost, $db, $dbUser, $dbMdp);
        $uc = lireDonneeUrl('uc');
        switch ($uc) {
            case 'selec':
                print displayNavbar().
                displaySelectionHeader($pdo).
                selectionController($pdo);
                break;
            case 'crea':
                print displayNavbar();
               //  displaySelectionHeader();
                print creationController($pdo);
                break;
            case 'integ':
                print displayNavbar();
                include './src/integration.php';
                break;
            case 'log':
                print displayNavbar();
                include './src/historique.php';
                break;
            case 'TB':
                print displayNavbar();
                include './src/tableau-bord.php';
                break;
            case 'mooc':
                print displayNavbar();
                include './src/mooc.php';
                break;
            case 'CSV':
                print displayNavbar(). 
                displayIntegrationCsv();
                break;
            case 'upload':
                require_once './src/integrationCSV/traitementCSV.php';
                print displayNavbar().
                 displayIntegrationCsv($msgErr, $nomFichier);
                break;
            default:
                include './src/connexion.php';
                break;
        }


    } catch(Exception $exp) {
        // if ($csvPath){unlink($csvPath);}
        print '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top:50px">' . "Erreur : ".  $exp->getMessage().'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>';
    }

?>
        </div>
    </body>
</html>