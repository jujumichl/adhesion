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
    try {
        require_once(__DIR__ . '/outils/utils.php');

        require_once(__DIR__ . '/src/selection/selectionMVC.php');

        require_once(__DIR__ . '/src/navbar.php');

        require_once(__DIR__ . '/src/integrationCSV/integrationCSV.php');

        $uc = lireDonneeUrl('uc');
        switch ($uc) {
            case 'selec':
                print displayNavbar().
                displaySelectionHeader().
                selectionController();
                break;
            case 'crea':
                print displayNavbar();
                include(__DIR__ . '/src/creation.php');
                break;
            case 'integ':
                print displayNavbar();
                include(__DIR__ . '/src/integration.php');
                break;
            case 'log':
                print displayNavbar();
                include(__DIR__ . '/src/historique.php');
                break;
            case 'TB':
                print displayNavbar();
                include(__DIR__ . '/src/tableau-bord.php');
                break;
            case 'mooc':
                print displayNavbar();
                include(__DIR__ . '/src/mooc.php');
                break;
            case 'CSV':
                print displayNavbar(). 
                displayIntegrationCsv();
                break;
            case 'upload':
                require_once(__DIR__ . '/src/integrationCSV/traitementCSV.php');
                $csvPath = upload();
                $valide = '/src/integrationCSV/fichier/valide.csv';
                $invalide = '/src/integrationCSV/fichier/invalide.csv';
                $resultat = CSVToSQL($csvPath, 'gestionccr', 'brouillon');
                $html = displaySQLtoCSV($resultat);
                print displayNavbar().
                displayIntegrationCsv($html);
                unlink($csvPath);
                
                break;
            default:
                include(__DIR__ . '/src/connexion.php');
                break;
        }


    } catch(Exception $exp) {
        unlink($csvPath);
        print '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top:50px">' . "Erreur : ".  $exp->getMessage().'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>';
    }

?>
        </div>
    </body>
</html>