<?php
/*Clean Bill*/
//include dbInfo
require_once ("../dbinfo.inc");
//include exceptions
require_once ("exception.php");
function createLoginData($uName, $pWord) {
	global $pdo;
	try {
		$stmt = $pdo->prepare("INSERT INTO users (username, password, status) VALUES (:user, :pass, 1)");
		$stmt->bindParam(':user', $uName);
		$stmt->bindParam(':pass', $pWord);
		$stmt->execute();
		$count = $stmt->rowCount();    
		if($count == 1) {
		    $sql = "SELECT userId, status FROM users WHERE username =?";
            if($stmt=$pdo->prepare($sql)){
                $stmt->bindParam(1,$uName);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result = $result[0];
                $_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'].'","Username":"'.$uName.'"}]';
            }
			return '[{"Create":"success"}]';
		}
		else{
			return '[{"Create":"fail"}]';
		}
	}
	catch (Exception $e) {
		log_error($e, $sql, null);
		return '[{"error":"fail"}]';
	}
}
/*********************************Utilities*********************************/
/*************************
returnJson
takes: prepared statement
-parameters already bound
returns: json encoded multi-dimensional associative array
*/
function returnJson($stmt) {
	$stmt->execute();
	$stmt->store_result();
	$meta = $stmt->result_metadata();
	$bindVarsArray = array();
	//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
	while($column = $meta->fetch_field()) {
		$bindVarsArray[] = & $results[$column->name];
	}
	//bind it!
	call_user_func_array(array($stmt, 'bind_result'), $bindVarsArray);
	//now, go through each row returned,
	while($stmt->fetch()) {
		$clone = array();
		foreach($results as $k => $v) {
			$clone [$k] = $v;
		}
		$data[] = $clone;
	}
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	//MUST change the content-type
	header("Content-Type:text/plain");
	// This will become the response value for the XMLHttpRequest object
	return json_encode($data);
}
?>