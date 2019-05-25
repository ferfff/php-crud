<?php
header('Content-Type: application/json');
/**
 * Class to DELETE values in DB
 */
class Delete {
	private $mysql;
	
	function __construct($db)
	{
		$this->mysql = $db;
	}

	/**
	 * delete Values
	 * @param  [int] $id
	 */
	function deleteValues($id) {
		$getValue = "SELECT * FROM `phonebook` WHERE id=:id";
	    $getValueSTMT = $this->mysql->prepare($getValue);
	    $getValueSTMT->bindValue(':id', $id, PDO::PARAM_INT);
	    $getValueSTMT->execute();
	    
	    if ($getValueSTMT->rowCount() > 0) {
	        $deletePost = "DELETE FROM `phonebook` WHERE id=:id";
	        $deletePostSTMT = $this->mysql->prepare($deletePost);
	        $deletePostSTMT->bindValue(':id', $id, PDO::PARAM_INT);
	        
	        if($deletePostSTMT->execute()){
	            http_response_code(202);
				echo json_encode(array("message" => "Id $id deleted correctly."));
	        }else{
	            http_response_code(404);
	    		echo json_encode(array("message" => $deletePostSTMT->errorInfo()));
	        }
	    	exit;
	    } else {
	        http_response_code(404);
	    	echo json_encode(array("message" => "Invalid ID"));
	    	exit;
	    }
	}
}