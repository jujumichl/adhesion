<?php
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


/*
 * En cas de doublon d'email, concatène les noms/prénoms associés sauf si email = '-'.
 */
function concatNomPrenomDoublons($data, $emailKey, $nomKey, $prenomKey) {
    $groupes = [];

    foreach ($data as $ligne) {
        $email = trim(preg_replace('/^\xEF\xBB\xBF/', '', $ligne[$emailKey]));

        if (!isset($groupes[$email])) {
            $groupes[$email] = [];
        }

        $groupes[$email][] = $ligne;
    }

    $resultat = [];

    foreach ($groupes as $email => $group) {
        if ($email === '-') {
            foreach ($group as $ligne) {
                $resultat[] = $ligne;
            }
        } elseif (count($group) > 1) {
            $noms = [];
            $prenoms = [];

            foreach ($group as $ligne) {
                $noms[] = trim($ligne[$nomKey]);
                $prenoms[] = trim($ligne[$prenomKey]);
            }

            $fusion = $group[0];
            $fusion[$nomKey] = implode(' / ', array_unique($noms));
            $fusion[$prenomKey] = implode(' / ', array_unique($prenoms));
            $resultat[] = $fusion;
        } else {
            $resultat[] = $group[0];
        }
    }
    return $resultat;
}


/**
 * Valide et trie les contacts d'un fichier CSV en séparant les valides et les invalides.
 * 
 * @param string $fichierOriginal Chemin du fichier CSV source.
 * @param string $fichierValide Chemin du fichier où seront enregistrés les contacts valides.
 * @param string $fichierInvalide Chemin du fichier où seront enregistrés les contacts invalides.
 */
function verifierContactEtClasser($fichierOriginal, $fichierValide, $fichierInvalide) {
    if (!file_exists($fichierOriginal)) {
        die("Fichier introuvable : $fichierOriginal");
    }

    $handle = fopen($fichierOriginal, 'r');
    $entetes = fgetcsv($handle, 0, ';');
    $separateur = ';';

    // Réessaie avec ',' si mauvais format
    if (!$entetes || count($entetes) < 3) {
        fclose($handle);
        $handle = fopen($fichierOriginal, 'r');
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
   
    foreach ($entetes as $col) {
        $colUpper = strtoupper(trim($col));
        if (in_array($colUpper, ['COURRIEL'])) $emailKey = $col;
        if (in_array($colUpper, ['PORTABLE'])) $telKey = $col;  
    }
    
    if (!$emailKey) {
        die("Colonne courriel introuvable");
    }

    // Initialisation
    $valideData = [];
    $invalideData = [];
    $doublons = [];
    $date = getAnnees();
    $emailsMap = []; // Associe chaque email à ses lignes
    $ColonnesSouhaiter = ['Nom', 'Prénom', 'Portable', 'Courriel', 'Commune', 'Montant ADH', 'Montant ACT', 'Règlement', 'Code', 'Code postal', 'Année'];
    while (($ligne = fgetcsv($handle, 0, $separateur)) !== false) {
        if (count($ligne) != count($entetes)) {
            $invalideData[] = array_merge($ligne, ['Ligne mal formée']);
            continue;
        }
        
        $data = array_combine($entetes, $ligne);
        $email = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[$emailKey] ?? ''));
        $tel = isset($data[$telKey]) ? preg_replace('/\D/', '', $data[$telKey]) : '';
        $erreurs = [];
        
        // Vérification email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Vérification téléphone si présent
            if ($tel !== '' && !preg_match('/^(?:\+33|0033|0)[1-9]\d{8}$/', $tel)) {
                $erreurs[] = "Telephone invalide";
                $donneesFiltrees = array_intersect_key($data, array_flip($ColonnesSouhaiter));
                $invalideData[] = array_merge($donneesFiltrees, [implode(', ', $erreurs)]);
            }

            // Ajoute aux doublons l'email
            if (!isset($emailsMap[$email])) {
                $emailsMap[$email] = [];
            }
            $emailsMap[$email][] = $data;
        }
        else {
            $erreurs[] = "Email invalide";
            $donneesFiltrees = array_intersect_key($data, array_flip($ColonnesSouhaiter));
            $invalideData[] = array_merge($donneesFiltrees, [implode(', ', $erreurs)]);
        }
    }
    

    fclose($handle);

    // Analyse des emails groupés
    foreach ($emailsMap as $email => $group) {
        if (count($group) > 1 && $email !== '-') {
            foreach ($group as $ligne) {
                $doublons[] = $ligne;
            }
        } else {
            $valideData[] = array_values($group[0]); // format ligne simple
        }
    }

    // Fusionne les doublons avec concatNomPrenomDoublons()
    if (!empty($doublons)) {
        $fusionnes = concatNomPrenomDoublons($doublons, $emailKey, 'Nom', 'Prénom');
        foreach ($fusionnes as $ligne) {
            $valideData[] = array_map(function ($col) {
                return is_string($col) ? $col : '';
            }, array_values($ligne)); // ligne formatée
        }
    }

    // Écrit les fichiers
    $entetesInvalide = ['Nom', 'Prenom', 'Telephone', 'Courriel', 'Code d\'erreurs'];
    writeTableTemp($fichierValide, $valideData, $entetes, ',');
    writeTableTemp($fichierInvalide, $invalideData, $entetesInvalide, ';');
    return true;
}