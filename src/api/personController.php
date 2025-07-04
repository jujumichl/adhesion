<?php
/**************** MODEL ****************************** */
// *** Theses functions gives the data management functions for the API
/* 
 * Search personnes
 */
function getSearchWS($pdo,$searchString) {
 
 
    $stmt = $pdo->prepare('
    select personnes.per_id, per_nom, per_prenom, per_email, per_tel, subscrCOncat, inscrptCOncat from personnes      
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS inscrptCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=1
       GROUP BY per_id) AS inscrp ON inscrp.per_id= personnes.per_id
       
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS subscrCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=2
       GROUP BY per_id) AS subscr ON subscr.per_id= personnes.per_id
             where per_nom  LIKE "%'.  $searchString .'%" OR per_prenom  LIKE  "%'. 
         $searchString .'%" OR per_email LIKE  "%'.  $searchString .'%" order by per_nom'); //
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
   
    return $result;
}


function getInscriptionPersonListByActivityWS($pdo,$act_id) {
 
    $stmt = $pdo->prepare('select personnes.per_id, per_nom, per_prenom, per_email, per_tel, subscrCOncat, inscrptCOncat from personnes      
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS inscrptCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=1
       GROUP BY per_id) AS inscrp ON inscrp.per_id= personnes.per_id
       
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS subscrCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=2
       GROUP BY per_id) AS subscr ON subscr.per_id= personnes.per_id
left join inscriptions on inscriptions.per_id=personnes.per_id

WHERE inscriptions.act_id='.$act_id.'
order by per_nom'); //
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
     return $result;
}



function getInscriptionPersonListToActivityWS($pdo,$act_id) {
 
    $stmt = $pdo->prepare('select personnes.per_id, per_nom, per_prenom, per_email, per_tel, subscrCOncat, inscrptCOncat from personnes      
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS inscrptCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=1
       GROUP BY per_id) AS inscrp ON inscrp.per_id= personnes.per_id
       
LEFT JOIN (select per_id, GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br>") AS subscrCOncat from inscriptions 
       LEFT JOIN activites ON activites.act_id=inscriptions.act_id
       LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
       WHERE typeactivite.tyac_famille=2
       GROUP BY per_id) AS subscr ON subscr.per_id= personnes.per_id
left join inscriptions on inscriptions.per_id=personnes.per_id

WHERE inscriptions.act_id='.$act_id.'
order by per_nom'); //
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
     return $result;
}

/* 
 * Search personnes
 */
function getActivitesWS($pdo ) {
 
  
    $stmt = $pdo->prepare("select * from activites order by act_libelle"); //
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    // print " Résultats " . json_encode($result) ."</br>";

    return $result;
}