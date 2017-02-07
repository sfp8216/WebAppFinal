<?php
//include dbInfo
require_once ("../../dbinfo.inc");
//include exceptions
function checkLoginData($uName, $pWord, $token) {
	global $pdo;
	try {
		$stmt = $pdo->prepare('SELECT userId, username, password, lastLogin FROM users where username = :username and password = :password ');
		$stmt->execute(array(':username' => $uName, ':password' => $pWord));
		$count = $stmt->rowCount();
		//Set status to Online
		if($count == 1) {
			$result = $stmt->fetch();
			//Check Token/Cookie
			$incomingToken = explode("|", unTokenize($token));
			if($result["lastLogin"] == null) {
				$stmt = $pdo->prepare('Update users set status=1, lastLogin=:token where username=:user and password=:pass');
				$stmt->execute(array(':token' => $token, ':user' => $uName, ':pass' => $pWord));
				$_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'] . '","Username":"' . $uName . '"}]';
				return '[{"Logged":"true","userId":"' . $result['userId'] . '","Username":"' . $uName . '"}]';
			}
			$storedToken = explode("|", unTokenize($result['lastLogin']));
			//Check to make sure data hasn't been tampered
			$inl = $incomingToken[2];
			$inf = $incomingToken[3];
			$storel = $storedToken[2];
			$storef = $storedToken[3];
			$storedDate = $storedToken[1];
			$expireDate = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));
			if($inl != $storel || $inf != $storef) {
				echo "Data Tamptered";
			}
			else
			//Check user agent
				if($storedToken[0] != $incomingToken[0]) {
					$_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'] . '","Token":"Browser","Username":"' . $uName . '"}]';
					$stmt = $pdo->prepare('Update users set status=1, lastLogin=:token where username=:user and password=:pass');
					$stmt->execute(array(':token' => $token, ':user' => $uName, ':pass' => $pWord));
					return '[{"Logged":"success","userId":"' . $result['userId'] . '","Token":"Browser","Username":"' . $uName . '"}]';
				}
				else 				//Check time
					if($storedDate > $expireDate) {
						$_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'] . '","Token":"Date","Username":"' . $uName . '"}]';
						$stmt = $pdo->prepare('Update users set status=1, lastLogin=:token where username=:user and password=:pass');
						$stmt->execute(array(':token' => $token, ':user' => $uName, ':pass' => $pWord));
						return '[{"Logged":"success","userId":"' . $result['userId'] . '","Token":"Date","Username":"' . $uName . '"}]';
					}
					else{
						$stmt = $pdo->prepare('Update users set status=1, lastLogin=:token where username=:user and password=:pass');
						$stmt->execute(array(':token' => $token, ':user' => $uName, ':pass' => $pWord));
						$_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'] . '","Username":"' . $uName . '"}]';
						return '[{"Logged":"true","userId":"' . $result['userId'] . '","Username":"' . $uName . '"}]';
			}
		}
		else{
			return '[{"Logged":"fail"}]';
		}
	}
	catch (Exception $e) {
		log_error($e, $sql, null);
		return '[{"Error":"fail"}]';
	}
}
function getUserListData($lobbyId) {
	global $pdo;
	try {
		$stmt = $pdo->prepare("SELECT username, status from users");
		$stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return json_encode($result);
	}
	catch (Exception $e) {
	   return '[{"Error":"GetUserList"}]';
	}
}
function logoutData($userId) {
	global $pdo;
	if(isset($_SESSION["Logged"])) {
		try {
			$stmt = $pdo->prepare("UPDATE users set status = 0 where userId=:username");
			$stmt->bindParam(":username", $userId);
			$stmt->execute();
		}
		catch (Exception $e) {
			return "Error";
		}
		$_SESSION["Logged"] = "";
		session_destroy();
		return '[{"Logout":"Success"}]';
	}
}
//"Cron" job to help clear out inactive accounts
//Checks for users that are inactive 30 minutes
function checkExpiredLoginData() {
	global $pdo;
	$sql = "SELECT * from users where status = 1";
	if($stmt = $pdo->prepare($sql)) {
		$stmt->execute();
		$result = $stmt->fetchAll();
		foreach($result as $suspectUsers) {
			$lastLogin = explode("|", unTokenize($suspectUsers["lastLogin"]));
            $lastLogin = $lastLogin[1];
			$timeout_time = date(strtotime($lastLogin));
			$timeout_time = (strtotime("+2 hours", $timeout_time));
			$time_now = strtotime("now");
			if($time_now > $timeout_time) {
				$sql = "UPDATE users set STATUS = 0 WHERE userId=?";
				if($stmt = $pdo->prepare($sql)) {
					$stmt->bindParam(1, $suspectUsers["userId"]);
					$stmt->execute();
				}
			}
		}
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