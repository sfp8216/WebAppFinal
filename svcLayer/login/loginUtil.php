<?php
function checkLoginSvs($data, $ip, $token) {
//$data: uname|password
//security
//should they be here.  Check security to see if the ip and token match this user...
    $h = strip_tags($data);
//prep data
//split data on |
    $h = explode('|', $data);
    $userId = $h[0];
    $passWord = md5($h[1]);
//include the bizData layer
//If already logged in with set session var, login
    if ( isset( $_SESSION["Logged"] )) {
        echo $_SESSION["Logged"];
    }
//If not logged in
    if ( $userId != "" && $passWord != "" ) {
        if ( !isset( $_SESSION["Logged"] )) {
            require_once ( 'BizDataLayer/checkLogin.php' );
            echo ( checkLoginData($userId, $passWord));
        }
    }
}
function createLoginSvs($data, $ip, $token) {
//$data: uname|password
//security
//should they be here.  Check security to see if the ip and token match this user...
    $h = strip_tags($data);
//prep data
//split data on |
    $h = explode('|', $data);
    $userId = $h[0];
    $passWord = md5($h[1]);
//include the bizData layer
    require_once ( 'BizDataLayer/createLogin.php' );
    echo ( createLoginData($userId, $passWord));
}
function getUserListSvs($data) {
    $lobbyId = $data;
    require_once ( 'BizDataLayer/checkLogin.php' );
    echo ( getUserListData($data));
}


function logoutSvs($data, $ip, $token) {
//SECURITY DO IT!
if(isset($_SESSION["Logged"])){
    $userId = json_decode($_SESSION["Logged"]);
     require_once ( 'BizDataLayer/checkLogin.php' );
     echo ( logoutData($userId[0]->{'userId'}));
}

}
?>