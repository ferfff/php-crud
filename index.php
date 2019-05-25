<?php
include_once 'db.php';
include_once 'read.php';
include_once 'update.php';
include_once 'create.php';
include_once 'delete.php';

$db = Database::getInstance();

switch ($_SERVER['REQUEST_METHOD']) {
	// Create info
 	case 'POST':
		$data = json_decode(file_get_contents("php://input"));
		$createClass = new Create($db);
		$createClass->createValues($data);
 		break;
 	// Update info by id
 	case 'PUT':
 		$data = json_decode(file_get_contents("php://input"));

 		if (!empty($data->id)) {
 			$updateClass = new Update($db);
 			$updateClass->setValues($data);
 		} else {
 			http_response_code(503);
	    	echo json_encode(array("message" => "Please set an id to update."));
 		}
 		break;
 	// Get info (by id or all)
 	case 'GET':
 		$readClass = new Read($db);
 		if (isset($_GET["id"])) {
 			$readClass->getPhonebook((int)$_GET["id"]);
 		} elseif (isset($_GET["firstname"]) OR isset($_GET["surnames"]) OR isset($_GET["phone"]) OR isset($_GET["email"])) {
 			$readClass->searchPhonebook($_GET);
 		} else {
 			$readClass->getAllPhonebook();
 		}
 		break;
 	// delete info by id
 	case 'DELETE':
 		$data = json_decode(file_get_contents("php://input"));

 		if (!empty($data->id) AND is_int($data->id)) {
 			$deleteClass = new Delete($db);
 			$deleteClass->deleteValues($data->id);
 		} else {
 			http_response_code(503);
	    	echo json_encode(array("message" => "Please set an id to delete."));
 		}
 		break;
 	default:
 		http_response_code(503);
	    echo json_encode(array("message" => "Unable to process your request."));
 		break;
}
