<?php



/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */
function selectionController () {
    // $output = displaySelectionHeader();
    $output = '';
    if (isset ($_GET['action'])){
        $action = $_GET['action'];
        if ($action=='getpersonnes') {
            if (isset ($_GET['searchString'])) {
                // print $_GET['searchString'];
                $personsList = getsearch($_GET['searchString']);
                $output .= displayPersonList($personsList);
            } else {
                throw new Exception ("Error : getpersonnes without searchString");
            }
        }
    }
    return $output;

}

/**************** VIEW ****************************** */
/**
 * Display header of the selection page
 */
function displaySelectionHeader() {

    $searchString='';
    if (isset ($_GET['searchString'])) 
        $searchString = $_GET['searchString'];
    
    $output='
<div style="margin-top:100px">
   <!-- Recherche -->
    
        <form  method="get"  href="index.php">   
            <input type="hidden"  value="selec" name="uc" >
            <input type="hidden"  value="getpersonnes" name="action" >
            <div class="h5" style="color:#d07d29">Sélection</div>
            <hr/>
<div class="row">
            <div class="col-4">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="" id="searchString" name="searchString" aria-label="" aria-describedby="" value="'.$searchString.'">
                   <button type="submit" value="Submit"  class="btn btn-outline-secondary" id="button-addon1">Chercher</button>
                   </div>            
            </div>
           <div class="col-2" style="margin-right:30px">
               <div class="dropdown" >
                    <a class="btn btn-outline-secondary dropdown-toggle"  href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        Recherches définies
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#">Adhésions</a></li>
                        <li><a class="dropdown-item" href="#">Atelier musique irlandaise</a></li>
                        <li><a class="dropdown-item" href="#">Astour</a></li>
                    </ul>
                </div>               
            </div>

            <div class="col-1">
               <div class="dropdown">
                    <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        Année
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#">Par défaut - année en cours</a></li>
                        <li><a class="dropdown-item" href="#">2024-2025</a></li>
                        <li><a class="dropdown-item" href="#">2023-2024</a></li>
                        <li><a class="dropdown-item" href="#">Tout</a></li>
                    </ul>
                </div>
            </div>
        
    </div>
    </form>
</div>
    ';

    return $output;
}

/**
 * 
 */
function displayPersonList($personList) {

    // print json_encode($personList);

    $output='   <div class="row" style="margin-top:30px">
        <div class="col-12">
            <div class="d-flex justify-content-between" style="backgournd-color:">
                <div class="h6" style="color:#d07d29">Résultats
                </div>
                <div class="d-flex justify-content-end" >
                    <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-mailbox-flag" viewBox="0 0 16 16">
                            <path d="M10.5 8.5V3.707l.854-.853A.5.5 0 0 0 11.5 2.5v-2A.5.5 0 0 0 11 0H9.5a.5.5 0 0 0-.5.5v8zM5 7c0 .334-.164.264-.415.157C4.42 7.087 4.218 7 4 7s-.42.086-.585.157C3.164 7.264 3 7.334 3 7a1 1 0 0 1 2 0"/>
                            <path d="M4 3h4v1H6.646A4 4 0 0 1 8 7v6h7V7a3 3 0 0 0-3-3V3a4 4 0 0 1 4 4v6a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V7a4 4 0 0 1 4-4m0 1a3 3 0 0 0-3 3v6h6V7a3 3 0 0 0-3-3"/>
                        </svg>
                    </div>
                    <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Exporter la liste"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                            <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                            <path d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                        </svg>
                    </div>
                    <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Imprimer la liste"> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                        </svg>                    
                    </div>
                </div>
            </div>
        </div>
            <hr/>
            <div>' . count($personList) .' résultats dans la liste</div>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">Nom Prénom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Téléphone</th>
                    <th scope="col">Rôles</th>
                    <th scope="col">Adhésions</th>
                    <th scope="col">Activités</th>            
                    </tr>
                </thead>
                <tbody>';
                foreach($personList as $person) {
                    $output.="<tr>
                    <td>". $person['per_prenom']. " ". $person['per_nom']."</td>
                     <td>". $person['per_email']."</td>
                     <td>". $person['per_tel']."</td> 
                     <td></td> 
                     <td></td>
                     <td></td>                         
                    </tr>";                
                }

    $output.='</tbody>
            </table>
        </div>';

    return $output;
}

/**************** MODEL ****************************** */
/* 
 * Search personnes
 */
function getSearch($searchString) {
 
    $host = 'localhost';
    $db   = 'adhesion';
    $user = 'root';
    $pass = '';
    $port = "3306";
    $charset = 'utf8mb4';

    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    $pdo = new \PDO($dsn, $user, $pass, $options);


    $stmt = $pdo->prepare("select * from personnes      where per_nom  LIKE  '%".  $searchString ."%' OR per_prenom  LIKE  '%".  $searchString ."%' OR per_email LIKE  '%".  $searchString ."%'"); //
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    // print " Résultats " . json_encode($result) ."</br>";

    return $result;
}