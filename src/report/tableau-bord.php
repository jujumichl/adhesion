<div style="margin-top:20px">
  <div class="row">
        <div class="h5" style="color:#d07d29">Tableaux de bord</div>
        <hr/>
        <div class="col-6">
            <div class="dropdown">
                <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    Tableaux
                </a>

                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="index.php?uc=reportactivite">Participants et montants par activité pour l'année 2024-2025</a></li>
                    <li><a class="dropdown-item" href="index.php?uc=reportintegration">Rapport d'intégration</a></li>
                </ul>
            </div>
        </div>
    </div>


    <hr/>
    <div class="row" style="margin-top:10px">
 
    <?php
    

/**
 * Tableau de bord de l'intégration des fichiers YB
 */
function getReportIntegration($pdo){
    $output="";
    // *** Get current year
    $sql = "SELECT brouillon.brou_annee, ans_id FROM brouillon
    LEFT JOIN an_exercice ON an_exercice.ans_libelle=brouillon.brou_annee";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
  
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if (!count($data )>0)
        throw new Exception ("the brouillon table is empty");    

    $currentYear=$data[0]['ans_id'];

    $sql = "SELECT activites.act_ext_key, activites.act_libelle,sum(ins_montant) AS  act_montant,brou_act AS broui_montant , sum(ins_montant)-brou_act AS delta,
     COUNT(*) AS act_nb, nbbroui as broui_nb , brou_adh FROM inscriptions
        LEFT JOIN activites ON activites.act_id=inscriptions.act_id

        LEFT JOIN (select brou_code, sum(brou_adh) AS brou_adh,sum(brou_act) AS brou_act , COUNT(*) as nbbroui from brouillon 
                        LEFT JOIN modereglement ON modereglement.mreg_code = brouillon.brou_reglement 
                        LEFT JOIN activites ON activites.act_ext_key = brouillon.brou_code 
                        LEFT JOIN an_exercice ON an_exercice.ans_libelle = brouillon.brou_annee
                        GROUP BY  brou_code) AS brouQuery ON brouQuery.brou_code = activites.act_ext_key
                         WHERE inscriptions.ans_id=$currentYear
        GROUP BY activites.act_libelle";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dataCheck = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $output.='  <div class="row" style="margin-top:0px">
        <div class="col-12">
        <p>
        Ce tableau permet de valider la dernière intégration d\'un fichier YB : 
        <ul>
             <li>La source des données est la table brouillon (qui est une copie ddu fichier csv ; La destination de l\'intégration est la \'base\'.</li>
            <li>Les colonnes \'brouillon\' montrent les données présentes dans le fichier YB.</li>
            <li>Les colonnes \'base\' montrent les données intégrées dans la base de données, à partir des données brouillon.</li>
            <li>la présence des 2 colonnes base et brouilon permet de comparer les données intégrées ; en montant et en nombre.</li>
            <li>du fait de la structure de la base de données, un peu différente de la source \'brouillon\',la dernière colonne indique le montant des adhésions dans brouillon, réparti par activité. Ce montant doiit être égal à la ligne
            Adhésion (dans la colonne montant base</li>
        

        </ul>
        </p>
            <div class="h6" style="color:#d07d29">Résultats</div>
            <hr/>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">Code</th>
                    <th scope="col">activité</th>
                    <th scope="col">Montant base</th>
                    <th scope="col">Montant Brouillon</th>
                    
                    <th scope="col">nb base</th>
                    <th scope="col">nb brouillon</th>
                    
                    <th scope="col">Montant adhesion Brouillon</th>
                    </tr>
                </thead>
                <tbody>';

                $act_montant=0;
                $broui_montant =0;                  
                $delta=0;
                $act_nb=0;
                $broui_nb=0;
                $brou_adh=0;

                foreach($dataCheck as $checkline) {
                    $output.=' <tr>';
                    $output.='<td>'.$checkline['act_ext_key'].'</td>';
                    $output.='<td>'.$checkline['act_libelle'].'</td>';
                    $output.='<td>'.($checkline['act_montant']>0 ? $checkline['act_montant'].'€' : '').'</td>';
                    $output.='<td>'.($checkline['broui_montant']>0 ? $checkline['broui_montant'].'€' : '').'€</td>';
                    $output.='<td>'.$checkline['act_nb'].'</td>';
                    $output.='<td>'.$checkline['broui_nb'].'</td>';                    
                    $output.='<td>'.$checkline['brou_adh'].'€</td>';
                    $output.=' </tr>';

                    $act_montant+=$checkline['act_montant'];
                    $broui_montant+=$checkline['broui_montant'];
                    $act_nb+=$checkline['act_nb'];
                    $broui_nb+=$checkline['broui_nb'];
                    $brou_adh+=$checkline['brou_adh'];
                }

                   $output.=' <tr>';
                    $output.='<td></td>';
                    $output.='<td><b>Total</b></td>';
                    $output.='<td><b>'.$act_montant.'€</b></td>';
                    $output.='<td><b>'.$broui_montant.'€</b></td>';
                    
                    $output.='<td><b>'. $act_nb.'</b></td>';
                    $output.='<td><b>'.$broui_nb.'</b></td>';
                    $output.='<td><b>'.$brou_adh.'€</b></td>';
                    $output.=' </tr>';
  
                $output.='</tbody>
            </table>
        </div>
    </div>';
    return $output;
}

/**
 * Report of the current year
 */
function getReportCAbyYear($pdo){
    $output="";
    // *** Get current year
    $sql = "SELECT brouillon.brou_annee, ans_id FROM brouillon
    LEFT JOIN an_exercice ON an_exercice.ans_libelle=brouillon.brou_annee";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
  
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if (!count($data )>0)
        throw new Exception ("the draft table is empty");    

    $currentYear=$data[0]['ans_id'];


    $sql = "SELECT activites.act_ext_key, activites.act_libelle,sum(ins_montant) AS  act_montant , 
     COUNT(*) AS act_nb FROM inscriptions
        LEFT JOIN activites ON activites.act_id=inscriptions.act_id
        LEFT JOIN an_exercice ON an_exercice.ans_id=inscriptions.ans_id     
        WHERE inscriptions.ans_id=$currentYear
        GROUP BY activites.act_libelle
        order by activites.act_libelle";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dataCheck = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $output.='  <div class="row" style="margin-top:0px">
        <div class="col-8">
        <div class="h6" style="color:#d07d29">Bilan année 2024-2025</div>
        <p>
        Ce tableau montre les données de la saison 2024/2025, en montant € et en nombre de participants</br>
        Attention, les adhésions sont indiquées dans une des lignes du tableau.</br>
        Le total est donc le total des recettes de l\'année.
        <ul>
         
        </ul>
        </p>
            <hr/>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                    <th scope="col">Code</th>
                    <th scope="col">activité</th>
                    <th scope="col">Montant base</th>               
                    <th scope="col">Nombre</th>
                    </tr>
                </thead>
                <tbody>';

                $act_montant=0;
                $act_nb=0;

                foreach($dataCheck as $checkline) {
                    $output.=' <tr >';
                    $output.='<td>'.$checkline['act_ext_key'].'</td>';
                    $output.='<td>'.$checkline['act_libelle'].'</td>';
                    $output.='<td >'.($checkline['act_montant']>0 ? number_format($checkline['act_montant'],0).'€' : '').'</td>';
                     $output.='<td >'.$checkline['act_nb'].'</td>';
                     $output.=' </tr>';
                     $act_montant+= $checkline['act_montant'];
                     $act_nb+=$checkline['act_nb'];    
                }

                $output.=' <tr>';
                $output.='<td></td>';
                $output.='<td><b>Total</b></td>';
                $output.='<td><b>'.number_format($act_montant, 0).'€</b></td>';             
                $output.='<td><b>'. number_format($act_nb, 0).'</b></td>';
                $output.=' </tr>';
                $output.='</tbody>
            </table>
        </div>
    </div>';
    return $output;
}













