<?php
header('Content-Type: application/json');
/**
 * Class to get info from DB
 */
class Read {
	private $mysql;
	
	function __construct($db)
	{
		$this->mysql = $db;
	}

	/**
	 * Get all the data in DB
	 */
	function getAllPhonebook() {
		try {
			$data = array();
			$sqlQuery = "SELECT * FROM phonebook as pb LEFT JOIN phone as p ON pb.id = p.fkPhonebook LEFT JOIN email as e ON pb.id = e.fkPhonebook";
			$stmt = $this->mysql->prepare($sqlQuery);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($stmt->rowCount() > 0) {
				foreach ($result as $key => $value) {
					$id = $value['id'];
					if (!array_key_exists($id, $data)) {
						$data[$id] = array();
					}

		            $data[$id]['firstname'] = $value['firstname'];
		            $data[$id]['surnames'] = $value['surnames'];
		            $type = $value['type'];
		            $phone = $value['phone'];
		            $email = $value['email'];
		            $data[$id]['phones'][$type] = $phone;
		            $data[$id]['emails'][$email] = $email;
				}
		        http_response_code(202);
		        echo json_encode($data);
			} else {
				http_response_code(204);
			}
			exit;
		} catch (Exception $e) {
			http_response_code(503);
	    	echo json_encode(array("message" => 'Caught exception: ',  $e->getMessage(), "\n"));
		}
	}

	/**
	 * Get phonebook data by Id
	 * @param  int $id
	 */
	function getPhonebook($id) {
		try {
			$data = array();
			$stmt = $this->mysql->prepare("SELECT * FROM phonebook as pb LEFT JOIN phone as p ON pb.id = p.fkPhonebook LEFT JOIN email as e ON pb.id = e.fkPhonebook WHERE pb.id = :id");
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			if ($stmt->rowCount() > 0) {
	  			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	  			foreach ($result as $key => $value) {
					$id = $value['id'];
		            $data[$id]['firstname'] = $value['firstname'];
		            $data[$id]['surnames'] = $value['surnames'];
		            $type = $value['type'];
		            $phone = $value['phone'];
		            $email = $value['email'];
		            $data[$id]['phones'][$type] = $phone;
		            if (!in_array($email, $data[$id]['emails'])) {
		            	$data[$id]['emails'][] = $email;
		            }
				}
		        http_response_code(202);
		        echo json_encode($data);
	  		} else {
				http_response_code(204);
			}
			exit;
		} catch (Exception $e) {
			http_response_code(503);
	    	echo json_encode(array("message" => 'Caught exception: ',  $e->getMessage(), "\n"));
		}
	}

	/**
	 * Search into DB
	 * @param  [array] $search
	 * @return [json]
	 */
	function searchPhonebook($search) {
		try {
			$sqlQuery = "SELECT * FROM phonebook as pb LEFT JOIN phone as p ON pb.id = p.fkPhonebook LEFT JOIN email as e ON pb.id = e.fkPhonebook WHERE ";
			
			if (isset($search['firstname'])) {
	        	$sqlQuery.= "pb.firstname LIKE :firstname AND ";
	        }

	        if (isset($search['surnames'])) {
	        	$sqlQuery.= "pb.surnames LIKE :surnames AND ";
	        }

	        if (isset($search['phone'])) {
	        	$sqlQuery.= "p.phone LIKE :phone AND ";
	        }

	        if (isset($search['email'])) {
	        	$sqlQuery.= "e.email LIKE :email AND ";
	        }

			$sqlQuery.= "1 = 1";

			$stmt = $this->mysql->prepare($sqlQuery);

			if (isset($search['firstname'])) {
	        	$stmt->bindValue(':firstname', "%".htmlspecialchars(strip_tags($search['firstname']))."%", PDO::PARAM_STR);
	        }

	        if (isset($search['surnames'])) {
	        	$stmt->bindValue(':surnames', "%".htmlspecialchars(strip_tags($search['surnames']))."%", PDO::PARAM_STR);
	        }

	        if (isset($search['phone'])) {
	        	$stmt->bindValue(':phone', "%".htmlspecialchars(strip_tags($search['phone']))."%", PDO::PARAM_STR);
	        }

	        if (isset($search['email'])) {
	        	$stmt->bindValue(':email', "%".htmlspecialchars(strip_tags($search['email']))."%", PDO::PARAM_STR);
	        }

			$stmt->execute();

			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($stmt->rowCount() > 0) {
				foreach ($result as $key => $value) {
					$id = $value['id'];
					if (!array_key_exists($id, $data)) {
						$data[$id] = array();
					}

		            $data[$id]['firstname'] = $value['firstname'];
		            $data[$id]['surnames'] = $value['surnames'];
		            $type = $value['type'];
		            $phone = $value['phone'];
		            $email = $value['email'];
		            $data[$id]['phones'][$type] = $phone;
		            $data[$id]['emails'][$email] = $email;
				}
		        http_response_code(202);
		        echo json_encode($data);
			} else {
				http_response_code(204);
			}
			exit;
		} catch (Exception $e) {
			http_response_code(503);
	    	echo json_encode(array("message" => 'Caught exception: ',  $e->getMessage(), "\n"));
		}
	}
}