<?php
//include dbInfo
require_once ( "../dbinfo.inc" );
//include exceptions
require_once ( 'exception.php' );


function getBoardsData(){
    global $pdo;
    //CHECK IF LOGGED IN
    $sql = "Select * FROM whiteboard";
    try{
        if($stmt=$pdo->prepare($sql)){
            echo returnJson($stmt);
          }
    }catch(Exception $e){
        return "Error";
    }
}

function saveStrokeData($type, $points,$boardId, $strokeId, $userId) {
    global $pdo;
    $sql = "INSERT INTO whiteboarddata(boardId, shape, points, userid, strokeId) VALUES (?,?,?,?,?)";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1,$boardId);
            $stmt->bindParam(2,$type);
            $stmt->bindParam(3,$points);
            $stmt->bindParam(4,$userId);
            $stmt->bindParam(5,$strokeId);
            $stmt->execute();
            $stmt->close();
            $pdo->close();
        }
        else
            if ( !$points ) {
                throw new Exception("Error with boarddata");
            }
    }
    catch ( Exception $e ) {
        log_error($e, $sql, null);
        echo '[{error":error"}]';
    }
}
function loadBoardData($boardId) {
    global $pdo;
    $sql = "SELECT * FROM whiteboarddata WHERE boardid=? ORDER BY time";
    try {
        if ( $stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1,$boardId);
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

function clearBoardData($boardId){
    global $pdo;
    $sql = "DELETE FROM whiteboarddata WHERE boardid = ?";
    try{
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(1,$boardId);
            echo returnJson($stmt);
            $stmt->close();
            $pdo->close();

        }
    }catch(Exception $e){
        echo "error";
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