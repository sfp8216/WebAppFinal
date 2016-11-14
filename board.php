<!DOCTYPE html>
<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<!--BEGIN THE BOOTSTRAPPENING-->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<!-- SITE IMPORTS-->
<script type="text/javascript" src="js/Whiteboard.js"></script>
<script type="text/javascript" src="js/SvgElement.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>

<link rel="stylesheet" href="css/styles.css" />

  <title>TeachBoard - Login!</title>
</head>
<body class="container">
    <div class="text-center row">
        <h1>TeachBoard - The Syncable Whiteboard App!</h1>
    <br/>
    <br/>
        <h3>Welcome user!</h3>
        <div class="col-md-4 col-md-offset-8">
            <button type="button" class="btn btn-danger">Logout</button>
        </div>
    </div>
    <br/>
    <br/>
    <div class="row">
        <!--Whiteboard class -->
    <div class="col-md-6">
    <h2>List of Whiteboards!</h2>
        <!--Whiteboard list -->
        <div class="col-md-8" id="listOfBoards">

        </div>
        <!--Whiteboard create -->
         <div class="col-md-2">
            <button class="btn btn-success">Create a new Whiteboard!</button>
        </div>
    </div>
    <!--Invites  -->
    <div class="col-md-6 text-right">
        <h2>Pending Invites</h2>
            <!--Invites go here  -->
        <div id="InviteStatus">
        </div>
    </div>
    </div>
<br>
    <!--WHITEBOARD DIV-->
    <div class="row" style="border:1px solid black;">
        <div class="row">
            <div class="col-md-6">
                <h2 id="whiteBoardName">Select a Whiteboard to continue!</h2>
            </div>
            <div class="col-md-6">
               <h2 id="locked"></h2>
            </div>
        </div>
            <!--SVG GOES HERE  -->
        <div class="col-md-7" style="border:1px solid black;" id="boardSpace">
        </div>
            <!--CHAT STUFF  -->
        <div class="col-md-5" style="border:1px solid black;">
            <div class="row">
                <div class="col-md-5">
                    Chat Room:
                </div>
                <div class="col-md-4">
                    Users In Room:
                </div>
                <div class="col-md-3">
                    All Users:
                </div>
            </div>
            <div class="col-md-8 chatOverFlow" id="chatRoom">
            </div>
            <div class="col-md-2 chatOverFlow" id="chatRoomUsers">
            </div>
            <div class="col-md-2 chatOverFlow" id="userList">
            </div>
        <div class="col-md-12">
              <form method="post" name="postChat" id="inputChat" style="display:none;">
                 <div class="input-group">
              <span class="input-group-btn">
                <button class="btn btn-primary" type="button"  name="submitChat" id="submitChat" >Send</button>
              </span>
              <input type="text" class="form-control" id="chatMessage" placeholder="Send Chat...">
            </div>
        </form>
        </div>
        </div>
    </div>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
<!-- SEND INVITE MODAL -->
<div id="inviteModal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="exampleModalLabel">Confirm Invition?</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmInvite">Invite</button>
      </div>
    </div>
  </div>
</div>

<!-- ACCEPT INVITE MODAL -->
<div id="acceptModal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Accept Invitation?</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="acceptBoardInviteBtn">Accept</button>
        <button type="button" class="btn btn-danger" id="denyBoardInviteBtn">Deny</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>