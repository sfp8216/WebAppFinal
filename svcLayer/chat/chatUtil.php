<?php
require_once ('BizDataLayer/chatData.php');
require_once ('svcLayer/security/security.php');
function getChatLobbiesSvs($d, $ip, $token) {
	if(isset($_SESSION["Logged"])) {
		echo (getChatLobbiesData());
	}
	else{
		echo '[{"Error":"Please Login"}]';
	}
}
function getChatUsersSvs($data) {
	$lobbyId = $data;
	if(filterBy($lobbyId, "Number") != "error") {
		echo (getChatUsersData($lobbyId));
	}
}
function getChatSvs($d, $ip, $token) {
	$chatLobby = $d;
	if(filterBy($chatLobby, "Number") != "error") {
		echo (getChatData($chatLobby));
	}
}
function sendChatSvs($data, $ip, $token) {
	if(isset($_SESSION["Logged"])) {
		$h = strip_tags($data);
		//prep data
		//split data on |
		$h = explode('|', $data);
		$lobbyId = $h[0];
		$sessionData = json_decode($_SESSION["Logged"]);
		$userId = $sessionData[0]->{'userId'};
		$message = $h[1];
		if(filterBy($lobbyId, "Number") != "error" && filterBy($userId, "Number") != "error" && filterBy($message, "Text", 150) != "error") {
			echo (sendChatData($lobbyId, $userId, $message));
		}
	}
	else{
		echo "error";
	}
}

function checkChatInviteSvs($data) {
	if(isset($_SESSION["Logged"])) {
		$sessionData = json_decode($_SESSION["Logged"]);
		$userId = $sessionData[0]->{'userId'};
		echo (checkChatInviteData($userId));
	}
}

function checkInviteHistorySvs($data){
    if(isset($_SESSION["Logged"])){
        $sessionData = json_decode($_SESSION["Logged"]);
        $userId = $sessionData[0]->{'userId'};
        echo(checkInviteHistoryData($userId));
    }
}

?>