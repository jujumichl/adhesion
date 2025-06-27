<?php
// require "../bootstrap.php";
use Src\Controller\PersonController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$body="Request not found";

// *** Parse URL and check url validity
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (count($uri)<6) {
    header("HTTP/1.1 404 Not Found");
    echo "Url incorrecte";
exit();
}

// print json_encode($_SESSION);

$requestMethod = $_SERVER["REQUEST_METHOD"];

 require_once '../../outils/utils.php';
 require_once '../../config.php';
 $pdo = init_pdo($dbHost, $db, $dbUser, $dbMdp);

if (isset($uri[5])) {
    $domain=$uri[5];
} else {
    header("HTTP/1.1 404 Not Found");
    echo "Url incorrecte";
}

// $userId=1;

 // print json_encode($uri)."\n";
switch ($domain) {
    case 'person':
        require_once('./personController.php');
        if (isset($uri[6])) {
        require_once('../creation/creationMVC.php');
        $body=getPerson($pdo,$uri[6]);
        }
        break;

    case 'searchperson' : 
        require_once('./personController.php');
        $body=getSearchWS($pdo,$uri[6]);
        break;

    case 'searchpersonbyactivity' : 
        require_once('./personController.php');
        $body=getInscriptionPersonListToActivityWS($pdo,$uri[6]);
        break;
    
    case 'activities':
        require_once('./personController.php');
        $body=getActivitesWS($pdo);
            // require_once('./PersonController.php');

    require_once('../creation/creationMVC.php');
        // $body=json_encode($_SESSION['PersonList']);
    break;    }

// the user id is, of course, optional and must be a number:
// $userId = null;
// if (isset($uri[2])) {
//     $userId = (int) $uri[2];
// }

// // pass the request method and user ID to the PersonController and process the HTTP request:
$response['body'] = json_encode($body );

$response['status_code_header'] = 'HTTP/1.1 200 OK';
header($response['status_code_header']);
//if ($response['body']) {
   echo $response['body'];
//}
