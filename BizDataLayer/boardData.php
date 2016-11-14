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

function checkLockData($boardId, $overwrite,$userId){
    global $pdo;
    $sql = "SELECT Locked, Timelock, UserId FROM WHITEBOARD WHERE BOARDID = ?";
    try{
    if($stmt=$pdo->prepare($sql)){
        $stmt->bindParam(1,$boardId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

        $timelock = $results["Timelock"];
        $lockedToUser = $results["UserId"];
        $timeNow = new DateTime();

        $timeOUT = new DateTime($timelock);
        $timeOUT->modify("+ 30 Seconds");
if($overwrite != "1"){
    if($timelock != NULL){  // It is in use
        if($timeNow> $timeOUT){ // the time expired
        // Update the board with null status and return locked false
        $sql = "UPDATE whiteboard set Locked = null, Timelock = null, userid = null where boardid = ?";
            if($stmt=$pdo->prepare($sql)){
                $stmt->bindParam(1,$boardId);
                $stmt->execute();
                echo '[{"Locked":"false"}]';
               }
        }else{   // time didnt expire
                //Check if user is the same
                if($lockedToUser == $userId){
                     echo '[{"Locked":"timed"}]';
                }else{
                  echo '[{"Locked":"true"}]';
                }
        }
      }else{// Timelock is null
       echo '[{"Locked":"false"}]';
      }
    }else{
          $sql = "UPDATE whiteboard set Locked = null, Timelock = null, userid = null where boardid = ?";
            if($stmt=$pdo->prepare($sql)){
                $stmt->bindParam(1,$boardId);
                $stmt->execute();
                echo '[{"Locked":"false"}]';
        }
    }

        $stmt->close();
        $pdo->close();
    }
    }catch(Exception $e){
        return "Error";
    }
}

 function lockBoardData($boardId, $userId){
    global $pdo;
    $date = new DateTime();
    $sql = "UPDATE whiteboard SET locked = 1, userid = ?, timelock = ? WHERE boardId = ?";
    try{
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(1,$userId);
            $stmt->bindParam(2,$date->format('Y-m-d  H:i:s'));
            $stmt->bindParam(3, $boardId);
            $stmt->execute();
            $stmt->close();
            $pdo->close();
            echo '[{"Locked":"true"}]';
        }
    }catch(Exception $e){

    }
 }

//Createboard name, owner, public or private
//Creates a whiteboard and chatroom connected
function createBoardData($name, $owner, $public){
    global $pdo;
    $sql = "INSERT INTO chatlobby  (ownerId, name, public) VALUES(?,?,?);
    INSERT INTO whiteboard (ownerId, lobbyId, name, public) SELECT ?, lobbyId, ?, ? FROM chatlobby where name = ?;
    INSERT INTO CHATUSERS (lobbyid,userid) SELECT lobbyId, ownerId FROM chatlobby where name = ?";
    try{
        if($stmt=$pdo->prepare($sql)){
            $stmt->bindParam(1,$owner,PDO::PARAM_INT);
            $stmt->bindParam(2,$name);
            $stmt->bindParam(3,$public,PDO::PARAM_INT);
            $stmt->bindParam(4,$owner,PDO::PARAM_INT);
            $stmt->bindParam(5,$name);
            $stmt->bindParam(6,$public,PDO::PARAM_INT);
            $stmt->bindParam(7,$name);
            $stmt->bindParam(8,$name);
            $stmt->execute();
            $stmt-close();
            $pdo-close();
        }
    }catch(Exception $e){
        echo "asdas";
    }
}

//Delete whiteboard
function deleteBoardData($name, $owner){
    global $pdo;
    //Checks to make sure only owner can delete
    $sql = "DELETE FROM chatlobby where name = ? AND ownerid = ? ; DELETE FROM whiteboards where name = ? and ownerId = ?";
    try{
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(1,$name);
            $stmt->bindParam(2,$owner);
            $stmt->bindParam(3,$name);
            $stmt->bindParam(4,$owner);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            if($rowCount==1){
                echo "[{'Delete':'Successful'}]";
            }
        }
    }catch(Exception $e){
        echo "error";
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

function inviteToBoardData($boardId,$userId,$inviter){
    global $pdo;
    try{
    $sql = "SELECT userid from users where username = ?";
        if($stmt=$pdo->prepare($sql)){
            $stmt->bindParam(1,$userId);
            $stmt->execute();
            $userId = $stmt->fetchColumn(0);
        }
    $sql = "SELECT lobbyid from whiteboard where boardid = ?";
        if($stmt=$pdo->prepare($sql)){
            $stmt->bindParam(1,$boardId);
            $stmt->execute();
            $lobbyId = $stmt->fetchColumn(0);
        }
    $sql = "INSERT INTO invites (boardId, lobbyId, inviter,invitee) values(?,?,?,?)";
        if($stmt=$pdo->prepare($sql)){
            $stmt->bindParam(1,$boardId);
            $stmt->bindParam(2,$lobbyId);
            $stmt->bindParam(3,$inviter);
            $stmt->bindParam(4,$userId);
            $stmt->execute();
            $stmt->close();
            $pdo->close();
        }
    }catch(Exception $e){
        return "Error";
    }
}

function acceptBoardInviteData($boardName, $inviter, $invitee){
    echo "UPDATE invites inv INNER JOIN whiteboard wb ON wb.boardid = inv.boardid SET accepted = 1 Where wb.name = $boardName AND inv.invitee = $invitee AND accepted = 0";
    global $pdo;

    $sql = "UPDATE invites inv INNER JOIN whiteboard wb ON wb.boardid = inv.boardid SET accepted = 1 Where wb.name = ? AND inv.invitee = ?;";
    $sql .= "INSERT INTO CHATUSERS (lobbyid,userid) values (?,?)";
    try{
        $getboardId = "SELECT * from whiteboard where name = ?";
        if($stmt = $pdo->prepare($getboardId)){
            $stmt->bindParam(1,$boardName);
            $stmt->execute();
            $boardRow = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        }
        if($stmt = $pdo ->prepare($sql)){
            $stmt->bindParam(1,$boardName);
            $stmt->bindParam(2, $invitee);
            $stmt->bindParam(3,$boardRow["lobbyId"]);
            $stmt->bindParam(4,$invitee);
            $stmt->execute();
            $stmt->close();
            $pdo->close();

        }
    }catch(Exception $e){

    }
}

function canJoinBoardData($boardId,$userId){
    global $pdo;
    $sql = "SELECT * from whiteboard where boardId=?";
    try{
        if($stmt=$pdo->prepare($sql)){
            $stmt->bindParam(1,$boardId);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            if($results["public"] == 1){
                //It is public
                echo '[{"allowed":true}]';
            }if($results["public"] == 0){
                $sql = "SELECT * FROM chatusers where lobbyId = ? and userId = ?";
                if($stmt=$pdo->prepare($sql)){
                    $stmt->bindParam(1,$results["lobbyId"]);
                    $stmt->bindParam(2,$userId);
                    $stmt->execute();
                    $rowCount = $stmt->rowCount();
                    if($rowCount == 1){
                        echo '[{"allowed":true}]';
                    }else{
                        echo '[{"allowed":false}]';
                    }
                    $stmt->close();
                    $pdo->close();
                }
            }
        }
    }catch(Exception $e){
        echo '[{"allowed":"error"}]';
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