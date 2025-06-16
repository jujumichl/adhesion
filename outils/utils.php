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
function ecrireCsv($cheminFichier, $donnees, $entetes = [], $separateur = ',') {
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
function getAnnees() {
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

/**
 * Retourne l'année scolaire courante
 * @return int|string
 */
function getThisYear(){
    $annee = date("Y");
    $annees = $annee + 1;
    return (date("Y-m")>="$annee-09") ? $annees : $annee;
}