<?php
header('Content-Type: application/json');
/**
 * Class to create values in DB
 */
class Create {
	private $mysql;
	
	function __construct($db)
	{
		$this->mysql = $db;
	}

	/**
	 * create Values from json
	 * @param  [array] $data
	 * @return [json]
	 */
	function createValues($data) {
		//Validate obligatory values
		if (isset($data->firstname) AND isset($data->surnames) AND !empty($data->firstname) AND !empty($data->surnames)) {
	    	$insertQuery = "INSERT INTO `phonebook`(firstname, surnames) VALUES (:firstname, :surnames)";

    		$this->mysql->beginTransaction();
	        $stmt = $this->mysql->prepare($insertQuery);

	        $stmt->bindValue(':firstname', htmlspecialchars(strip_tags($data->firstname)),PDO::PARAM_STR);
	        $stmt->bindValue(':surnames', htmlspecialchars(strip_tags($data->surnames)),PDO::PARAM_STR);

	        if(!$stmt->execute()){
	         	$this->mysql->rollBack();
	            http_response_code(500);
	    		echo json_encode($stmt->errorInfo());
	    		exit;   
	        }

	        $id = $this->mysql->lastInsertId();

	        if (isset($data->phone) AND !empty($data->phone)) {
	        	$this->savePhone($data->phone, $id);
	        }

	        if (isset($data->email) AND !empty($data->email)) {
	        	$this->saveEmail($data->email, $id);
	        }

	        $this->mysql->commit();

	        http_response_code(202);
			echo json_encode(array("message" => "Data saved correctly.", "data" => $data));
			exit;
	    }
		http_response_code(404);
	    echo json_encode(array("message" => "Obligatory value missing"));
	    exit;
	}

	/**
	 * save Phone
	 * @param  [array] $dataPhone
	 * @param  [int] $id 
	 */
	private function savePhone($dataPhone, $id) {
		foreach ($dataPhone as $type => $value) {
			$insertPhone = "INSERT INTO `phone` (`phone`, `type`, `fkPhonebook`) VALUES (:phone, :type, :idPhone)";
			$insertPhoneSTMT = $this->mysql->prepare($insertPhone);
	        $insertPhoneSTMT->bindValue(':phone', htmlspecialchars(strip_tags($value)),PDO::PARAM_INT);
	        $insertPhoneSTMT->bindValue(':type', htmlspecialchars(strip_tags($type)),PDO::PARAM_STR);
	    	$insertPhoneSTMT->bindValue(':idPhone', $id, PDO::PARAM_INT);

			// If phone doesnt exist previously or another error 
			if (!$insertPhoneSTMT->execute()) {
	        	$this->mysql->rollBack();
	            http_response_code(404);
	    		echo json_encode(array("message" => $insertPhoneSTMT->errorInfo()));
	    		exit;
	        }
		} // end foreach
	}

	/**
	 * Save Email
	 * @param  [array] $emails
	 * @param  [int] $id
	 */
	private function saveEmail($emails, $id) {
		foreach ($emails as $email) {
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        	$this->mysql->rollBack();
				http_response_code(404);
	    		echo json_encode(array("message" => "not a valid email address"));
	    		exit;
			}

			$insertEmail = "INSERT INTO `email` (`idEmail`, `email`, `fkPhonebook`) VALUES (NULL, :email, :idEmail)";
	        $insertEmailSTMT = $this->mysql->prepare($insertEmail);
	        $insertEmailSTMT->bindValue(':email', htmlspecialchars(strip_tags($email)),PDO::PARAM_STR);
        	$insertEmailSTMT->bindValue(':idEmail', $id, PDO::PARAM_INT);
		
			// Email is unique in DB, we only verify query is done
			if (!$insertEmailSTMT->execute()) {
	        	$this->mysql->rollBack();
	            http_response_code(404);
	    		echo json_encode(array("message" => $insertEmailSTMT->errorInfo()));
	    		exit;
	        }
		} // end foreach
	}
}