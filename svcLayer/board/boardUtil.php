<?php
//ALL game goes in this folder
require_once ( 'BizDataLayer/boardData.php' );

function getBoardsSvs(){
    //SECURITY
    echo(getBoardsData());
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

    echo (saveStrokeData($type,$points,$boardId, $strokeId, $userId));
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