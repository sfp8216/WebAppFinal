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
<script type="text/javascript" src="js/uiStyles.js"></script>


<link rel="stylesheet" href="css/styles.css" />
  <title>TeachBoard - Board!</title>
</head>
<body class="container">
    <div class="tbContainer">
    <div class="text-center row whBg">
        <h1 class="headerTitle">TeachBoard - The Syncable Whiteboard App!</h1>
        <h5>Web Application Final Project</h5>
<hr>
        <h3 id="greetBanner">Welcome user!</h3>
        <h5 id="tokenMessage"></h5>
           <!--Whiteboard create -->
         <div class="col-md-4">
            <button class="btn btn-success"  data-toggle='modal' data-target='#newBoardModal'>Create a new Whiteboard!</button>
            <button class="btn btn-info"  data-toggle='modal' data-target='#helpModal'>?</button>
        </div>
          <div class="col-md-2 col-md-offset-4 text-right">
            Logged in as: <span id="loggedInAs">User</span>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger" id="logoutBtn">Logout</button>
        </div>
    </div>
    <div class="row whBg">
        <!--Whiteboard class -->
    <div class="col-md-6 whBg">
    <h2>Whiteboards:</h2>
        <!--Whiteboard list -->
        <div class="col-md-8" id="listOfBoards">
        </div>

    </div>
    <!--Invites  -->
    <div class="col-md-6 text-center">
        <h2>Pending Invites</h2>
            <!--Invites go here  -->
        <div id="InviteStatus">
        You have no pending invites.
        </div>
    </div>
    </div>
    <!--WHITEBOARD DIV-->
    <div class="row whBg" id="whiteboardDiv">
    <hr/>
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <h2 id="whiteBoardName">Select a Whiteboard!</h2>
            </div>
            <div class="col-md-6">
               <h2 id="locked"></h2>
               <span id="timer"></span>
            </div>
        </div>
            <!--SVG GOES HERE  -->
        <div class="col-md-7" id="boardSpace">
        </div>
            <!--CHAT STUFF  -->
        <div class="col-md-5 chatDiv">
            <div class="row">
                <div class="col-md-6">
                    Chat Room:
                </div>
                <div class="col-md-3">
                    In Room:
                </div>
                <div class="col-md-3">
                    All Users:
                </div>
            </div>
            <div class="col-md-6 chatOverFlow" id="chatRoom">
            </div>
            <div class="col-md-3 chatOverFlow" id="chatRoomUsers">
            </div>
            <div class="col-md-3 chatOverFlow" id="userList">
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
         <h4 class="modal-title" id="exampleModalLabel">Confirm Invitation?</h4>
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
        <!--<button type="button" class="btn btn-danger" id="denyBoardInviteBtn">Deny</button>-->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="newBoardModal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Create new Whiteboard</h4>
      </div>
      <div class="modal-body">
        <form  id="createWb" name="createWb"  class="form-horizontal">
          <div class="form-group">
            <label for="whiteboardNameInput" id="newBoardName" class="col-sm-2 control-label">Board Name:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="whiteboardNameInput" placeholder="Your Whiteboard">
            </div>
          </div>
          <div class="form-group">
            <label for="privacyRadio" class="col-sm-2 control-label">Privacy</label>
            <div class="col-sm-10">
               <label class="radio-inline"><input type="radio" name="publicRadio" checked value=1>Public</label>
               <label class="radio-inline"><input type="radio" name="publicRadio" value=0>Private</label><br>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
               <button id="submitNewBoard" name="submitNewBoard" class="btn btn-success" type="button">Create</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="colorsModal" class="modal colorModal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">Select a Color</h4>
      </div>
      <div class="modal-body">
          <div class="row">
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:Red";>RED</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:ORANGE";>ORANGE</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:YELLOW";>YELLOW</div></div>
          </div>
          <div class="row">
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:GREEN";>GREEN</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:BLUE";>BLUE</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:PURPLE";>PURPLE</div></div>
          </div>
          <div class="row">
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:BLACK";>BLACK</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:WHITE";>WHITE</div></div>
          <div class=" col-md-4"><div class="pickColor" data-dismiss="modal" style="background:GREY";>GREY</div></div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="deleteBoardModal" class="modal colorModal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">Are you sure you want to delete the board?</h4>
        <h5 class="modal-title text-center">This will delete all chat logs too and cannot be undone</h5>
      </div>
      <div class="modal-body">
          <div class="row">
          <div class=" col-md-6"><button type="button" class='btn btn-primary' id="confirmDeleteBoard">Confirm</button></div>
          <div class=" col-md-6"><button class="btn btn-primary" data-dismiss="modal">Cancel</button></div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="helpModal" class="modal helpModal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">Help</h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
          <p>
                <h2>Welcome to Teachboard!</h2>
                <span class="helpHeader">How to create a whiteboard:</span>
                Click the "Create a new Whiteboard" button. Enter in a valid name and public or private status then click submit.
                <br>
                Once completed, the board will be selectable on the list!
                <br>
                <br>
                <span class="helpHeader">Joining a Whiteboard:</span>
                <br>
                To Join a Whiteboard, please select one from the scrolling list.
                <br>
                You will only see Whiteboards listed that are public, that you have created, or have been invited to join and accepted.
                <br>
                <br>
                <span class="helpHeader">How to draw on the Whiteboard:</span>
                <br>
                Once you select a whiteboard, if the notification message says "Board Free", then go ahead and click and drag to draw!
                <br>
                If the message says "Board in use please wait" somebody else is drawing.
                <br>
                When you can draw, picking the "Change Color" button will display a popup that will allow you to select your color.
                <br>
                The - and + signs on the buttons will increase or decrease the size of your brush when you paint.
                <br>
                You may at anytime clear the board, as long as it is your turn to draw.
                <br>
                If you are the boards creator, you have the option to delete the board you've created. This will also delete any chat logs.
                <br>
                <br>
                <span class="helpHeader">How to invite people to a private board:</span>
                <br>
                Once you've selected a board from the list, under the "All Users" column, select the user you wish to invite to your whiteboard.<br>
                Please note that the user may decline or ignore your invitation.
                <br>
                Once the user confirms the invitation, they will be able to draw in your private whiteboard!
            </p>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
</div>
</body>
</html>