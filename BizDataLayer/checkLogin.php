<?php
//include dbInfo
require_once ( "../dbinfo.inc" );
//include exceptions
function checkLoginData($uName, $pWord) {
//probably need 2 args, game_id and user_id?
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT userId, username, password FROM users where username = :username and password = :password ');
        $stmt->execute(array( ':username' => $uName, ':password' => $pWord ));
        $count = $stmt->rowCount();
        if ( $count == 1 ) {
            $result = $stmt->fetch();
            $_SESSION['Logged'] = '[{"Logged":"true","userId":"' . $result['userId'] . '"}]';
            $stmt = $pdo->prepare('Update users set status=1 where username=:user and password=:pass');
            $stmt->execute(array( ':user' => $uName, ':pass' => $pWord ));
            $count = $stmt->rowCount();
            return '[{"Logged":"success","userId":"' . $result['userId'] . '"}]';
        }
        else {
            return '[{"Logged":"fail"}]';
        }
    }
    catch ( Exception $e ) {
        log_error($e, $sql, null);
        return '[{"error":"fail"}]';
    }
}
function getUserListData($lobbyId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT username from USERS where status = 1");
        $stmt->execute();
        return json_encode($stmt->fetchAll());
    }
    catch ( Exception $e ) {
        return "Error";
    }
}
function logoutData($userId) {
    global $pdo;
//TODO
//IMPLEMENT THIS
    if ( isset( $_SESSION["Logged"] )) {
        try {
            $stmt = $pdo->prepare("UPDATE users set status = 0 where userId=:username");
            $stmt->bindParam(":username", $userId);
            $stmt->execute();
        }
        catch ( Exception $e ) {
            return "Error";
        }
        $_SESSION["Logged"] = "";
        session_destroy();
        return '[{"Logout":"Success"}]';
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
    $bindVarsArray = array( );
//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
    while ( $column = $meta->fetch_field()) {
        $bindVarsArray[] = & $results[$column->name];
    }
//bind it!
    call_user_func_array(array( $stmt, 'bind_result' ), $bindVarsArray);
//now, go through each row returned,
    while ( $stmt->fetch()) {
        $clone = array( );
        foreach ( $results as $k => $v ) {
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