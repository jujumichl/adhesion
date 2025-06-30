<?php

/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */

 function launchIntegration ($pdo) {
        
    $csvPath = uploadCSV();
    $filename = $_FILES["fileToUpload"]["name"];
    $result = CSVToSQL($csvPath,  'brouillon', $pdo);

    $validity = checkBrouillonValidity($pdo);
    
    if ($validity!="") {
        print "Input lile validity :".$validity." </br>";
        throw new Exception("Le fichier proposé n'est pas valide pour une intégration, merci de corriger les erreurs");
    } else {
        print "Input lile validity : file verified and ok.</br>";
    }
    // *** Load data if validity ok
    $msgErr='';
    $msgErr = parseAndStoreData($pdo);
    if ($msgErr=='')
        print "Résultat de l'intégration : Pas d'erreur." ;
    else
        print "Résultat de l'intégration : " .$msgErr;
}

/**
 * @return : true if vallidity is OK
 */
function checkBrouillonValidity($pdo){

    $message='';
     $globalValidity=true;
    // $message.= "</br>";

    // *** Check same emails with differents names 
    $sql = "SELECT  brou_email, COUNT(*) from
        (SELECT brou_email,brou_nom,brou_prenom  FROM brouillon
        GROUP by brou_email,brou_nom,brou_prenom
        ORDER BY brou_email) AS subq
        GROUP BY brou_email
        having COUNT(*) >1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data ) > 0) {
            $globalValidity=false;
            $message.= "************************ ";
            $message.= "Même email pour 2 personnes (nom, prenom) : " .count($data) ."</br>";
           foreach ($data as $line) {
            $message.= $line['brou_email'] ."</br>";
           }
        }

          // *** Check same emails with differents names 
    $sql = "SELECT brou_email,brou_nom,brou_prenom , brou_annee, brou_code, COUNT(*) FROM brouillon
        GROUP by brou_email,brou_nom,brou_prenom, brou_annee, brou_code
        HAVING COUNT(*) >1
        ORDER BY brou_email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if (count($data ) > 0) {
        $globalValidity=false;
        $message.= "</br>************************ ";
        $message.= "Même activité, la même année pour un email  : " .count($data) ."</br>";
       foreach ($data as $line) {
            $message.= $line['brou_email'] ."</br>";
       }
    }

    // *** Check  emails validity
    $sql = "SELECT brou_email, brou_nom,brou_prenom FROM brouillon";
     $stmt = $pdo->prepare($sql);
     $stmt->execute();
     $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
     $messageEmail="";
     if (count($data ) > 0) {
         foreach ($data as $line) {
            if (!filter_var($line['brou_email'], FILTER_VALIDATE_EMAIL)) {
                $globalValidity=false;       
                $messageEmail.= $line['brou_email'] . "Proposition :  ". $line['brou_nom']. "." . $line['brou_prenom']  ."@inconnu.fr</br>";
            }
        }
        
    if ($messageEmail!="") {
        $message."</br>************************ ";
        $message."Email invalides  : </br>". $messageEmail;
    }
        // } else {
        //     // print "</br></br>************************";
        //     // print "Email valides  : " .count($data) ."</br>";
        // }
    }

   //  if ($globalValidity==true)
   //     return "";
   // else

    $sql = "select * from brouillon 
    LEFT JOIN modereglement ON modereglement.mreg_code = brouillon.brou_reglement 
    LEFT JOIN activites ON activites.act_ext_key = brouillon.brou_code 
    LEFT JOIN an_exercice ON an_exercice.ans_libelle = brouillon.brou_annee";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // print "</br>*** line : " . $res['brou_email'] . " : ";
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $datenaiss = stringToDate(
    substr($data[0]['brou_date_naiss'], 0,-8),
    substr($data[0]['brou_date_naiss'], 3, -5),
    substr($data[0]['brou_date_naiss'], -4));
    // *** Create the person and get back the ID of person this person
    $per_id = createPers($data[0], $datenaiss, $pdo);

    // *** Check date and mreg validity
    foreach ($data as $line){
        $dateAdh = stringToDate(
        substr($line['brou_date_adh'], 0,-8),
        substr($line['brou_date_adh'], 3, -5),
        substr($line['brou_date_adh'], -4));

                // Check date
        if (is_null($line['act_libelle'])){
            $message."***** Date d'adhésion invalide " . $line['brou_email'] . '<br>';
        }
           
        // Check date
        if (strtotime($dateAdh) === false){
         $message."***** Date d'adhésion invalide " . $line['brou_email'] . '<br>';
        }

        // check mreg 
        if (is_null($line['mreg_id'])){
            $message."***** Mode de règlement invalide " . $line['brou_email'] . '<br>';
        }
        if (is_null($line['mreg_Libelle'])){
            $message."***** Mode de règlement inconnu " . $line['brou_email'] . '<br>';
        }

        if (is_null($line['brou_nom']) || empty($line['brou_nom']) || $line['brou_nom']==='') {
            $message."***** Nom incorrect " . $line['brou_email'] . '<br>';
        }
        // if (isnull($line['mreg_prenom']) || empty($line['mreg_prenom']) || $line['mreg_prenom']==='') {
        //     $message."***** Nom incorrect " . $line['brou_email'] . '<br>';
        // }

        //check act code
        if (is_null($line['act_ext_key'])) {
            $message."***** Code d'activité invalide " . $line['brou_email'] . '<br>';
        }

        if (is_null($line['ans_id'])){
            $message."***** saison invalide " . $line['brou_email'] . '<br>';
        }
    }


    // *** End checkBrouillonValidity
    return $message;
}

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
    $sql = "DROP TABLE IF EXISTS `brouillon`;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $sql = "CREATE TABLE IF NOT EXISTS `brouillon`(
        `brou_id` int NOT NULL AUTO_INCREMENT,
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
        `brou_telephone` char(10), 
         UNIQUE KEY `brou_id` (`brou_id`)
    );";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    
    $sql = "DROP TABLE IF EXISTS `integrationerrors`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $sql = "CREATE TABLE IF NOT EXISTS `integrationerrors` (
        `interr` int NOT NULL AUTO_INCREMENT,
        `textError` varchar(255) DEFAULT NULL,
        `linedata` TEXT DEFAULT NULL,
         UNIQUE KEY `interr` (`interr`)
        )  CHARSET=utf8 COLLATE=utf8_general_ci;
        ;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Data processing
    $nbLine=0;
    while (($ligne = fgetcsv($handle, 0, $separateur)) !== false) {
        $data = array_combine($entetes, $ligne);

        if ($nbLine==0) {
            $currentYear = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$annKey] ?? ''));
            print "année en cours" . $currentYear;
        }
        ++$nbLine;

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
        
        if ($annee== $currentYear) {
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

