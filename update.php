<?php
header('Content-Type: application/json');
/**
 * Class to update info from DB
 */
class Update {
	private $mysql;
	
	function __construct($db)
	{
		$this->mysql = $db;
	}

	/**
	 * Update values from json
	 * @param [array] $data
	 */
	function setValues($data)
	{
		// Get and find id in DB
		$id = $data->id;
		$sqlQuery = "SELECT * FROM phonebook WHERE id=:id";
	    $stmt = $this->mysql->prepare($sqlQuery);
	    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
	    $stmt->execute();

	    if ($stmt->rowCount() > 0) {
	        $row = $stmt->fetch(PDO::FETCH_ASSOC);

	        $firstname = isset($data->firstname) ? $data->firstname : $row['firstname'];
	        $surnames = isset($data->surnames) ? $data->surnames : $row['surnames'];

	        $this->mysql->beginTransaction();
	        $updateQuery = "UPDATE `phonebook` SET firstname = :firstname, surnames = :surnames WHERE id = :id";
	        $updateSTMT = $this->mysql->prepare($updateQuery);

	        // Binding and sanitize values
	        $updateSTMT->bindValue(':firstname', htmlspecialchars(strip_tags($firstname)),PDO::PARAM_STR);
	        $updateSTMT->bindValue(':surnames', htmlspecialchars(strip_tags($surnames)),PDO::PARAM_STR);
	        $updateSTMT->bindValue(':id', $row['id'], PDO::PARAM_INT);

	        if (!$updateSTMT->execute()) {
	        	$this->mysql->rollBack();
	            http_response_code(500);
	    		echo json_encode($updateSTMT->errorInfo());
	    		exit;
	        }

	        // We save email if it is the case
	        if (isset($data->email) AND !empty($data->email)) {
	        	$this->saveEmail($data->email, $row['id']);
	        }

	        //We save phone if it is the case
	        if (isset($data->phone) AND !empty($data->phone)) {
	        	$this->savePhone($data->phone, $row['id']);
	        }
	        
	        $this->mysql->commit();
	        http_response_code(202);
			echo json_encode(array("message" => "Data saved correctly.", "data" => $data));
			exit;
	    }
	    else{
	        http_response_code(404);
	    	echo json_encode(array("message" => "Invalid ID"));
	    }
	    exit;
	}

	/**
	 * save Phone
	 * @param  [array] $dataPhone
	 * @param  [int] $id
	 */
	private function savePhone($dataPhone, $id) {
		foreach ($dataPhone as $type => $value) {
			$updatePhone = "UPDATE `phone` SET `phone` = :phone WHERE `type` = :type AND `fkPhonebook` = :idPhone";
			$updatePhoneSTMT = $this->mysql->prepare($updatePhone);
	        $updatePhoneSTMT->bindValue(':phone', htmlspecialchars(strip_tags($value)),PDO::PARAM_INT);
	        $updatePhoneSTMT->bindValue(':type', htmlspecialchars(strip_tags($type)),PDO::PARAM_STR);
	    	$updatePhoneSTMT->bindValue(':idPhone', $id, PDO::PARAM_INT);

			// If phone doesnt exist previously or another error 
			if (!$updatePhoneSTMT->execute()) {
	        	$this->mysql->rollBack();
	            http_response_code(404);
	    		echo json_encode(array("message" => "Error updating phone"));
	    		exit;
	        }

	        if ($updatePhoneSTMT->rowCount() === 0) {
	        	$this->mysql->rollBack();
	            http_response_code(404);
	    		echo json_encode(array("message" => "Type $type doesnt exist OR phone was already registered"));
	    		exit;
	        }
		} // end foreach
	}

	/**
	 * save Email
	 * @param  [array] $dataEmail
	 * @param  [int] $id 
	 */
	private function saveEmail($dataEmail, $id) {
		$deleteEmail = "DELETE FROM `email` WHERE fkPhonebook = :id";
        $deleteEmailSTMT = $this->mysql->prepare($deleteEmail);
        $deleteEmailSTMT->bindValue(':id', $id, PDO::PARAM_INT);
        
        if (!$deleteEmailSTMT->execute()) {
            http_response_code(404);
    		echo json_encode(array("message" => $deleteEmailSTMT->errorInfo()));
        }

		foreach ($dataEmail as $email) {
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        	$this->mysql->rollBack();
				http_response_code(404);
	    		echo json_encode(array("message" => "not a valid email address"));
	    		exit;
			}

			$update_email = "INSERT INTO `email` (`idEmail`, `email`, `fkPhonebook`) VALUES (NULL, :email, :idEmail)";
	        $updateEmailSTMT = $this->mysql->prepare($update_email);
	        $updateEmailSTMT->bindValue(':email', htmlspecialchars(strip_tags($email)),PDO::PARAM_STR);
        	$updateEmailSTMT->bindValue(':idEmail', $id, PDO::PARAM_INT);
		
			// Email is unique in DB, we only verify query is done
			if (!$updateEmailSTMT->execute()) {
	        	$this->mysql->rollBack();
	            http_response_code(404);
	    		echo json_encode(array("message" => "Email already exists"));
	    		exit;
	        }
		} // end foreach
	}
}