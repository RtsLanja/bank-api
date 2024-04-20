<?php
error_reporting(E_ALL);
ini_set('display_errors' , 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
include "../controller/Clients.php";
// Inclure la bibliothèque MySQL
require_once '../controller/DB_connexion.php';
$objDB = new DBConnect;
$conn = $objDB->connect();
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

$path = explode('/' , $request_uri);
$idClient = $path[3];
 
$clients = new client;

switch($request_method) {
        case 'GET':
                if($request_uri == '/react/api/'){
                        $clients->getAllClients($conn);
                }elseif(isset($path[3]) && is_numeric($path[3])){
                        $clients->getClientById($conn,$idClient);
                } elseif($request_uri == '/react/api/sold') {
                        $clients->getSold($conn);
                }
                else{
                        http_response_code(404);
                        echo json_encode(['URI Error' => 'URI not found']);
                }
                break;
        case 'POST':
                if($request_uri == '/react/api/save'){
                        $data = json_decode(file_get_contents('php://input'));
                        $clients->addClient($conn,$data);
                }else{
                        http_response_code(404);
                        echo json_encode(['URI Error' => 'URI not found']);
                }
                break;
        case 'PUT':
                $data = json_decode(file_get_contents('php://input'));
                $clients->updateClient($conn,$data);
                break;
        case 'DELETE':
                $clients->deleteUser($conn,$idClient);

}
?>