<?php

use Dom\Element;



/**************** CONTROLLER ****************************** */
/**
 * Controller for the Selection page
 */
function upload() {
    $target_dir = __DIR__ . "/fichier/";

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
 * Function for initialize PDO
 * @param mixed $host (ex: localhost)
 * @param mixed $db name of database
 * @param mixed $user user who we want connect
 * @param mixed $pass the password of this user
 * @return PDO connexion to the database
 */
function init_pdo($host, $db, $user, $pass) {
    $port = "3306";
    $charset = 'utf8mb4';

    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    $pdo = new \PDO($dsn, $user, $pass, $options);
    return $pdo;
}

/**
 * make all data who are in csv file into the database selected
 * @param string $cheminFichierCSV path of csv file
 * @param string $nomBdd name of database
 * @param string $nomTable name of table
 * @throws \Exception if our data aren't in our database
 * @return array the content of our database
 */
function CSVToSQL($cheminFichierCSV, $nomBdd, $nomTable){
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
        die("Colonne courriel introuvable");
    }
    $pdo = init_pdo('localhost', $nomBdd, 'root', '');
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
    while (($ligne = fgetcsv($handle, 0, $separateur)) !== false) {
        
        $data = array_combine($entetes, $ligne);
        $nom = trim(preg_replace( '/^\xEF\xBB\xBF/', '', $data[$nomKey] ?? ''));
        $prenom = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$prenomKey] ?? ''));
        $port = isset($data[$portKey]) ? preg_replace('/\D/', '', $data[$portKey]) : '';
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
        $tel = isset($data[$telKey]) ? preg_replace('/\D/', '', $data[$telKey]) : '';
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
 * @return void
 */
function storeData($pdo){
    $sql = "select brou_email, count(*) AS tot from brouillon group by brou_email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($result as $res){
        //$res = Array ( [brou_email] => - [tot] => 85 )
        if ($res['tot'] === 1){
            createPers($res['brou_email'], $pdo);
        }
        
    }
}

/**
 * use to create only one people
 * @param mixed $mail email of people
 * @param mixed $pdo pdo connexion
 * @return void
 */
function createPers($mail, $pdo){
    // check email
    if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $sql = "select * from brouillon where brou_email = :mail";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':mail' => $mail
        ]);
        // result = Array (
        // [0] => Array ( 
        // [brou_id] => ...............................................
        // [brou_nom] => ...............................................
        // [brou_prenom] => ...............................................
        // [brou_portable] => ...............................................
        // [brou_email] => ...............................................
        // [brou_commune] => ...............................................
        // [brou_adh] => ...............................................
        // [brou_act] => ...............................................
        // [brou_reglement] => ...............................................
        // [brou_code] => ...............................................
        // [brou_CP] => ...............................................
        // [brou_annee] => ...............................................
        // [brou_date_adh] => ...............................................
        // [brou_date_naiss] => ...............................................
        // [brou_titre] => ...............................................
        // [brou_telephone] => ..............................................
        //) )
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $data){
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
            if ($data['brou_adh']>0){
                createAdh($data, $pdo);
                continue;
            }
            if ($data['brou_code'] !== ''){
                createAct($data, $pdo);
                continue;
            }
        }
    }
}

/**
 * create one adhesion
 * @param array $data data of createPers
 * @param mixed $pdo PDO connexion
 * @return void
 */
function createAdh($data, $pdo){
    //Get id people
    $sql = 'select per_id from personnes where per_nom = :nom AND per_prenom = :prenom';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'=> $data['brou_nom'],
        ':prenom'=> $data['brou_prenom']
        ]);
    $per_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //Get id of activity
    $sql = 'select act_id from activites where act_ext_key = :code';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code'=> $data['brou_code']
        ]);
    $act_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    /* A voir plus tard, il faut d'abord enregistrer le paiement puis récupérer son identifiant
    //Get id of payments (reg_montant = total -- id_reg = sous-total ?, payment details)
    $sql = 'select reg_id from reglements where act_ext_key = :code';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code'=> $data['brou_code']
        ]);
    $reg_id = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    */
    foreach ($per_id as $per){
        foreach ($act_id as $act){
            //foreach ($reg_id as $reg){
                //$per = Array ( [per_id] => 1 )
                //$act = Array ( [act_id] => 138)
                //$reg = Array ( [reg_id] => 1)
                $sql = 'INSERT IGNORE INTO `inscriptions`(
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
                ';
                $stmt = $pdo->prepare($sql);
                
                $stmt->execute([
                    ':per_id' => $per['per_id'],
                    ':act_id' => $act['act_id'],
                    ':ins_date_inscription' => $data['brou_date_adh'],
                    //id_reg a récupérer
                    ':id_reg' => $reg['brou_date_adh'],
                    ':ins_debut' => $data['brou_date_adh'],
                    ':ins_fin' => '31/08' . date('/Y'),
                    ':ins_montant' => $data['brou_date_adh']
                ]);
            //}
        }
    }
}

/**
 * Create one activity
 * @param mixed $data data of createPers
 * @param mixed $pdo PDO connexion
 * @return void
 */
function createAct($data, $pdo){
    return;
}