<?php

/**************** MODEL ****************************** */
// *** Theses functions gives the data management functions for the API
/* 
 * Search personnes
 */
function getSearchWS($pdo,$searchString) {
 
//   print 'getSearch';
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

/**
 * 
 */
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


/**
 * 
 */
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

/* 
 * Search personnes
 */
function getActivityWS($pdo , $act_id) {

    $stmt = $pdo->prepare('select * from activites  where act_id='.$act_id);
    $stmt->execute();
    $result = $stmt->fetch();
    // print " Résultats " . json_encode($result) ."</br>";
    if ($result) {
      // print json_encode($result);
      return $result;
    } else 
      return null;

}

/**************** MODEL ****************************** */
/* 
 * Get a person
 */
function getPersonApi($per_id, $pdo) {
  $person=null;

  // *** Get the person
  $stmt = $pdo->prepare("select * from personnes  left join
    civilites on  personnes.civ_id=civilites.civ_id   where personnes.per_id=".$per_id.""); 
  $stmt->execute();
  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

  if(count($result) <1) 
    throw new Exception ("La personne n'a pas été trouvée dans la base de données. id : ". $per_id);
  else 
    if (count($result) >1)
      throw new Exception ("Plus d'une personne avec l'id : ". $per_id. " -" . json_encode($person) );
    else 
      $person= $result[0];

    // *** Get person subscriptions
  $person['subscriptions']= getPersonSubscriptionsApi($per_id, $pdo);

      // *** Get person purchases
  $person['purchases']= getPersonInscriptionsApi($per_id, $pdo);

      // *** Get person payments
  $person['payments']= getPersonPaymentsApi($per_id, $pdo);

    return $person;
  }
  
  /**
   * 
   */
  function getPersonSubscriptionsApi($per_id, $pdo) {
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
  function getPersonInscriptionsApi($per_id, $pdo) {
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
  function getPersonPaymentsApi($per_id, $pdo) {
    $stmt = $pdo->prepare('SELECT reglements.reg_id, reglements.reg_montant, reglements.reg_date, mreg_code,  GROUP_CONCAT(concat(ins_date_inscription, " - ",act_libelle) SEPARATOR  "</br> ") as reg_details FROM reglements 
    LEFT JOIN inscriptions ON inscriptions.reg_id=reglements.reg_id
    LEFT JOIN modereglement ON reglements.mreg_id=modereglement.mreg_id
    LEFT JOIN activites ON activites.act_id=inscriptions.act_id
    LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
    LEFT JOIN an_exercice ON an_exercice.ans_id=inscriptions.ans_id
    where per_id='.$per_id.'
      GROUP BY reglements.reg_id, reglements.reg_montant, reglements.reg_date, mreg_code
      order by reglements.reg_date');
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }