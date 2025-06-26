<?php
namespace Src\Controller;

use Src\TableGateways\PersonGateway;

class PersonController {

    private $db;
    private $requestMethod;
    private $userId;

    private $personGateway;

    public function __construct($db )
    {
        $this->db = $db;
        // $this->requestMethod = $requestMethod;
        // $this->userId = $userId;

        // $this->personGateway = new PersonGateway();
    }

    public function processRequest($requestMethod, $userId)
    {
        switch ($requestMethod) {
            case 'GET':
                //if ($this->userId) {
                //    $response = $this->getUser($this->userId);
                //} else {
                    $response = $this->getPerson();
                // };
                break;
            // case 'POST':
            //     $response = $this->createUserFromRequest();
            //     break;
            // case 'PUT':
            //     $response = $this->updateUserFromRequest($this->userId);
            //     break;
            // case 'DELETE':
            //     $response = $this->deleteUser($this->userId);
            //     break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsers()
    {
    $array = ['€', 55.6666666666666666, 'http://example.com/some/cool/page', '000337', '55.6666666666666666'];
        $result =json_decode ($test);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($array);
        return $response;
    }
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

}