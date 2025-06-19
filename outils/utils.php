<?php
/** 
 * Fournit la valeur d'une donnée transmise par la méthode get (url).                    
 * 
 * Retourne la valeur de la donnée portant le nom $nomDonnee reçue dans l'url, 
 * $valDefaut si aucune donnée de nom $nomDonnee dans l'url 
 * @param string nom de la donnée
 * @param string valeur par défaut 
 * @return string valeur de la donnée
 */ 
function lireDonneeUrl($nomDonnee, $valDefaut="") {
    if ( isset($_GET[$nomDonnee]) ) {
        $val = $_GET[$nomDonnee];
    }
    else {
        $val = $valDefaut;
    }
    return $val;
}

/**
 * Permet d'écrire un fichier CSV
 * @param string $cheminFichier
 * @param array|string $donnees
 * @param list $entetes
 * @param string $separateur
 * @return void
 */
function writeTableTemp($cheminFichier, $donnees, $entetes = [], $separateur = ',') {
    $dir = dirname($cheminFichier);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $f = fopen($cheminFichier, 'w');
    if ($f === false) {
        die("Impossible d'ouvrir le fichier pour écriture : $cheminFichier");
    }
    if ($entetes) {
        fputcsv($f, $entetes, $separateur);
    }
    foreach ($donnees as $ligne) {
        fputcsv($f, $ligne, $separateur);
    }
    fclose($f);
}

/**
 * Récupère l'année en cours et l'année suivante
 *
 * @return string Une chaine de caractère sous la forme "$anneeCourante-$anneeSuivante"
 */
function getCurrentSeason() {
    $date = date("Y-m");
    $anneeCourante = date("Y");
    if ($date >= "$anneeCourante-09"){
        $anneeSuivante = $anneeCourante + 1;
        return "$anneeCourante-$anneeSuivante";
    }
    else{
        $anneeprecedente = $anneeCourante - 1;
        return "$anneeprecedente-$anneeCourante";
    }    
}


/************************************ TO DO *********************************************** */
/**
 * get in table an_exercice ans_date_fin
 */
function getEndOfSeasonDate($date, $endDay ="31/08"){
    return;

}


/**
 * Summary of formatPhoneNumber
 * @param mixed $number
 */
function formatPhoneNumber($number) {
    // Supprimer tout sauf les chiffres
    $digits = preg_replace('/\D/', '', $number);

    // Si le numéro commence par 0 et fait 10 chiffres (ex: 0601020304)
    if (strlen($digits) === 10 && $digits[0] === '0') {
        return '+33' . substr($digits, 1);
    }

    // Si déjà en format international
    if (preg_match('/^\+33\d{9}$/', $number)) {
        return $number;
    }

    // Sinon retour vide (mauvais format)
    return '';
}

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