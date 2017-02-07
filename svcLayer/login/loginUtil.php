<?php
require_once ("svcLayer/security/security.php");
function tokenize($userAgent, $lAndF) {
	//First MD5 encrypt the useragent
	//"Minify" user agent by getting every other char
	$newUserAgent = "";
	for($i = strlen($userAgent) - 1; $i >= 0; $i -= 2) {
		$newUserAgent = $newUserAgent . $userAgent[$i];
	}
	$newUserAgent = md5($newUserAgent);
	//Next, get the current time and base64 encode that
	$timeNow = base64_encode(date("Y-m-d H:i:s"));
	//Convert the Last and First letters to ASCII chars
	$asciNum = ord(strtolower($lAndF[0])) - 96;
	$asciNum2 = ord(strtolower($lAndF[1])) - 96;
	$stringNum = $asciNum . "|" . $asciNum2;
	//Base 64 the whole thing
	$completedToken = base64_encode($newUserAgent . "|" . $timeNow . "|" . $stringNum);
	return $completedToken;
}
function unTokenize($token) {
	//First unbase 64Encode
	$decrypt = base64_decode($token);
	//Explode the results
	$decrypt = explode("|", $decrypt);
	$userAgent = $decrypt[0];
	$time = $decrypt[1];
	$l = $decrypt[2];
	$f = $decrypt[3];
	//echo "User Agent is : ".$userAgent."<br><br>";
	//echo "Time is : ".$time ."<br><br>"           ;
	//echo "L  is : ".    $l  ."<br><br>"            ;
	//echo "F  is : ".  $f    ."<br><br>"             ;
	//Convert Time back to time
	$time = base64_decode($time);
	//Convert L back to letter
	$l = chr($l + 96);
	//Convert F back to letter
	$f = chr($f + 96);
	//Take the 2 numbers off the back
	$completedDecrypt = $userAgent . "|" . $time . "|" . $l . "|" . $f;
	return $completedDecrypt;
}
function checkLoginSvs($data, $ip, $token) {
	//$data: uname|password
	//security
	$h = filterBy($data, "Text");
	//prep data
	//split data on |
	$h = explode('|', $data);
	$userId = $h[0];
	$passWord = md5($h[1]);
	/*Sanitize Inputs*/
	//Token Here
	//    if(!isset($_COOKIE["TeachBoardLoginCookie"])){
	$token = $h[2];
	$lAndF = substr($userId, - 1);
	$lAndF .= $userId[0];
	$checkToken = tokenize($token, $lAndF);
	/*Sanitize Token*/
	//    setcookie("TeachBoardLoginCookie",$checkToken);
	//    }else{
	//Cookie IS set
	//        $checkToken = $_COOKIE["TeachBoardLoginCookie"];
	//   }
	//If already logged in with set session var, login
	if(isset($_SESSION["Logged"])) {
		echo $_SESSION["Logged"];
	}
	//If not logged in
	if($userId != "" && $passWord != "") {
		if(!isset($_SESSION["Logged"])) {
			$userId = filterBy($userId, "Text", 20);
			$passWord = filterBy($passWord, "Text", 50);
			$checkToken = filterBy($checkToken, "Text", 150);
			if($userId != "error" && $passWord != "error" && $checkToken != "error") {
				require_once ('BizDataLayer/checkLogin.php');
				echo (checkLoginData($userId, $passWord, $checkToken));
			}
			else{
				echo '[{"Error":"validate"}]';
			}
		}
	}
}
function createLoginSvs($data) {
	//$data: uname|password
	$h = filterBy($data, "Text");
	$h = explode('|', $data);
	$userId = $h[0];
	$passWord = md5($h[1]);
	/*Sanitize Inputs*/
	$userId = filterBy($userId, "Text", 20);
	$passWord = filterBy($passWord, "Text", 50);
	//If there are no sanitization errors
	if($userId != "error" && $passWord != "error") {
		require_once ('BizDataLayer/createLogin.php');
		echo (createLoginData($userId, $passWord));
	}
	else{
		echo '[{"Error":"validate"}]';
	}
}
function getUserListSvs($data) {
	$lobbyId = $data;
	require_once ('BizDataLayer/checkLogin.php');
	echo (getUserListData($data));
}
function logoutSvs($data, $ip, $token) {
	//SECURITY DO IT!
	if(isset($_SESSION["Logged"])) {
		$userId = json_decode($_SESSION["Logged"]);
		require_once ('BizDataLayer/checkLogin.php');
		echo (logoutData($userId[0]->{'userId'}));
	}
}
function checkExpiredLoginSvs() {
	//Auto function to make sure users that are inactive get logged out
    //This will run when ANYONE logs in
	require_once ('BizDataLayer/checkLogin.php');
	echo (checkExpiredLoginData());
}
?>