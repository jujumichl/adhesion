<?php

/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */

/**
 * Use to upload CSV file in folder "fichier", the file was named like this 202506264150636.csv
 * @throws \Exception if the file aren't in the right format (here it's CSV format)
 * @return string return the path for access to the file
 */
function uploadCSV() {
    $target_dir = "./src/integrationCSV/fichier";

    $tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $type = array("csv");
    $csvFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    //named the file with the year, month, day and hour, minute and seconde
    $target_file = $target_dir . date('YmdHis') . '.' . $csvFileType;
    // *** Check if CSV file is a actual CSV or fake CSV
    if (is_uploaded_file($tmp_name) && in_array($csvFileType, $type)){
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        return $target_file;
    }
    else {
        throw new Exception("Veuillez vérifier le que vous avez bien exporter votre fichier au format CSV encoder en UTF-8.");
    }
}
$csvPath = uploadCSV();
$filename = $_FILES["fileToUpload"]["name"];
$result = CSVToSQL($csvPath,  'brouillon', $pdo);
$msgErr = parseAndStoreData($pdo);

/**************** MODEL ****************************** */
/* 
 * get back data in csv file, add all data which we need in our database and stores them in our database
 */


/**
 * make all data who are in csv file into the database selected
 * @param string $cheminFichierCSV path of csv file
 * @param string $nomBdd name of database
 * @param string $nomTable name of table
 * @throws \Exception if your column wasn't named with the right name
 * @return array the content of our database
 */
function CSVToSQL($cheminFichierCSV, $nomTable, $pdo){
    if (!file_exists($cheminFichierCSV)) {
        die("Fichier introuvable : $cheminFichierCSV");
    }

    $handle = fopen($cheminFichierCSV, 'r');
    $entetes = fgetcsv($handle, 0, ';');
    $separateur = ';';

    // Try again with ',' if bad format
    if (!$entetes || count($entetes) < 3) {
        fclose($handle);
        $handle = fopen($cheminFichierCSV, 'r');
        $entetes = fgetcsv($handle, 0, ',');
        $separateur = ',';
    }

    // Cleaning headers
    $entetes = array_map(function ($col) {
        return trim(preg_replace('/^\xEF\xBB\xBF/', '', $col));
    }, $entetes);

    // Location of key columns
    $emailKey = null;
    $telKey = null;
    $portKey = null;
    $nomKey = null;
    $prenomKey = null;
    $commKey = null;
    $adhKey = null;
    $actKey = null;
    $regKey = null;
    $codeKey = null;
    $CPKey = null;
    $annKey = null;
    $DadhKey = null;
    $naissKey = null;
    $titreKey = null;

    foreach ($entetes as $col) {
        $colUpper = strtoupper(trim($col));
        if (in_array($colUpper, ['NOM'])) $nomKey = $col;
        if (in_array($colUpper, ['PRéNOM'])) $prenomKey = $col;
        if (in_array($colUpper, ['PORTABLE'])) $portKey = $col;
        if (in_array($colUpper, ['COURRIEL'])) $emailKey = $col;
        if (in_array($colUpper, ['COMMUNE'])) $commKey = $col;
        if (in_array($colUpper, ['MONTANT ADH'])) $adhKey = $col;
        if (in_array($colUpper, ['MONTANT ACT'])) $actKey = $col;
        if (in_array($colUpper, ['RèGLEMENT'])) $regKey = $col;
        if (in_array($colUpper, ['CODE'])) $codeKey = $col;
        if (in_array($colUpper, ['CODE POSTAL'])) $CPKey = $col;
        if (in_array($colUpper, ['ANNéE'])) $annKey = $col;
        if (in_array($colUpper, ['DATE D\'ADHéSION'])) $DadhKey = $col;
        if (in_array($colUpper, ['DATE DE NAISSANCE'])) $naissKey = $col;
        if (in_array($colUpper, ['TITRE'])) $titreKey = $col;
        if (in_array($colUpper, ['TéLéPHONE'])) $telKey = $col;

    }
    
    if (!$emailKey) {
        throw new Exception("Colonne courriel introuvable");
    }
    //Drop and create table brouillon
    $sql = "drop table brouillon";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $sql = "CREATE TABLE IF NOT EXISTS `brouillon`(
        `brou_id` int,
        `brou_nom` varchar(255),
        `brou_prenom` varchar(255),
        `brou_portable` char(10),
        `brou_email` varchar(255),
        `brou_commune` varchar(255),
        `brou_adh` float,
        `brou_act` float,
        `brou_reglement` char(3),
        `brou_code` varchar(255),
        `brou_CP` varchar(255),
        `brou_annee` char(9),
        `brou_date_adh` char(10),
        `brou_date_naiss`char(10),
        `brou_titre` varchar(255),
        `brou_telephone` char(10)
    );";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Data processing
    while (($ligne = fgetcsv($handle, 0, $separateur)) !== false) {
        
        $data = array_combine($entetes, $ligne);
        $nom = trim(preg_replace( '/^\xEF\xBB\xBF/', '', $data[$nomKey] ?? ''));
        $prenom = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$prenomKey] ?? ''));
        $port = formatPhoneNumber($data[$portKey]);
        $email = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$emailKey] ?? ''));
        $commune = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$commKey] ?? ''));
        $adh = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$adhKey] ?? ''));
        $act = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$actKey] ?? ''));
        $reg = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$regKey] ?? ''));
        $code = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$codeKey] ?? ''));
        $CP = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$CPKey] ?? ''));
        $annee = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$annKey] ?? ''));
        $Dadh = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$DadhKey] ?? ''));
        $naiss = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$naissKey] ?? ''));
        $titre = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$titreKey] ?? ''));
        $tel = formatPhoneNumber($data[$telKey]);
        
        if ($nom !== ''){
            $sql = "INSERT INTO $nomTable(
            brou_nom,
            brou_prenom,
            brou_portable,
            brou_email,
            brou_commune,
            brou_adh,
            brou_act,
            brou_reglement,
            brou_code,
            brou_CP,
            brou_annee,
            brou_date_adh,
            brou_date_naiss,
            brou_titre,
            brou_telephone
            ) Values (
            :nom,
            :prenom,
            :portable,
            :email,
            :commune,
            :adh,
            :act,
            :reglement,
            :code,
            :cp,
            :annee,
            :date_adh,
            :date_naiss,
            :titre,
            :telephone)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':portable' => $port,
                ':email' => $email,
                ':commune' => $commune,
                ':adh' => $adh,
                ':act' => $act,
                ':reglement' => $reg,
                ':code' => $code,
                ':cp' => $CP,
                ':annee' => $annee,
                ':date_adh' => $Dadh,
                ':date_naiss' => $naiss,
                ':titre' => $titre,
                ':telephone' => $tel
            ]);
        }
    }
    fclose($handle);
    // Check if it's an sucess
    if ($stmt->rowCount() > 0) {
        $sql = "select * from brouillon";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        unlink($cheminFichierCSV);
        return $result;
    } else {
        throw new Exception("Erreur lors du passage de données, veuillez vérifier le fichier ainsi que le nom des colonnes de ce fichier.");
    }
}





/**
 * store and parse data in our database
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
function parseAndStoreData($pdo){
    (string)$message = "";
    $data = null;
    //$count=0;
    //$count1=0;
    $sql = "select brou_email, count(*) AS tot from brouillon group by brou_email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($result as $res){
        // $res = Array ( [brou_email] => - [tot] => 17 )
        try{
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////// MAIN FUNCTION WHO MAKES DECISIONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // check email
            if (filter_var($res['brou_email'], FILTER_VALIDATE_EMAIL)) {
                // *** Get the lines of one person
                //SQL request who takes all line of one person who is identify by her email
                $sql = "select * from brouillon 
                        LEFT JOIN modereglement ON modereglement.mreg_code = brouillon.brou_reglement 
                        LEFT JOIN activites ON activites.act_ext_key = brouillon.brou_code 
                        LEFT JOIN an_exercice ON an_exercice.ans_libelle = brouillon.brou_annee
                        where brouillon.brou_email = :mail;";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':mail' => $res['brou_email']
                ]);
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $dateAdh = stringToDate(
                    substr($data[0]['brou_date_adh'], 0,-8),
                    substr($data[0]['brou_date_adh'], 3, -5),
                    substr($data[0]['brou_date_adh'], -4));
                $datenaiss = stringToDate(
                    substr($data[0]['brou_date_naiss'], 0,-8),
                    substr($data[0]['brou_date_naiss'], 3, -5),
                    substr($data[0]['brou_date_naiss'], -4));
                // *** Create the person and get back the ID of person this person
                $per_id = createPers($data[0], $datenaiss, $pdo);
                
                // *** Check date and mreg validity
                foreach ($data as $line){

                    // Check date
                    if (strtotime($dateAdh) === false){
                        throw new Exception("Date d'adhésion invalide " . $line['brou_email'] . '<br>');
                    }
                    

                    // check mreg 
                    if (is_null($line['mreg_id'])){
                        throw new Exception("Mode de règlement invalide " . $line['brou_email'] . '<br>');
                    }
                    

                    //check act code
                    if (is_null($line['act_ext_key'])){
                        throw new Exception("Code d'activité invalide " . $line['brou_email'] . '<br>');
                    }

                    if (is_null($line['ans_id'])){
                        throw new Exception("Libellé d'année invalide " . $line['brou_email'] . '<br>');

                    }
                }
                
                // *** Choose treatment of according to the numbre of line
                switch ($res['tot']){
                    // print "La date et le mode de règlement sont correctes";
                    // print "Vérification du nombre de lignes...";
                    // print "Il y a " . $res['tot'] . " de ligne(s)";
                    case 1:
                        $message .= "La ligne traitée car " . $res["tot"] . ' ' . $res['brou_email'] .'<br>';
                        $reg_id = getamount(
                            $per_id,
                            $data[0]['brou_adh'],
                            $data[0]['brou_act'],
                            $data[0]['brou_date_adh'],
                            $data[0]['mreg_code'],
                            $pdo);
                        // print "Il n'y a qu'une seule ligne";
                        if ($data[0]['brou_code'] !== ''){
                            createAct($data[0],$per_id, $reg_id, $dateAdh, $pdo);
                            // $message .= "Création d'une activitées...";
                        }
                        if ($data[0]['brou_adh']>0){
                            $data[0]['codeADH'] = 'AUT01';
                            createSubscription($data[0],$per_id, $reg_id, $dateAdh, $pdo);
                            // $message .="Création d'une adhésion pour " . $data[0]['brou_nom'] . " " . $data[0]["brou_prenom"] . " identifier " . $per_id;
                        }
                        break;
                    case 2:
                        $message .= "La ligne non traitée car " . $res["tot"] . ' ' . $res['brou_email'] .'<br>';
                        break;
                    default:
                        $message .= "La ligne non traitée car " . $res["tot"] . ' ' . $res['brou_email'] .'<br>';
                }
                    
                    
                
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////// MAIN FUNCTION WHO MAKES DECISIONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            else {
                throw new Exception("Email invalide " . $res['brou_email']);
            }
        }catch (Exception $e){
            if ($data !== null){
                $message .= "Erreur : " . $e->getMessage() . ' -- ' . $data[0]['brou_email']."</br>";
            }
            else {
                $message .= "Erreur : " . $e->getMessage() ."</br>";
            }
        }
    }
    //echo $count . "/" . $count1;
    return $message;
}

/**
 * use to create only one people
 * @param mixed $result data of people
 * @param mixed $pdo pdo login
 * @return mixed 
 */
function createPers($data,$date_naiss, $pdo){
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
}

/**
 * Create one activity
 * @param mixed $data data of createPers
 * @param mixed $pdo PDO login
 */
function createAct($data, $per_id, $reg_id, $dateAdh, $pdo){
    $act_id = getIDActivity($data['brou_code'], $pdo);
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
function createPayment($montant_adh, $montant_act, $dateAdh, $mreg, $per_id, $pdo){
    $sql = 'select mreg_id from modereglement where mreg_code = :mreg';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ ':mreg' => $mreg]);
    $mregID = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $total = (int)$montant_act + (int)$montant_adh;
    if (!$mregID[0]['mreg_id']) {
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

        $sql = 'UPDATE inscriptions
        SET reg_id = :reg_id
        WHERE per_id = :per_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':reg_id' => $reg_id,
            ':per_id' => $per_id
        ]);
        return $reg_id;
    }
}



/**
 * get back reg_id and check if he is already inside our database or not, add it if not in
 * @param mixed $per_id id of one person
 * @param mixed $montant_adh amount of subscription
 * @param mixed $montant_act amount of activity
 * @param mixed $dateAdh the date of payment
 * @param mixed $mreg method of payment
 * @param mixed $pdo pdo login
 * @return int return reg_id of the payment
 */
function getamount($per_id, $montant_adh, $montant_act, $dateAdh, $mreg, $pdo){
    $sql = 'select reg_id from inscriptions where per_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $per_id]);
    $amoutid = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // If $amoutid is empty, it means no registration was found, so create a new payment.
    if (empty($amoutid)){
        return createPayment(
            $montant_adh,
            $montant_act,
            $dateAdh,
            $mreg,
            $per_id,
            $pdo
        );
    }
    // If $amoutid is NOT empty, it means a registration was found, so return its reg_id.
    else {
        //print json_encode($amoutid) . '<br>';
        return $amoutid[0]['reg_id'];
    }
}

/**
 * use to process multiple lines
 * @param mixed $person data on one person
 * @param mixed $pdo pdo login
 * @return void
 * 
 */
function multipleLignesComput($person, $per_id, $reg_id, $pdo){
    if (count($person) === 2){

    }
}








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