<?php
//include dbInfo
require_once ( "../dbinfo.inc" );
//include exceptions
require_once ( 'exception.php' );
function getChatLobbiesData() {
    global $pdo;
    $sql = "Select * from chatlobby";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            echo returnJson($stmt);
            $stmt->close();
            $pdo->close();
        }
        else
            if ( !$data ) {
                throw new Exception("Error with chat lobbies");
            }
    }
    catch ( Exception $e ) {
        log_error($e, $sql, null);
        echo '[{error":error"}]';
    }
}
function getChatData($lobbyId) {
//probably need 2 args, game_id and user_id?
    global $pdo; //import the var into the function...
    $sql = "Select lobbyId, username, time, message from chatdata INNER JOIN users on chatdata.userId=users.userId WHERE chatdata.lobbyId=? ORDER BY time";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1, $lobbyId, PDO::PARAM_STR);
            echo returnJson($stmt);
            $stmt->close();
            $pdo->close();
        }
        else
            if ( !$data ) {
                throw new Exception("An error occurred while fetching data");
            }
    }
    catch ( Exception $e ) {
        log_error($e, $sql, null);
        echo '[{"error":"fail"}]';
    }
}
function sendChatData($lobby, $user, $message) {
//probably need 2 args, game_id and user_id?
    global $pdo; //import the var into the function...
    $sql = "Insert into chatdata (lobbyId, userId, message, time) VALUES (:lobby, :userId, :message, :time)";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            $timenow = new DateTime();
            $timenow->format('Y-m-d H:i:s');
            echo $lobby . " " . $user . " " . $message;
            $stmt->execute(array( ':lobby' => $lobby, ':userId' => $user, ':message' => $message, ':time' => $timenow->format('Y-m-d H:i:s')));
            $stmt->close();
            $pdo->close();
        }
        else
            if ( !$data ) {
                throw new Exception("An error occurred while fetching data");
            }
    }
    catch ( Exception $e ) {
        log_error($e, $sql, null);
        echo '[{"error":"fail"}]';
    }
}
function checkChatInviteData($data) {
    global $pdo;
    $sql = "SELECT usr.username, wb.name, cu.datetime FROM invites cu INNER JOIN users usr on cu.inviter = usr.userId LEFT JOIN whiteboard wb on cu.boardId = wb.boardId WHERE cu.accepted=0 AND  cu.invitee = ?";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1, $data);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $answer = "";
            $rowCount = $stmt->rowCount();
            echo json_encode($results);
        }
    }
    catch ( Exception $e ) {
        echo "sadness";
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
    return json_encode($stmt->fetchAll());
}
?>