<?php

/**************** VIEW ****************************** */
/**
 * Display integration form of the integration page
 */
function displayIntegrationCsvBar($resultat = "En attente de fichier...", $nomFichiers = "Aucun fichier sélectionné") {
    $output = '
    <div class="container" style="margin-top:20px">
        <div class="row">
            <div class="col-12" style="">
                <div class="h5" style="color:#d07d29">Intégration fichier CSV </div>
                <hr/>
                Veuillez noter que cette fonction intègre toutes les lignes du fichier choisi mais uniquement celles de l\'année  correspondant à la première ligne du fichier.
                </br>Donc la 1ère ligne du fichier CSV doit porter l\'année à intégrer :
                Le début du fichier CSV ci-dessous, n\'intègrera que les lignes de l\'année 2024-2025 : 
                Nbr,<b>Année</b>,Statut,Nom,Prénom, ... 1,<b>2024-2025</b>,RENOU,AUVIGNE,Florence, ...
                </br></br>
                <ul>
                    <li>Le bouton \'lancer l\'intégration\' effectue toutes les opérations d\'intégration</li>
                    <li>Les boutons suivants permettent d\'activer chacunes des opérations de l\'intégration (Chargement du fichier CSV, vérification du fichier CSV, intégration )               
                </ul>
            </div>
        </div>
        <div class="row" style="margin-top:10px">
            <hr/>
            <div class="col-6" style="margin-top:20px">
            </hr>
                <form action="index.php?uc=upload" method="post" enctype="multipart/form-data">
                    <div class="input-group w-100 float-end">
                        <input                            
                            type="file"
                            id="fileToUpload"
                            name="fileToUpload"
                            class="form-control"
                            placeholder="Choisir un fichier .csv"
                        />
                        <input type="submit" class="btn btn-outline-secondary" name="btSubmit" value="Lancer l\'intégration">
                    </div>
                    <div class="input-group w-100 float-end" style="margin-top:20px">
                        <input type="submit" class="btn btn-outline-secondary" style="margin-left:20px" name="btSubmit" value="Charger le fichier">
                        <input type="submit" class="btn btn-outline-secondary"  name="btSubmit" value="Tester le fichier">
                        <input type="submit" class="btn btn-outline-secondary" name="btSubmit" value="Intégrer">                                
                    </div></div>                   
                </form>
            </div>
        </div>
        
    <div class="h6" style="color:#d07d29; margin-top:20px">Résultat de l\'intégration</div>
        <hr/>
        ';
    return $output;
}

/**
 * Use to display all data in our database
 * @param mixed $donnees
 * @return string
 */
function displaySQLtoCSV($donnees) {

    $output = '
    <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">Nom Prénom</th>
            <th scope="col">Email</th>
            <th scope="col">Téléphone</th>
            <th scope="col">Portable</th>
            <th scope="col">Commune</th>
            <th scope="col">Montant ADH</th>
            <th scope="col">Montant ACT</th>
            <th scope="col">Mode de règlement</th>
            <th scope="col">Code d\'activité</th>
            <th scope="col">Code Postal</th>
            <th scope="col">Année</th>
            <th scope="col">Date d\'adhésion</th>
            <th scope="col">Date de naissance</th>
            <th scope="col">Titre</th>
            </tr>
        </thead>
        <tbody>';
        foreach($donnees as $data) {
            $output.="<tr>
            <td>". $data['brou_nom']. " ". $data['brou_prenom']."</td>
                <td>". $data['brou_email']."</td>
                <td>". $data['brou_telephone']."</td> 
                <td>" . $data['brou_portable'] . "</td> 
                <td>" . $data['brou_commune'] . "</td>
                <td>" . $data['brou_adh'] . "</td>
                <td>" . $data['brou_act'] . "</td>  
                <td>" . $data['brou_reglement'] . "</td>  
                <td>" . $data['brou_code'] . "</td>  
                <td>" . $data['brou_CP'] . "</td>                     
                <td>" . $data['brou_annee']. "</td>  
                <td>" . $data['brou_date_adh'] . "</td>  
                <td>" . $data['brou_date_naiss'] . "</td>  
                <td>" . $data['brou_titre'] . "</td>  
            </tr>";                
        }

    $output.='</tbody>
            </table>
        </div>';
    return $output;
}