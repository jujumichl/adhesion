<?php







/**************** VIEW ****************************** */
/**
 * Display integration form of the integration page
 */
function displayIntegrationCsv($resultat = "En attente de fichier...", $nomFichiers = "Aucun fichier sélectionné") {
    $output = '
    <div style="margin-top:100px">
    <div class="row">
        <div class="h5" style="color:#d07d29">Sélection</div>
        <hr/>
        <div class="col-6">
        <form name="foo" action="index.php?uc=upload" method="post" enctype="multipart/form-data" class="form-floating">
            <div class="input-group w-100 float-end">
                <input
                    type="file"
                    id="fileToUpload"
                    name="fileToUpload"
                    class="form-control"
                />
                ';
    if ($nomFichiers !== "Aucun fichier sélectionné"){
        $output .= '</div>
        <small class="form-text text-muted">Dernier fichier importer : ' . htmlspecialchars($nomFichiers) . '</small>
        <div class="input-group w-100 float-end">';
    }
    $output .= '</div>
        </div>
            <div class="col-6">
            <button type="submit" class="btn btn-outline-secondary">Lancer l\'intégration</button>        </div>
            </div>
        </form>
        </div>
    <div class="h6" style="color:#d07d29; margin-top:20px">Résultat de l\'intégration</div>
        <hr/>
        ' . $resultat . '</div>';
    return $output;
}

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