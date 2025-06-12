<?php
require_once(__DIR__ . "\\vues\\header.html");
require_once(__DIR__ . "\\vues\\connexion.html");
require_once(__DIR__ . "\\vues\\footer.html");
require_once(__DIR__ . "\\outils\\utils.php");
$uc = lireDonneeUrl('uc');
switch ($uc) {
    case 'connexion':
        include(__DIR__ . "\\vues\\selection.html");
        break;
    default:
    break;
}