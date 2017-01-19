<?php
/*Clean Bill*/
//include dbInfo
require_once ("../../dbinfo.inc");
//include exceptions
require_once ('exception.php');
function getBoardsData($userId) {
	global $pdo;
	$filteredList[] = "";
	//CHECK IF LOGGED IN
	$sql = "Select * FROM whiteboard";
	$count = 0;
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($results as $key => $boardValue) {
				if($boardValue["public"] == 0) {
					$sql = "SELECT * FROM chatusers where lobbyId = ? and userId =? ";
					if($stmt = $pdo->prepare($sql)) {
						$stmt->bindParam(1, $boardValue["lobbyId"]);
						$stmt->bindParam(2, $userId);
						$stmt->execute();
						$rowCount = $stmt->rowCount();
						if($rowCount == 1) {
							foreach($boardValue as $key) {
								$filteredList[$count] = $boardValue;
							}
							$count++;
						}
					}
				}
				else{
					//it is public
					foreach($boardValue as $key) {
						$filteredList[$count] = $boardValue;
					}
					$count++;
				}
			}
			echo json_encode($filteredList);
		}
	}
	catch (Exception $e) {
		return '[{"Error":"GetBoardData"}]"';
	}
}
function checkLockData($boardId, $overwrite, $userId) {
	global $pdo;
	$sql = "SELECT wb.locked, wb.timelock, wb.userId, usr.username FROM whiteboard wb INNER JOIN users usr on (wb.userId = usr.userId) WHERE wb.boardId = ?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results = $results[0];
			$timelock = $results["timelock"];
			$lockedToUser = $results["userId"];
			$timeNow = new DateTime();
			$timeOUT = new DateTime($timelock);
			$timeOUT->modify("+ 30 Seconds");
			if($overwrite != "1") {
				if($timelock != null) {
					// It is in use
					if($timeNow > $timeOUT) {
						// the time expired
						// Update the board with null status and return locked false
						$sql = "UPDATE whiteboard set locked = null, timelock = null, userId = null where boardId = ?";
						if($stmt = $pdo->prepare($sql)) {
							$stmt->bindParam(1, $boardId);
							$stmt->execute();
							return '[{"Locked":"false"}]';
						}
					}
					else{
						// time didnt expire
						//Check if user is the same
						if($lockedToUser == $userId) {
							return '[{"Locked":"timed"}]';
						}
						else{
							return '[{"Locked":"true","Username":"'.$results["username"].'"}]';
						}
					}
				}
				else{
					// Timelock is null
					return '[{"Locked":"false"}]';
				}
			}
			else{
				$sql = "UPDATE whiteboard set Locked = null, Timelock = null, userid = null where boardid = ?";
				if($stmt = $pdo->prepare($sql)) {
					$stmt->bindParam(1, $boardId);
					$stmt->execute();
					return '[{"Locked":"false"}]';
				}
			}
		}
	}
	catch (Exception $e) {
		return '[{"Error":"CheckLock"}]"';
	}
}
function lockBoardData($boardId, $userId) {
	global $pdo;
	$date = new DateTime();
	$sql = "UPDATE whiteboard SET locked = 1, userid = ?, timelock = ? WHERE boardId = ?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $userId);
			$stmt->bindParam(2, $date->format('Y-m-d  H:i:s'));
			$stmt->bindParam(3, $boardId);
			$stmt->execute();
			return '[{"Locked":"true"}]';
		}
	}
	catch (Exception $e) {
		return '[{"Error":"LockBoard"}]"';
	}
}
//Createboard name, owner, public or private
//Creates a whiteboard and chatroom connected
function createBoardData($name, $owner, $public) {
	global $pdo;
	$sql = "INSERT INTO chatlobby  (ownerId, name, public) VALUES(?,?,?);
    INSERT INTO whiteboard (ownerId, lobbyId, name, public) SELECT ?, lobbyId, ?, ? FROM chatlobby where name = ?;
    INSERT INTO chatusers (lobbyId,userId) SELECT lobbyId, ownerId FROM chatlobby where name = ?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $owner, PDO::PARAM_INT);
			$stmt->bindParam(2, $name);
			$stmt->bindParam(3, $public, PDO::PARAM_INT);
			$stmt->bindParam(4, $owner, PDO::PARAM_INT);
			$stmt->bindParam(5, $name);
			$stmt->bindParam(6, $public, PDO::PARAM_INT);
			$stmt->bindParam(7, $name);
			$stmt->bindParam(8, $name);
			$stmt->execute();
            return '[{"Created":"true"}]';
		}
	}
	catch (Exception $e) {
		return '[{"Error":"CreateBoard"}]"';
	}
}
//Delete whiteboard
function deleteBoardData($name, $owner) {
	global $pdo;
	//Checks to make sure only owner can delete
	$sql = "DELETE FROM chatlobby where name = ? AND ownerid = ? ; DELETE FROM whiteboard where name = ? and ownerId = ?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $name);
			$stmt->bindParam(2, $owner);
			$stmt->bindParam(3, $name);
			$stmt->bindParam(4, $owner);
			$stmt->execute();
			$rowCount = $stmt->rowCount();
            return '[{"Delete":"Successful"}]';
		}
	}
	catch (Exception $e) {
		return '[{"Error":"DeleteBoard"}]"';
	}
}
function saveStrokeData($type, $points, $boardId, $strokeId, $userId, $color, $brushSize) {
	global $pdo;
	$sql = "INSERT INTO whiteboarddata(boardId, shape, points, userid, strokeId,color,brushSize) VALUES (?,?,?,?,?,?,?)";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			$stmt->bindParam(2, $type);
			$stmt->bindParam(3, $points);
			$stmt->bindParam(4, $userId);
			$stmt->bindParam(5, $strokeId);
			$stmt->bindParam(6, $color);
			$stmt->bindParam(7, $brushSize);
			$stmt->execute();
		}
		else
			if(!$points) {
				throw new Exception("Error with boarddata");
			}
	}
	catch (Exception $e) {
		log_error($e, $sql, null);
		return '[{Error":SaveStroke"}]';
	}
}
function loadBoardData($boardId) {
	global $pdo;
	$sql = "SELECT * FROM whiteboarddata WHERE boardid=? ORDER BY time";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			echo returnJson($stmt);
		}
		else
			if(!$data) {
				throw new Exception("Error with chat lobbies");
			}
	}
	catch (Exception $e) {
		log_error($e, $sql, null);
		return '[{Error":LoadBoard"}]';
	}
}
function clearBoardData($boardId) {
	global $pdo;
	$sql = "DELETE FROM whiteboarddata WHERE boardid = ?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			echo returnJson($stmt);
		}
	}
	catch (Exception $e) {
		return '[{"Error":"ClearBoard"}]"';
	}
}
function inviteToBoardData($boardId, $userId, $inviter) {
	global $pdo;
	try {
		$sql = "SELECT userid from users where username = ?";
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $userId);
			$stmt->execute();
			$userId = $stmt->fetchColumn(0);
		}
		$sql = "SELECT lobbyid from whiteboard where boardid = ?";
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			$stmt->execute();
			$lobbyId = $stmt->fetchColumn(0);
		}
		$sql = "INSERT INTO invites (boardId, lobbyId, inviter,invitee) values(?,?,?,?)";
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			$stmt->bindParam(2, $lobbyId);
			$stmt->bindParam(3, $inviter);
			$stmt->bindParam(4, $userId);
			$stmt->execute();
		}
	}
	catch (Exception $e) {
		return "Error";
	}
}

function removeFromBoardData($boardName, $userId, $inviter) {
	global $pdo;
	try {
		$sql = "SELECT inv.inviter as Inviter, usr.username, brd.name FROM invites inv INNER JOIN users usr on (inv.invitee = usr.userId) INNER JOIN whiteboard brd on (inv.boardId = brd.boardId) WHERE Inviter = ? AND usr.username = ? AND brd.name =?";
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $inviter);
			$stmt->bindParam(2, $userId);
			$stmt->bindParam(3, $boardName);
			$stmt->execute();
			$rowCount = $stmt->rowCount();
            if($rowCount == 1){
                $sql = "DELETE inv FROM invites inv INNER JOIN users usr on (inv.invitee = usr.userId) INNER JOIN whiteboard brd on (inv.boardId = brd.boardId) WHERE inv.inviter = ? AND usr.username = ? AND brd.name =?;";
                $sql .= "DELETE chatusr FROM chatusers chatusr INNER JOIN users usr on (chatusr.userId = usr.userId) INNER JOIN whiteboard brd on (chatusr.lobbyId = brd.lobbyId) WHERE usr.username = ? AND brd.name =?";
                if($stmt = $pdo -> prepare($sql)){
                    $stmt->bindParam(1, $inviter);
        			$stmt->bindParam(2, $userId);
        			$stmt->bindParam(3, $boardName);
        			$stmt->bindParam(4, $userId);
        			$stmt->bindParam(5, $boardName);
        			$stmt->execute();
        			$rowCount = $stmt->rowCount();
                }
            }else{
                return json_encode("Error:Your Not Admin");
            }
            return "[{'Delete','Success'}]";
		}
	}
	catch (Exception $e) {
		return "Error";
	}
}

function acceptBoardInviteData($boardName, $inviter, $invitee) {
	global $pdo;
	$sql = "UPDATE invites inv INNER JOIN whiteboard wb ON wb.boardId = inv.boardId SET accepted = 1 Where wb.name = ? AND inv.invitee = ?;";
	$sql .= "INSERT INTO chatusers (lobbyId,userId) values (?,?)";
	try {
		$getboardId = "SELECT * from whiteboard where name = ?";
		if($stmt = $pdo->prepare($getboardId)) {
			$stmt->bindParam(1, $boardName);
			$stmt->execute();
			$boardRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $boardRow = $boardRow[0];
		}
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardName);
			$stmt->bindParam(2, $invitee);
			$stmt->bindParam(3, $boardRow["lobbyId"]);
			$stmt->bindParam(4, $invitee);
			$stmt->execute();
			return '[{"Accepted":"Success"}]';
		}
	}
	catch (Exception $e) {
		return '[{"Error":"AcceptError"}]';
	}
}
function canJoinBoardData($boardId, $userId) {
	global $pdo;
	$sql = "SELECT * from whiteboard where boardId=?";
	try {
		if($stmt = $pdo->prepare($sql)) {
			$stmt->bindParam(1, $boardId);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results = $results[0];
			if($results["public"] == 1) {
				//It is public
				//Add user to the chatRoom but first check if they are already there
				$sql = "SELECT * FROM chatusers where lobbyid=? and userid=?";
				if($stmt = $pdo->prepare($sql)) {
					$stmt->bindParam(1, $results["lobbyId"]);
					$stmt->bindParam(2, $userId);
					$stmt->execute();
					$rowCount = $stmt->rowCount();
					if($rowCount == 0) {
						$sql = "INSERT INTO chatusers(lobbyId, userId) VALUES (?,?)";
						if($stmt = $pdo->prepare($sql)) {
							$stmt->bindParam(1, $results["lobbyId"]);
							$stmt->bindParam(2, $userId);
							$stmt->execute();
                            if($results["ownerId"] == $userId){
						    	return '[{"allowed":true,"Owner":"true"}]';
                            }
							return '[{"allowed":true,"Owner":"false"}]';
						}
					}
					else{
						if($results["ownerId"] == $userId){
						    	return '[{"allowed":true,"Owner":"true"}]';
                            }
							return '[{"allowed":true,"Owner":"false"}]';
					}
				}
			}
			if($results["public"] == 0) {
				$sql = "SELECT * FROM chatusers where lobbyId = ? and userId = ?";
				if($stmt = $pdo->prepare($sql)) {
					$stmt->bindParam(1, $results["lobbyId"]);
					$stmt->bindParam(2, $userId);
					$stmt->execute();
					$rowCount = $stmt->rowCount();
					if($rowCount == 1) {
						if($results["ownerId"] == $userId){
						    	return '[{"allowed":true,"Owner":"true"}]';
                            }
							return '[{"allowed":true,"Owner":"false"}]';
					}
					else{
					   if($results["ownerId"] == $userId){
						    	return '[{"allowed":false,"Owner":"false"}]';
                            }
							return '[{"allowed":false,"Owner":"false"}]';
					}
				}
			}
		}
	}
	catch (Exception $e) {
		return '[{"allowed":"error"}]';
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
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	//MUST change the content-type
	header("Content-Type:text/plain");
	// This will become the response value for the XMLHttpRequest object
	return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>