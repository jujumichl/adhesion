<?php

/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */
function creationController ($pdo) {
 
    $output = '';
    if (isset ($_GET['action'])){
        $action = $_GET['action'];
        if ($action=='getpersonne') {
            if (isset ($_GET['per_id'])) {
                $person = getPerson($_GET['per_id'], $pdo);
                $output .= displayPerson($person);

                $subscriptions= getPersonSubscriptions($_GET['per_id'], $pdo);
                $output .= displaypersonSubscriptions($subscriptions);

                $inscriptions= getPersonInscriptions($_GET['per_id'], $pdo);
                $output .= displaypersonInscriptions($inscriptions);

                $personPayments= getPersonPayments($_GET['per_id'], $pdo);
                $output .= displayPersonPayments($personPayments);

                
             //   print json_encode($person)."</br>";
              // print json_encode($subscriptions)."</br>";
             //  print json_encode($inscriptions)."</br>";

            } else {
                throw new Exception ("Error : getpersonnes without searchString");
            }
        }
    }
    // print json_encode($output);
    return $output;

}



/**************** MODEL ****************************** */
/* 
 * Get a person
 */
function getPerson($per_id, $pdo) {
  $stmt = $pdo->prepare("select * from personnes  left join
    civilites on  personnes.civ_id=civilites.civ_id   where per_id=".$per_id.""); //
  $stmt->execute();
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
   // print " Résultats " . json_encode($result) ."</br>";

  if(count($result) <1) 
    throw new Exception ("La personne n'a pas été trouvée dans la base de données. id : ". $per_id);
  else 
    if (count($result) >1)
      throw new Exception ("Plus d'une personne avec l'id : ". $per_id. " -" .print json_encode($person) );
    else 
      return $result[0];
}

/**
 * 
 */
function getPersonSubscriptions($per_id, $pdo) {
  $stmt = $pdo->prepare("SELECT * FROM inscriptions 
  LEFT JOIN activites ON activites.act_id=inscriptions.act_id
  LEFT JOIN reglements ON reglements.reg_id = inscriptions.reg_id
  LEFT JOIN modereglement ON reglements.mreg_id=modereglement.mreg_id
  LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
  LEFT JOIN an_exercice ON an_exercice.ans_id=inscriptions.ans_id
  WHERE typeactivite.tyac_famille=2 and per_id=".$per_id."");
  $stmt->execute();
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
  return $result;
}

/**
 * 
 */
function getPersonInscriptions($per_id, $pdo) {
  $stmt = $pdo->prepare("SELECT * FROM inscriptions 
  LEFT JOIN activites ON activites.act_id=inscriptions.act_id
  LEFT JOIN reglements ON reglements.reg_id = inscriptions.reg_id
  LEFT JOIN modereglement ON reglements.mreg_id=modereglement.mreg_id
  LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
  LEFT JOIN an_exercice ON an_exercice.ans_id=inscriptions.ans_id
  WHERE typeactivite.tyac_famille=1 and per_id=".$per_id."");
  $stmt->execute();
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
  return $result;
}


/**
 * 
 */
function getPersonPayments($per_id, $pdo) {
  $stmt = $pdo->prepare('SELECT reglements.reg_id, reglements.reg_montant, reglements.reg_date, mreg_code,  GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br> ") as reg_details FROM reglements 
  LEFT JOIN inscriptions ON inscriptions.reg_id=reglements.reg_id
  LEFT JOIN modereglement ON reglements.mreg_id=modereglement.mreg_id
  LEFT JOIN activites ON activites.act_id=inscriptions.act_id
  LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
  LEFT JOIN an_exercice ON an_exercice.ans_id=inscriptions.ans_id
  where per_id='.$per_id.' ');
  $stmt->execute();
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
  return $result;
}

/**************** VIEW ****************************** */

function displayPerson($person) {
 $output='
  <div style="margin-top:100px">
    <div class="h3" style="color:#d07d29">Personne</div>
    <hr/>
    <div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between" style="backgournd-color:">
            <div class="h6" style="color:#d07d29">Identité
            </div>
            <div class="d-flex justify-content-end" >
              <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Sauve identité"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z"/>
                </svg>               
              </div>
            </div>
        </div>
      </div>
      <hr/>
      <div class="col-6">
        <div class="row align-items-center">
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Civilité
              </label>
            </div>
            <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['civ_libelle'].'">
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Nom
              </label>
            </div>
            <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_nom'].'">
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Prénom
              </label>
            </div>
            <div class="col-8 mb-3">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_prenom'].'">
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Email
              </label>
            </div>
            <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_email'].'">
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-2 ">
              <label for="exampleFormControlInput1" class="col-form-label">Téléphone
              </label>
            </div>
            <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_tel'].'">
            </div>
          </div>
      

          <div class="row align-items-center">
              <div class="col-2 ">
                <label for="exampleFormControlInput1" class="col-form-label">Date naissance
                </label>
              </div>
              <div class="col-8">
              <input type="date" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_dat_naissance'].'">
              </div>
            </div>
        
        </div>
        <div class="col-6">

          <div class="row align-items-center">
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Adresse
              </label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_adresse'].'">
            </div>
          </div>

          <div class="row align-items-center">   
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Code postal
              </label>
            </div>
            <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_code_postal'].'">
            </div>
          </div>

          <div class="row align-items-center">   
            <div class="col-2 mb-3">
              <label for="exampleFormControlInput1" class="col-form-label">Ville
              </label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['per_ville'].'">
            </div>
          </div>
    </div> 
  </div>
';
return $output;
}
  
/**
 * 
 */
function displaypersonSubscriptions($personSubscriptions) {

 $output=' <!-- Adhésions --->
    <div class="row" style="margin-top:30px">
      <div class="col-12">
            <div class="d-flex justify-content-between" style="backgournd-color:">
                <div class="h6" style="color:#d07d29">Adhésions
                </div>
                <div class="d-flex justify-content-end" >
                  <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion"> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                      <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                      <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                  </div>
                 </div>
            </div>
        </div>

        <hr/>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Saison</th>
              <th scope="col">Adhésion</th>
              <th scope="col">Date Adhésion</th>
              <th scope="col">Montant</th>
              <th scope="col">Date début</th> 
              <th scope="col">Date fin</th>
              <th scope="col">Règlement</th>              
            </tr>
          </thead>
          <tbody>
          ';
          if (count($personSubscriptions) >0) {
            foreach ($personSubscriptions as $personSubscription ) {
            $output.='<tr>
                <td >'.$personSubscription['ans_libelle'].' </td>
                <td>'.$personSubscription['act_libelle'].'</td>
                <td >'.date("d/m/Y", strtotime( $personSubscription['ins_date_inscription'])).'</td>
                <td>'.$personSubscription['ins_montant'].'€</td>
                <td>'.date("d/m/Y", strtotime($personSubscription['ins_debut'])).'</td>
                <td>'.date("d/m/Y", strtotime($personSubscription['ins_fin'])).'</td>                              
                <td>'.$personSubscription['reg_montant'].'€ - '. $personSubscription['reg_date'].' - '. $personSubscription['mreg_code'].'</td>
              </tr>';
            }
          }  else {
            $output.='<tr><td>Pas d\'adhésion pour cette personne</td></tr>';
          }
          $output.=' 
          </tbody>
        </table>
      </div>
    ';
  return $output;
  }


/**
 * 
 */
function displaypersonInscriptions($personInscriptions) {

   $output=' <!-- Adhésions --->
      <div class="row" style="margin-top:30px">
        <div class="col-12">
              <div class="d-flex justify-content-between" style="backgournd-color:">
                  <div class="h6" style="color:#d07d29">Activités
                  </div>
                  <div class="d-flex je="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion"> 
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                        <path d="M14 1ustify-content-end" >
                    <div class="" styla1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                      </svg>
                    </div>
                   </div>
              </div>
          </div>
  
          <hr/>
          <table class="table table-striped">
            <thead>
              <tr>
               <th scope="col">Saison</th>
                <th scope="col">Date inscription</th>
                <th scope="col">Activité</th>
                <th scope="col">Montant</th>
                <th scope="col">Date début</th> 
                <th scope="col">Date fin</th>
                <th scope="col">Règlement</th>              
              </tr>
            </thead>
            <tbody>
            ';
            if (count($personInscriptions) >0) {
              foreach ($personInscriptions as $personInscription ) {
              $output.='<tr>
                  <td >'.$personInscription['ans_libelle'].' </td>
                  <td >'.date("d/m/Y", strtotime($personInscription['ins_date_inscription'])).' </td>
                  <td>'.$personInscription['act_libelle'].'</td>                
                  <td>'.$personInscription['ins_montant'].'€</td>
                  <td>'.date("d/m/Y", strtotime($personInscription['ins_debut'])).'</td>
                  <td>'.date("d/m/Y", strtotime($personInscription['ins_fin'])).'</td>
                  <td>'.$personInscription['reg_montant'].'€ - '. $personInscription['reg_date'].' - '. $personInscription['mreg_code'].'</td>
                </tr>';
              }
            }  else {
              $output.='<tr><td>Pas d\'inscription pour cette personne</td></tr>';
            }
            $output.=' 
            </tbody>
          </table>
        </div>
      </div>
      ';
  return $output;
}

/**
 * 
 */
function displayPersonPayments($personPayments) {

  print json_encode($personPayments);
  $output=' <!-- Adhésions --->
     <div class="row" style="margin-top:30px">
       <div class="col-12">
             <div class="d-flex justify-content-between" style="backgournd-color:">
                 <div class="h6" style="color:#d07d29">Règlements
                 </div>
                 <div class="d-flex je="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion"> 
                     <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                       <path d="M14 1ustify-content-end" >
                   <div class="" styla1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                       <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                     </svg>
                   </div>
                  </div>
             </div>
         </div>
 
         <hr/>
         <table class="table table-striped">
           <thead>
           <tr>
            <th scope="col">Date règlement</th>
            <th scope="col">Montant règlement</th> 
            <th scope="col">Mode règlement</th>
            <th scope="col">Détail</th>           
          </tr>
           </thead>
           <tbody>
           ';
           if (count($personPayments) >0) {
             foreach ($personPayments as $personPayment ) {
             $output.='<tr>
 
              <td >'.$personPayment['reg_date'].' </td>                
                 <td>'.$personPayment['reg_montant'].'€</td>
                 <td>'.$personPayment['mreg_code'].'</td>
                 <td>'.$personPayment['reg_details'].'</td>
                </tr>';
             }
           }  else {
             $output.='<tr><td>Pas de paiement pour cette personne</td></tr>';
           }
           $output.=' 
           </tbody>
         </table>
       </div>
     </div>
     ';
 return $output;
   }
/**
 * 
 */
function displayRest () {
   $output=' <!-- Participations -->
    <div class="row" style="margin-top:30px">
          <div class="col-12">
              <div class="d-flex justify-content-between" style="backgournd-color:">
                  <div class="h6" style="color:#d07d29">Participations
                  </div>
                  <div class="d-flex justify-content-end" >
                    <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute participation"> 
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                      </svg>
                    </div>
                  </div>
              </div>
          </div>
       
        <hr/>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Année</th>
              <th scope="col">Atelier/Activité</th>
              <th scope="col">Dates</th> 
              <th scope="col">Montant</th>           
            </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">2011</td>
              <td>Musique densemble irlande</td>
              <td>Année</td>
              <td>238€</td>
            </tr>
            <tr>
              <td scope="row">2011</td>
              <td>Astour, musique bretonne accoustique</td>
              <td>Année</td>
              <td>100€</td>
            </tr>
            <tr>
              <td scope="row">2013</td>
              <td>Atelier Gallo</td>
              <td>20/05/2013-25/05/2013</td>
              <td>100€</td>
            </tr>

          </tbody>
        </table>
      </div>

<!-- Règlements -->
      <div class="row" style="margin-top:30px">
        <div class="col-12">
          <div class="d-flex justify-content-between" style="backgournd-color:">
              <div class="h6" style="color:#d07d29">Règlements
              </div>
              <div class="d-flex justify-content-end" >
                <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute règlement"> 
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                  </svg>
                </div>
              </div>
          </div>
      </div>

      <hr/>
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Date règlement</th>
            <th scope="col">Montant règlement</th> 
            <th scope="col">Mode règlement</th>
            <th scope="col">Détail</th>           
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">12/10/2011</td>
            <td>238€</td>
            <td>Cheque</td>
            <td> Musique densemble irlande, adhésion 2011-2012</td>
          </tr>
          <tr>
          <td scope="row">12/10/2013</td>
            <td>23€</td>
            <td>CB</td>
            <td>Stage Gallo</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Rôles -->
<div class="row" style="margin-top:30px">
      <div class="col-12">
        <div class="h6" style="color:#d07d29">Rôle</div>
      </div>
        <hr/>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Rôle</th>
              <th scope="col">Sujet</th> 
            </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">Animateur</td>
              <td>Scottish whispers</td>
            </tr>
            <tr>
            <td scope="row">Référent</td>
              <td>Bombarde</td>
            </tr>
          </tbody>
        </table>
    </div>
</div>
</div>';

 return $output;
}