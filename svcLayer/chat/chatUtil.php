<?php
//in the service layer, just got loaded in mid.php

//go to the data layer and actually get the data I want
//load up my specific biz/data layer file
require_once('BizDataLayer/chatData.php');

function getChatLobbiesSvs($d,$ip,$token){
    if(isset($_SESSION["Logged"])){
           echo(getChatLobbiesData());
    }
    else{
        echo '[{"Error":"Please Login"}]';
    }
}

function getChatUsersSvs($data){
    $lobbyId = $data;
    echo(getChatUsersData($lobbyId));
}

function getChatSvs($d,$ip,$token){
	//security
		//should they be here.  Check security to see if the ip and token match this user...
        $chatLobby = $d;
	//prep data
		//this data is empty (could - probably should - hold the game_id as a chat room identifier)

	//pass it on to the bizData layer
	echo(getChatData($chatLobby));

}

function sendChatSvs($data,$ip,$token){
    if(isset($_SESSION["Logged"])){
        $h = strip_tags($data);
        //prep data
        //split data on |
        $h=explode('|',$data);
        $lobbyId=$h[0];
        $sessionData =json_decode($_SESSION["Logged"]);
        $userId = $sessionData[0]->{'userId'};
        $message=$h[1];
       echo(sendChatData($lobbyId,$userId,$message));
    }else{
        //TODO  FIX ERROR
        
    echo "error";
    }
}

function checkChatInviteSvs($data){
    if(isset($_SESSION["Logged"])){
         $sessionData =json_decode($_SESSION["Logged"]);
        $userId = $sessionData[0]->{'userId'};
        echo(checkChatInviteData($userId));
    }
}


//eventually, need setChat(), clearChat(), banFromChat()



?>