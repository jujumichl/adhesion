<?php

use Dom\Element;



/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */
function upload() {
    $target_dir = "./src/integrationCSV/fichier";

    $tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $type = array("csv");
    $csvFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    //named the file with the year, month, day and hour, minute and seconde
    $target_file = $target_dir . date('YmdHis') . '.' . $csvFileType;
    // Check if CSV file is a actual CSV or fake CSV
    if (is_uploaded_file($tmp_name) && in_array($csvFileType, $type)){
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        return $target_file;
    }
    else {
        throw new Exception("Veuillez vérifier le que vous avez bien exporter votre fichier au format CSV encoder en UTF-8.");
    }
}


/**************** MODEL ****************************** */
/* 
 * Get data in csv file, add all data which we need in our database and stores them
 */


/**
 * make all data who are in csv file into the database selected
 * @param string $cheminFichierCSV path of csv file
 * @param string $nomBdd name of database
 * @param string $nomTable name of table
 * @throws \Exception if our data aren't in our database
 * @return array the content of our database
 */
function CSVToSQL($cheminFichierCSV, $nomTable, $pdo){
    if (!file_exists($cheminFichierCSV)) {
        die("Fichier introuvable : $cheminFichierCSV");
    }

    $handle = fopen($cheminFichierCSV, 'r');
    $entetes = fgetcsv($handle, 0, ';');
    $separateur = ';';

    // Réessaie avec ',' si mauvais format
    if (!$entetes || count($entetes) < 3) {
        fclose($handle);
        $handle = fopen($cheminFichierCSV, 'r');
        $entetes = fgetcsv($handle, 0, ',');
        $separateur = ',';
    }

    // Nettoyage des entêtes
    $entetes = array_map(function ($col) {
        return trim(preg_replace('/^\xEF\xBB\xBF/', '', $col));
    }, $entetes);

    // Localisation des colonnes clés
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

    //Treatment of data
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
        return $result;
    } else {
        throw new Exception("Erreur lors du passage de données, veuillez vérifier le fichier ainsi que le nom des colonnes de ce fichier.");
    }
}





/**
 * store data
 * @param mixed $pdo pdo connexion
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
    $sql = "select brou_email, count(*) AS tot from brouillon group by brou_email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($result as $res){
        try{
            // check email
            if (filter_var($res['brou_email'], FILTER_VALIDATE_EMAIL)) {
                $sql = "select * from brouillon where brou_email = :mail";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':mail' => $res['brou_email']
                ]);
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                //$res = Array ( [brou_email] => - [tot] => 85 )
                $per_id = createPers($data[0], $pdo);

                if ($res['tot'] === 1){
                    if ($data[0]['brou_adh']>0){
                        $data[0]['codeADH'] = 'AUT01';
                        createSubscription($data[0],$per_id, $pdo);
                    }
                    if ($data[0]['brou_code'] !== ''){
                        createAct($data[0],$per_id, $pdo);
                    }

                }
                else {
                    multipleLignesComput($data);
                }
            }
            else {
                throw new Exception("Email invalide " . $res['brou_email']);
            }
        }catch (Exception $e){
            $message .= "Erreur : " . $e->getMessage() . "</br>";
        }
    }
    return $message;
}

/**
 * use to create only one people
 * @param mixed $result data of people
 * @param mixed $pdo pdo connexion
 * @return mixed 
 */
function createPers($data, $pdo){
 /////////////////////////////////////////////check people and create people if not exists/////////////////////////////////////////////
    if ($data['brou_titre'] === 'Madame'){
        $data['brou_titre'] = 2;
    }
    else if ($data['brou_titre'] === 'Monsieur'){
        $data['brou_titre'] = 1;
    }
    //$data = just array of data cf $result
    $sql = "INSERT IGNORE INTO `personnes`(
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
        ':naiss'=>$data['brou_date_naiss']
    ]);
    $per_id = $pdo->lastInsertId();
    return $per_id;
}

/**
 * create one subscription
 * @param array $data data of createPers
 * @param mixed $pdo PDO connexion
 * @return void
 */
function createSubscription($data, $per_id, $pdo){
    $act_id = getIDActivity($data['codeADH'], $pdo);
    $reg_id = createPayment(
        $data['brou_adh'],
        $data['brou_act'],
        $data['brou_date_adh'],
        $data['brou_reglement'],
        $per_id,
        $pdo);
    $sql = 'INSERT INTO `inscriptions`(
        per_id,
        act_id,
        ins_date_inscription,
        id_reg,
        ins_debut,
        ins_fin,
        ins_montant
        )VALUES(
        :per_id,
        :act_id,
        :ins_date_inscription,
        :id_reg,
        :ins_debut,
        :ins_fin,
        :ins_montant
    )
    ';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':per_id' => $per_id,
        ':act_id' => $act_id,
        ':ins_date_inscription' => $data['brou_date_adh'],
        ':id_reg' => (int)$reg_id,
        ':ins_debut' => $data['brou_date_adh'],
        ////////////////////////////////////A REVOIR/////////////////////////////////////////////
        ':ins_fin' => '31/08' . date('/Y'),
        ////////////////////////////////////////////////////////////////////////////////////////
        ':ins_montant' => $data['brou_adh']
    ]);
}

/**
 * Create one activity
 * @param mixed $data data of createPers
 * @param mixed $pdo PDO connexion
 * @return void
 */
function createAct($data, $per_id, $pdo){
    $act_id = getIDActivity($data['brou_code'], $pdo);
    $reg_id = getamout(
        $per_id,
        $data['brou_adh'],
        $data['brou_act'],
        $data['brou_date_adh'],
        $data['brou_reglement'],
        $pdo
        );

    $sql = 'INSERT INTO `inscriptions`(
    per_id,
    act_id,
    ins_date_inscription,
    id_reg,
    ins_debut,
    ins_fin,
    ins_montant
    )VALUES(
    :per_id,
    :act_id,
    :ins_date_inscription,
    :id_reg,
    :ins_debut,
    :ins_fin,
    :ins_montant
    )
    ';
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':per_id' => (int)$per_id,
        ':act_id' => (int)$act_id,
        ':ins_date_inscription' => $data['brou_date_adh'],
        ':id_reg' => (int)$reg_id,
        ':ins_debut' => $data['brou_date_adh'],
        ':ins_fin' => '31/08' . date('/Y'),
        ':ins_montant' => $data['brou_act']
    ]);
}

function getIDActivity($code, $pdo){
    //Get id of activity
    $sql = 'select act_id from activites where act_ext_key = :code';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code'=> $code
        ]);
    $act_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $act_id[0]['act_id'];
}

function getIdPerson($email, $pdo){
//Get id of person
    $sql = 'select per_id from personnes where per_nom = :email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email'=> $email
        ]);
    $per_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $per_id[0]['per_id'];
}




function createPayment($montant_adh, $montant_act, $dateAdh, $mreg, $per_id, $pdo){
    $sql = 'select mreg_id from modereglement where mreg_code = :mreg';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ ':mreg' => $mreg]);
    $mregID = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $total = $montant_act + $montant_adh;
    if (!$mregID[0]['mreg_id']) {
        return "Modèle de règlement non connu ou invalide." . $per_id;
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


function getamout($per_id, $montant_adh, $montant_act, $dateAdh, $mreg, $pdo){
    $sql = 'select id_reg from inscriptions where per_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $per_id]);
    $amoutid = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if (!empty($amoutid['id_reg'])){
        return $amoutid['id_reg'];
    }
    else{
        return createPayment(
            $montant_adh, 
            $montant_act, 
            $dateAdh, 
            $mreg, 
            $per_id, 
            $pdo);
    }
}

function multipleLignesComput($Table){
    return;
}