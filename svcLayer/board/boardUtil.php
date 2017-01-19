<?php
//Board Security/Sanitization layer
require_once ('BizDataLayer/boardData.php');
require_once('svcLayer/security/security.php');
function getBoardsSvs() {
	//SECURITY
	$userId = getSessionUserId();
	if($userId != "Error") {
		echo (getBoardsData($userId));
	}
}
function lockBoardSvs($data) {
	$boardId = $data;
	$userId = getSessionUserId();
	//Check for valid input//
	if(filterBy($boardId, "Number") != "error" && $userId != "Error") {
		echo (lockBoardData($boardId, $userId));
	}
}
function checkLockSvs($data) {
	$h = explode("|", $data);
	$boardId = $h[0];
	$overwrite = $h[1];
	$userId = getSessionUserId();
	//Check for valid input//
	if(filterBy($boardId, "Number") != "error" && $userId != "Error") {
		echo (checkLockData($boardId, $overwrite, $userId));
	}
}
function saveStrokeSvs($data) {
	//$data: gameId|userId
	$userId = getSessionUserId();
	$h = explode('|', $data);
	$type = $h[0];
	$points = $h[1];
	$boardId = $h[2];
	$strokeId = $h[3];
	$color = $h[4];
	$brushSize = $h[5];
	//Check for valid input//
	if(filterBy($type, "Text") != "error" && filterBy($boardId, "Number") != "error" && filterBy($color, "Text") != "error" && filterBy($brushSize, "Number", 3) != "error" && $userId != "Error") {
		echo (saveStrokeData($type, $points, $boardId, $strokeId, $userId, $color, $brushSize));
	}
}
function loadBoardSvs($boardId) {
	//Check for valid input//
	if(filterBy($boardId, "Number") != "error") {
		echo (loadBoardData($boardId));
	}
}
function getTurnSvs($data, $ip, $token) {
	//$data: gameId|userId
	$h = explode('|', $data);
	$gId = $h[0];
	$uId = $h[1];
	//include the bizData layer
	echo (getTurnData($gId, $uId));
}
function clearBoardSvs($data) {
	if(filterBy($data, "Number") != "error") {
		echo clearBoardData($data);
	}
}
function createBoardSvs($data) {
	$h = explode('|', $data);
	//Data = BoardName|OwnerId| ChatLobbyId| Public or private
	$boardName = $h[0];
	$ownerId = getSessionUserId();
	$public = (int) $h[1];
	if((filterBy($boardName, "Text", 20) != "error" ) && (filterBy($ownerId, "Number") != "error") && (filterBy($public, "Bool") != "error")) {
	   echo (createBoardData($boardName, $ownerId, $public));
	}
}
function deleteBoardSvs($data) {
	//CLEAN IT  TODO FINISH IT
	$h = explode('|', $data);
	//Data =
	// BoardName|OwnerId| ChatLobbyId| Public or private
	$boardName = $h[0];
	$ownerId = getSessionUserId();
	echo (deleteBoardData($boardName, $ownerId));
}
function inviteToBoardSvs($data) {
	$h = explode('|', $data);
	//Data =
	// BoardName|OwnerId| ChatLobbyId| Public or private
	$boardId = $h[0];
	$userId = $h[1];
	$inviteer = getSessionUserId();
	if(filterBy($boardId, "Number") != "error" && filterBy($userId, "Text") != "error" && filterBy($inviteer, "Number") != "error") {
	 	echo (inviteToBoardData($boardId, $userId, $inviteer));
	}
}
function removeFromBoardSvs($data) {
	$h = explode('|', $data);
	//Data =
	// BoardName|OwnerId| ChatLobbyId| Public or private
	$boardName = $h[0];
	$userId = $h[1];
	$inviteer = getSessionUserId();
    var_dump($data);
	if(filterBy($boardName, "Text") != "error" && filterBy($userId, "Text") != "error" && filterBy($inviteer, "Number") != "error") {
	 	echo (removeFromBoardData($boardName, $userId, $inviteer));
	}
}
function updateInviteSvs($data) {
	$h = explode('|', $data);
	$boardName = $h[0];
	$inviter = $h[1];
	$invitee = getSessionUserId();
	$acceptOrDeny = $h[2];
	if($acceptOrDeny == 1) {
		if(filterBy($boardName, "Text", 20) != "error" && filterBy($inviter, "Text") != "error" && filterBy($invitee, "Number") != "error") {
		  	echo (acceptBoardInviteData($boardName, $inviter, $invitee));
		}
	}
	else
		if($acceptOrDeny == 0) {
			echo (denyBoardInviteData($boardName, $inviter, $invitee));
		}
}
function canJoinBoardSvs($boardId) {
	$userId = getSessionUserId();
	if(filterBy($boardId, "Number") != "error" && $userId != "Error") {
		echo (canJoinBoardData($boardId, $userId));
	}
}
/*FUNCTION THAT CHECKS FOR USERID AND SANITIZES IT*/
function getSessionUserId() {
	if(isset($_SESSION["Logged"])) {
		$sessionData = json_decode($_SESSION["Logged"]);
		//Get the ID
		$sessionData = $sessionData[0]->{'userId'};
		$cleanUserId = $sessionData;
		$cleanUserId = strip_tags($cleanUserId);
		$cleanUserId = htmlentities($cleanUserId);
		return $cleanUserId;
	}
	else{
		return "Error";
	}
}
?>