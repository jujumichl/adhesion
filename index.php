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

 
        require_once './src/navbar.php';

       //  require_once './src/integrationCSV/integrationCSV.php';

        // require_once './src/selection/brevo/brevo-modal.php';

        require_once './src/creation/creationMVC.php';

       include './config.php';
       $pdo = init_pdo($dbHost, $db, $dbUser, $dbMdp);

    //    if ($pdo ==null)
    //      throw new Exception("pdo not intitialised");

        $uc = lireDonneeUrl('uc');
        switch ($uc) {
            case 'selec':
                require_once './src/selection/selectionMVC.php';
 
                print displayNavbar();
                print displaySelectionHeader($pdo);
                print selectionController($pdo);
                break;
            case 'selecjs':
                header('Location: src/selectionjs/selectionjs.html');
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
                require_once './src/integrationCSV/integrationTraitementCSV.php';
                require_once './src/integrationCSV/integrationControllerCSV.php';
                require_once './src/integrationCSV/integrationUploadCSV.php';
                print displayNavbar();
                // throw new Exception ("Dans une première version, cette fonction n'est disponible que pour les administrateurs");

                print displayNavbar();
               print displayIntegrationCsvBar();
                break;
            case 'upload':
                require_once './src/integrationCSV/integrationTraitementCSV.php';
                require_once './src/integrationCSV/integrationControllerCSV.php';
                require_once './src/integrationCSV/integrationUploadCSV.php';
                print displayNavbar();
                print displayIntegrationCsvBar();
                print launchIntegration ($pdo);
                break;    
                
            case 'report':
                print displayNavbar();
                 require_once './src/report/tableau-bord.php';
                 print getReportCAbyYear($pdo);
                break;
                case 'reportactivite':
                    print displayNavbar();
                    require_once './src/report/tableau-bord.php';
                    print getReportCAbyYear($pdo);
                    break;
                case 'reportintegration':
                    print displayNavbar();
                    require_once './src/report/tableau-bord.php';
                    print getReportIntegration($pdo);
                    break;
                case 'doc':
                    require_once './src/documentation/documentation.php';
                    print displayNavbar();
                    print getDocumentation();
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