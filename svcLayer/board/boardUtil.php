<?php
//ALL game goes in this folder
require_once ( 'BizDataLayer/boardData.php' );

function getBoardsSvs(){
    //SECURITY
    $userId = checkSession();
    echo(getBoardsData($userId));
}
function lockBoardSvs($data){
    $boardId = $data;
    $userId = checkSession();
    echo(lockBoardData($boardId,$userId));
}

function checkLockSvs($data){
    $h = explode("|",$data);
    $boardId = $h[0];
    $overwrite = $h[1];
    $userId = checkSession();
    echo(checkLockData($boardId,$overwrite,$userId));
}
function saveStrokeSvs($data) {
//$data: gameId|userId
//security               dStroke("path",pathData,boardId,strokeId
//should they be here.  Check security to see if the ip and token match this user...

//Get userId...
$userId = checkSession();
//prep data
//split data on
    $h = explode('|', $data);
    $type = $h[0];
    $points = $h[1];
    $boardId = $h[2];
    $strokeId = $h[3];
    $color = $h[4];

    echo (saveStrokeData($type,$points,$boardId, $strokeId, $userId,$color));
}
function loadBoardSvs($boardId) {
//$data: gameId|userId
 //Get Username from session

//security
//should they be here.  Check security to see if the ip and token match this user...
//prep data
//split data
    echo ( loadBoardData($boardId));
}
function getTurnSvs($data, $ip, $token) {
//$data: gameId|userId
//security
//should they be here.  Check security to see if the ip and token match this user...
//prep data
//split data on |
    $h = explode('|', $data);
    $gId = $h[0];
    $uId = $h[1];
//include the bizData layer
    echo ( getTurnData($gId, $uId));
}

function clearBoardSvs($data){
    //SECURITYYYYYYYYYYYYY
    echo clearBoardData($data);
}


function createBoardSvs($data){
    //CLEAN IT  TODO FINISH IT
    $h = explode('|',$data);
    //Data =
    // BoardName|OwnerId| ChatLobbyId| Public or private
    $boardName = $h[0];
    $ownerId = checkSession();
    $public = (int)$h[1];        
    echo(createBoardData($boardName,$ownerId,$public));
}
function deleteBoardSvs($data){
       //CLEAN IT  TODO FINISH IT
        $h = explode('|',$data);
        //Data =
        // BoardName|OwnerId| ChatLobbyId| Public or private
        $boardName = $h[0];
        $ownerId = checkSession();
        echo(deleteBoardData($name,$ownerId));
}
function inviteToBoardSvs($data){
    $h = explode('|',$data);
    //Data =
    // BoardName|OwnerId| ChatLobbyId| Public or private
    $boardId = $h[0];
    $userId = $h[1];
    $inviteer = checkSession();

    echo(inviteToBoardData($boardId,$userId,$inviteer));
}

function updateInviteSvs($data){
    $h = explode('|',$data);
    $boardName = $h[0];
    $inviter = $h[1];

    $invitee = checkSession();
    $acceptOrDeny = $h[2];
    if($acceptOrDeny == 1){
        echo(acceptBoardInviteData($boardName, $inviter, $invitee));
    }else if($acceptOrDeny == 0){
           echo(denyBoardInviteData($boardName, $inviter, $invitee));
    }
}

function canJoinBoardSvs($boardId){
    $userId = checkSession();
    echo(canJoinBoardData($boardId,$userId));
}

//HELPER FUNCTION
function checkSession() {
    if ( isset( $_SESSION["Logged"] )) {
        $sessionData = json_decode($_SESSION["Logged"]);
        //Get the ID
            $sessionData = $sessionData[0]->{'userId'};
            $cleanSession = $sessionData;
            $cleanSession = strip_tags($cleanSession);
            $cleanSession= htmlentities($cleanSession);
            return $cleanSession;
    }else{
        return "Error";
    }
}
?>