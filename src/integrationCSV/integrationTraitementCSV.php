<?php 
/**
 * Analyse brouillon table and generate personnes, inscriptions and reglements
 * @param mixed $pdo pdo login
 * @return string
 *   $data = Array (
 *       [0] => Array ( 
 *           [brou_id] => ...............................................
 *           [brou_nom] => ...............................................
 *           [brou_prenom] => ...............................................
 *           [brou_portable] => ...............................................
 *           [brou_email] => ...............................................
 *           [brou_commune] => ...............................................
 *           [brou_adh] => ...............................................
 *           [brou_act] => ...............................................
 *           [brou_reglement] => ...............................................
 *           [brou_code] => ...............................................
 *           [brou_CP] => ...............................................
 *           [brou_annee] => ...............................................
 *           [brou_date_adh] => ...............................................
 *           [brou_date_naiss] => ...............................................
 *           [brou_titre] => ...............................................
 *           [brou_telephone] => ..............................................
 *           )
 *       ) 
 */
function parseAndStoreData($pdo,$modetest){
    (string)$message = "";
    $data = null;
    //$count=0;
    //$count1=0;
    $sql = "select brou_email, count(*) AS tot from brouillon  group by brou_email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    // *** Set the year 
    foreach ($result as $res){
        try{
            // check email
            if (!(filter_var( $res['brou_email'] , FILTER_VALIDATE_EMAIL))) {
                 throw new Exception(" ***** Email invalide " . $res['brou_email']);
            }

            // *** Get the lines of one person
            $sql = "select * from brouillon 
                    LEFT JOIN modereglement ON modereglement.mreg_code = brouillon.brou_reglement 
                    LEFT JOIN activites ON activites.act_ext_key = brouillon.brou_code 
                    LEFT JOIN an_exercice ON an_exercice.ans_libelle = brouillon.brou_annee
                    where brouillon.brou_email = :mail;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':mail' => $res['brou_email']
            ]);
           print "</br>*** line : " . $res['brou_email'] . " : ";
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // print "base.". json_encode($baseTab)." </br>";           
            $datenaiss = stringToDate(
                substr($data[0]['brou_date_naiss'], 0,-8),
                substr($data[0]['brou_date_naiss'], 3, -5),
                substr($data[0]['brou_date_naiss'], -4));

            // *** Create the person and return the ID of person this person
            $per_id = createPers($data[0], $datenaiss, $pdo);
            
            // *** Check date and mreg validity
            foreach ($data as $line){
                $dateAdh = stringToDate(
                substr($line['brou_date_adh'], 0,-8),
                substr($line['brou_date_adh'], 3, -5),
                substr($line['brou_date_adh'], -4));

                // Check date
                if (strtotime($dateAdh) === false){
                    throw new Exception("***** Date d'adhésion invalide " . $line['brou_email'] . '<br>');
                }
                
                // check mreg 
                if (is_null($line['mreg_id'])){
                    throw new Exception("***** Mode de règlement invalide " . $line['brou_email'] . '<br>');
                }
                
                //check act code
                if (is_null($line['act_ext_key'])){
                    throw new Exception("***** Code d'activité invalide " . $line['brou_email'] . '<br>');
                }

                if (is_null($line['ans_id'])){
                    throw new Exception("***** saison invalide " . $line['brou_email'] . '<br>');
                }
            }

            // *** main computation
            computeMultipleLines($data,$per_id, $dateAdh, $pdo,$modetest);
            
          } catch (Exception $e){
                print $e->getMessage();
                $sql = "insert into `integrationerrors` ( textError,lineData ) Values ( :message, :linedata)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                ':message'=>$e->getMessage(),
                ':linedata'=>json_encode($data)]);               
                $stmt->execute();
 
            // }
        }
    }
    return $message;
}

/**
 * Return the index of a line inside an array of lines 
 * compare the 2 objects
 */
function getLinesIndexFromKey($baseTab,$key,$per_id) {
    $foundIndex= -1;
    // print "base.". json_encode($baseTab)." </br>";
    // print "lkey.". $key." </br>";

    $returntab=[];
    for ( $i=0; $i<count($baseTab);++$i) {
        $keyCalc= $per_id.$baseTab[$i]['brou_date_adh'].$baseTab[$i]['mreg_id'].$baseTab[$i]['ans_id'];
        if ($keyCalc == $key) {
            array_push($returntab,$baseTab[$i]);
        }
    }
   //  print "Trouvé".json_encode( $returntab)."</br>";
    return  $returntab;
}
/**
 * Analyse a year of subs and act for a person
 * Create subs and acttry to find 1) single lines 2) double lines 3) triple lines
 * for example : within a year, double lines are lines created the same day with the same payment mode 
 * The main loop 
 */
function computeMultipleLines($tempLines,$per_id, $dateAdh, $pdo,$modetest) {

    // *** Compute  the keys of each line 
    $temptab=[];
   // print "Start tab Tab :' " . json_encode($tempLines)."</br>";;
    for ($line=0; $line<count($tempLines);++$line) {
        array_push($temptab,$per_id.$tempLines[$line]['brou_date_adh'].$tempLines[$line]['mreg_id'].$tempLines[$line]['ans_id']);
    }

    // *** Compute the nb of line per key
    $countValues=array_count_values($temptab);

   // *** Create each registration line needed
   // print "counvalue Tab :' " . json_encode($countValues)."</br>";;
   $compte=0;
   foreach ($countValues as $key =>$countValue) {
        print  " key = ". $key;
        print " value = ". $countValue."</br>";
        $lineToCompute=getLinesIndexFromKey($tempLines,$key, $per_id );
       //  print "Lines to compute : ". json_encode( $lineToCompute)."</br>";
        addRegistrationLines($lineToCompute, $per_id, $pdo,$modetest);
       }

}

/**
 * 
 */
function checkAllreadyExists($per_id,$act_id,$ans_id,$pdo) {
           // *** Get the lines of one person
  //  print" check allready exists per_id='".$per_id."' and act_id='".$act_id."' and ans_id='".$ans_id."'";
    $sql = "select * from inscriptions where per_id='".$per_id."' and act_id='".$act_id."' and ans_id='".$ans_id."'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    // print "Data " .json_encode( $data );
    if (count($data )>0)
        return true;
    else
        return false;
}


/**
 * Use to add several registration lines
 * @param mixed $data one array of two line
 * @param mixed $per_id id of person
 * @param mixed $dateAdh date of payment
 * @param mixed $pdo pdo login
 * @throws \Exception if we haven't got activity and subscription
 * @return void
 */
function addRegistrationLines($data, $per_id, $pdo,$modetest){
    print "addRegistrationLines" .  "</br>"; // json_encode($data).
     $dateAdh = stringToDate(
         substr($data[0]['brou_date_adh'], 0,-8),
         substr($data[0]['brou_date_adh'], 3, -5),
         substr($data[0]['brou_date_adh'], -4));
 
     // *** Compute total 
     $total = 0;
     foreach($data as $line){
         $total += (int)$line['brou_adh'] + (int)$line['brou_act'];
     }
 
     // *** Create payment
    
     $reg_id = createPayment($total, $dateAdh,  $data[0]['mreg_code'], $per_id, $pdo,$modetest);
     // print "Create Payment " .  $reg_id."</br>"; // json_encode($data). " - " .
 
     foreach ($data as $line){
         // *** Create Activity
         if ($line['brou_code'] !== ''){
             $id_act = createAct($line,$per_id, $reg_id, $dateAdh, $pdo,$modetest);
             // print "Create activity : " . json_encode($line) . "</br>";
         }
 
         // *** Create subscription
         if ($line['brou_adh']>0 && !isset($adh_id)){
             $line['codeADH'] = 'AUT01';
             $adh_id = createSubscription($line,$per_id, $reg_id, $dateAdh, $pdo,$modetest);   
             // $message .="Création d'une adhésion pour " . $data[0]['brou_nom'] . " " . $data[0]["brou_prenom"] . " identifier " . $per_id;
         }
     }
     if (!isset($id_act)){
         throw new Exception("Pas de code d'activité pour " . $data[0]['brou_email']);
     }
 }
 
/**
 * use to create only one people
 * @param mixed $result data of people
 * @param mixed $pdo pdo login
 * @return mixed 
 */
function createPers($data,$date_naiss, $pdo){
    print "Create person : " .$data['brou_email']. "</br>";
    //check if person isn't in db
    $sql = 'SELECT per_id FROM personnes WHERE per_email = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email' => $data['brou_email']
    ]);
    $per_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //if not in
    if (count($per_id) < 1) {
        if ($data['brou_titre'] === 'Madame'){
            $data['brou_titre'] = 2;
        }
        else if ($data['brou_titre'] === 'Monsieur'){
            $data['brou_titre'] = 1;
        }
        //$data = just array of data cf $result
        $sql = "INSERT INTO `personnes`(
        per_nom,
        civ_id,
        per_prenom,
        per_tel,
        per_email,
        per_code_postal,
        per_ville,
        per_dat_naissance
        )
        Values (
        :nom,
        :civ,
        :prenom,
        :tel,
        :mail,
        :cp,
        :ville,
        :naiss)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'=>$data['brou_nom'],
            ':civ'=>$data['brou_titre'],
            ':prenom'=>$data['brou_prenom'],
            ':tel'=>$data['brou_portable'],
            ':mail'=>$data['brou_email'],
            ':cp'=>$data['brou_CP'],
            ':ville'=>$data['brou_commune'],
            ':naiss'=>$date_naiss
        ]);

        $per_id = $pdo->lastInsertId();
        return $per_id;
    }
    else{
        return $per_id[0]['per_id'];
    }
}

/**
 * create one subscription
 * @param array $data data of createPers
 * @param mixed $pdo PDO login
 */
function createSubscription($data, $per_id, $reg_id, $dateAdh, $pdo){
    $act_id = getIDActivity($data['codeADH'], $pdo);
    print "Create Subscription : " . $per_id . " - " . $reg_id . " - " . $data['codeADH']." - " . $data['brou_adh']. "</br>";

    // *** Check if the line allready exists in the database
    if (checkAllreadyExists($per_id,$act_id,$data['ans_id'],$pdo)==true) {
        print "Subs allready exists in the database : " . $per_id ." - " .$act_id." - " .$data['ans_id']. "</br>";
        return;
    }
    // print "</br>Create subs: " .  $reg_id."</br>"; // json_encode($data) . " - " .

    $sql = 'INSERT INTO `inscriptions`(
        per_id,
        act_id,
        ins_date_inscription,
        reg_id,
        ins_debut,
        ins_fin,
        ins_montant,
        ans_id
        )VALUES(
        :per_id,
        :act_id,
        :ins_date_inscription,
        :reg_id,
        :ins_debut,
        :ins_fin,
        :ins_montant,
        :ans_id
    )
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':per_id' => $per_id,
        ':act_id' => $act_id,
        ':ins_date_inscription' => $dateAdh,
        ':reg_id' => (int)$reg_id,
        ':ins_debut' => $dateAdh,
        ':ins_fin' => getEndOfSeasonDate($dateAdh),
        ':ins_montant' => (float)$data['brou_adh'],
        ':ans_id' => (int)$data['ans_id']
    ]);  
  
    return $pdo->lastInsertId(); 
}

/**
 * Create one activity
 * @param mixed $data data of createPers
 * @param mixed $pdo PDO login
 */
function createAct($data, $per_id, $reg_id, $dateAdh, $pdo){
    $act_id = getIDActivity($data['brou_code'], $pdo);
    print "Create activity : " . $per_id . " - " . $reg_id . " - " . $data['act_id']. " - " . $data['brou_act']. "</br>";

        // *** Check if the line allready exists in the database
    if (checkAllreadyExists($per_id,$act_id,$data['ans_id'],$pdo)==true) {
        throw new Exception(" Act allready exists in the database : " . $per_id ." - " .$act_id." - " .$data['ans_id']. "</br>");
        return;
    }
    // print "</br>Create Activity : " .  $reg_id."</br>"; // json_encode($data) . " - " .
    $sql = 'INSERT INTO `inscriptions`(
    per_id,
    act_id,
    ins_date_inscription,
    reg_id,
    ins_debut,
    ins_fin,
    ins_montant,
    ans_id
    )VALUES(
    :per_id,
    :act_id,
    :ins_date_inscription,
    :reg_id,
    :ins_debut,
    :ins_fin,
    :ins_montant,
    :ans_id
    )
    ';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':per_id' => $per_id,
        ':act_id' => $act_id,
        ':ins_date_inscription' => $dateAdh,
        ':reg_id' => (int)$reg_id,
        ':ins_debut' => $dateAdh,
        ':ins_fin' => getEndOfSeasonDate($dateAdh),
        ':ins_montant' => (float)$data['brou_act'],
        ':ans_id' => (int)$data['ans_id']
    ]);


    return $pdo->lastInsertId();
}

/**
 * get back the id the activity code which passed in param
 * @param string $code code of one activity
 * @param mixed $pdo pdo login
 */
function getIDActivity($code, $pdo){
    $sql = 'select act_id from activites where act_ext_key = :code';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code'=> $code
        ]);
    $act_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $act_id[0]['act_id'];
}

/**
 * Use to create one payment for one person
 * @param mixed $montant_adh amount of subscription
 * @param mixed $montant_act amount of activity
 * @param mixed $dateAdh the date of payment
 * @param mixed $mreg method of payment
 * @param mixed $per_id id of one person
 * @param mixed $pdo pdo login
 * @throws \Exception if the method of payment is unknown
 * @return int return reg_id of amount
 */
function createPayment($total, $dateAdh, $mreg, $per_id, $pdo){
    print "Create payment : " . $per_id . " - " . $mreg . " - " .$total. "</br>";
    $sql = 'select mreg_id from modereglement where mreg_code = :mreg';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ ':mreg' => $mreg]);
    $mregID = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if (empty($mregID[0]['mreg_id'])) {
        throw new Exception ("Modèle de règlement non connu ou invalide pour la personne identifier : " . $per_id);
    }
    else {
        $sql = 'INSERT INTO reglements(
        `reg_montant`,
        `mreg_id`,
        `reg_date`
        )
        VALUES(
        :total,
        :mregid,
        :dateAdh
        )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':total' => $total,
            ':mregid'=> $mregID[0]['mreg_id'],
            ':dateAdh' => $dateAdh
        ]);
        $reg_id = $pdo->lastInsertId();

        return $reg_id;
    }
}


///*************** TRASH */
// /**
//  * get back reg_id and check if he is already inside our database or not, add it if not in
//  * @param mixed $per_id id of one person
//  * @param mixed $montant_adh amount of subscription
//  * @param mixed $montant_act amount of activity
//  * @param mixed $dateAdh the date of payment
//  * @param mixed $mreg method of payment
//  * @param mixed $pdo pdo login
//  * @return int return reg_id of the payment
//  */
// function getamount($per_id, $total, $dateAdh, $mreg, $pdo){
//     $sql = 'select reg_id from inscriptions where per_id = :id';
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([':id' => $per_id]);
//     $amoutid = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
//     // If $amoutid is empty, it means no registration was found, so create a new payment.
//     if (empty($amoutid)){
//         return createPayment(
//             $total,
//             $dateAdh,
//             $mreg,
//             $per_id,
//             $pdo
//         );
//     }
//     // If $amoutid is NOT empty, it means a registration was found, so return its reg_id.
//     else {
//         //print json_encode($amoutid) . '<br>';
//         return $amoutid[0]['reg_id'];
//     }
// }


// /**
//  * get back the id of one person by his email
//  * @param mixed $email the email of this person
//  * @param mixed $pdo pdo login
//  */
// function getIdPerson($email, $pdo){
//     $sql = 'select per_id from personnes where per_nom = :email';
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([
//         ':email'=> $email
//         ]);
//     $per_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//     return $per_id[0]['per_id'];
// }


// /**
//  * Use to add one line
//  * @param mixed $data data of one line
//  * @param mixed $per_id id of one person
//  * @param mixed $dateAdh the date of payment
//  * @param mixed $pdo pdo login
//  * @return void
//  */
// function addOneLine($data,$per_id, $dateAdh, $pdo){
//     $dateAdh = stringToDate(
//         substr($data['brou_date_adh'], 0,-8),
//         substr($data['brou_date_adh'], 3, -5),
//         substr($data['brou_date_adh'], -4));

    
//     $total = (int)$data['brou_adh'] + (int)$data['brou_act'];
//     $reg_id = createPayment($total, $dateAdh, $data['mreg_code'], $per_id, $pdo);
//     // $reg_id = getamount(
//     //     $per_id,
//     //     $total,
//     //     $dateAdh,
//     //     $data['mreg_code'],
//     //     $pdo);

//     // print "Il n'y a qu'une seule ligne";
//     if ($data['brou_code'] !== ''){
//        createAct($data,$per_id, $reg_id, $dateAdh, $pdo);
//         // $message .= "Création d'une activitées...";
//     }

//     if ($data['brou_adh']>0){
//         $data['codeADH'] = 'AUT01';
//         createSubscription($data,$per_id, $reg_id, $dateAdh, $pdo);
//         // $message .="Création d'une adhésion pour " . $data[0]['brou_nom'] . " " . $data[0]["brou_prenom"] . " identifier " . $per_id;
//     }
// }



// /**
//  * Return the index of a line inside an array of lines 
//  * compare the 2 objects
//  */
// function getLineIndex($baseTab,$line) {
//     $foundIndex= -1;
//     // print "base.". json_encode($baseTab)." </br>";
//     // print "line.". json_encode($line)." </br>";

//     for ( $i=0; $i<count($baseTab);++$i) {
//         // print $i. " - " . json_encode($baseTab[$i]['brou_id'])."</br>";
//         if ($baseTab[$i]['brou_id'] == $line['brou_id'])
//             $foundIndex= $i;
//     }
//     return  $foundIndex;
// }
// /**
// * Return the first single line found
// */
// function getSingleLine1($tempLines) {

//     // print "GetSingleline : " . json_encode($tempLines). "</br></br>" ;
//     $foundDouble=false;
//     // for ($line=0; $line<count($tempLines)-1;++$line) {
//     for ($i=1; $i<count($tempLines);++$i) {
//         if ($tempLines[0]['brou_date_adh']== $tempLines[$i]['brou_date_adh'] && ($tempLines[0]['mreg_id']== $tempLines[$i]['mreg_id'])
//         && ($tempLines[0]['ans_id']== $tempLines[$i]['ans_id'])) {
//             $foundDouble=true;
//         }
//     }
    
//     if (!$foundDouble && count($tempLines)>0) {
//        // print "Found single : " . json_encode($tempLines[0]). "</br></br>" ;
//         print "Found single line, " . $tempLines[0]['brou_id']. " -" .$tempLines[0]['brou_code'];
//         $returnTab=[];
//         array_push( $returnTab,$tempLines[0]);
//         return $returnTab;
//     } else { 
//         return null;
//     }
// }

// /**
// * Return the first single line found
// */
// function getSingleLine($tempLines) {

//     // print "GetSingleline : " . json_encode($tempLines). "</br></br>" ;

//     $temptab=[];
//     for ($line=0; $line<count($tempLines);++$line) {
//         array_push($temptab,$tempLines[$line]['brou_date_adh'].$tempLines[$line]['mreg_id'].$tempLines[$line]['ans_id']);
//     }
//     $uniqueTabs=array_unique( $temptab);

//     if (count($uniqueTabs)>0) {
//         for ($line=0; $line<count($tempLines);++$line) {
//             if ($tempLines[$line]['brou_date_adh'].$tempLines[$line]['mreg_id'].$tempLines[$line]['ans_id']==$uniqueTabs[0])
//                 $returnTab=[];
//                 array_push( $returnTab,$tempLines[$line]);
//                 return $returnTab;
//         }
//     }
//     return null;
// }

// /**
// * Return the first single linge found
// */
// function getDoubleLines($tempLines) {

//     // print "GetDoubleline : " . json_encode($tempLines). "</br></br>" ;
//     $returnTab=[];

//     $foundDouble=-1;
//     // for ($line=0; $line<count($tempLines)-1;++$line) {
//     for ($i=1; $i<count($tempLines);++$i) {
//         if ($tempLines[0]['brou_date_adh']== $tempLines[$i]['brou_date_adh'] && ($tempLines[0]['mreg_id']== $tempLines[$i]['mreg_id'])
//         && ($tempLines[0]['ans_id']== $tempLines[$i]['ans_id'])) {
//             if ($foundDouble==-1)
//                 $foundDouble=$i;
//             else
//                 $foundDouble=-1;    // the is more than 2 lines, this function can't compute this case
//         }
//     }
    
//     if ($foundDouble>0 ) {
//         array_push($returnTab,$tempLines[0]);
//         array_push($returnTab,$tempLines[$foundDouble]);
//         print "Found double : " . json_encode($returnTab). "</br></br>" ;
//         print " Found double line,";
//         return $returnTab;
//     } else { 
//         return null;
//     }
// }


// /**
// * Return the first triple line found
// */
// function getTripleLines($tempLines) {

//     // print "Get Tripleline : " . json_encode($tempLines). "</br></br>" ;

//     if (count($tempLines)<3)
//         return null;
//     // print "test triple"; 
//     $returnTab=[];

//     array_push($returnTab,$tempLines[0]);
//     for ($i=1; $i<count($tempLines);++$i) {
//         if ($tempLines[0]['brou_date_adh']== $tempLines[$i]['brou_date_adh'] && ($tempLines[0]['mreg_id']== $tempLines[$i]['mreg_id'])
//         && ($tempLines[0]['ans_id']== $tempLines[$i]['ans_id'])) {
//             array_push($returnTab,$tempLines[$i]);            
//         }
//     }
//     // print "returnTab" .  json_encode($returnTab). "</br></br>";
//     if (count($returnTab)==3 ) {
//         print "Found triple line, ". count($returnTab) ."</br>"; 
//         return $returnTab;
//     } else { 
//         return null;
//     }
// }



