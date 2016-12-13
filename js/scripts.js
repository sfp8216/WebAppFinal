var checkChat;
var checkBoard;
var inviteTimeout;
var timerCheck = null;
var timerTimeout;
        var whiteboard = null;
$(document).ready(function() {
    //go getChat...
    //TEMPORARLILY LOAD BOARD       name,owner,status,size, title, borderColor
    checkExpiredLogin();
    checkLogin();
    $("#submitBtn").on('click', function(event) {
        checkLogin();
    });
    $("#newUserBtn").on('click', function(event) {
        createLogin();
    });
    $("#submitChat").on('click', function(event) {
        sendChat();
    });
    $("#logoutBtn").on('click', function(event) {
        logout();
    });
    $("#submitNewBoard").on('click',function(event){
        var boardName = $("#whiteboardNameInput").val();
        var publicBool = $('input[name=publicRadio]:checked', '#createWb').val();
        createBoard(boardName, publicBool);
        $("#newBoardModal").modal("hide");
        $("#listOfBoards").html("");
        getBoards();
    });
    $("#confirmInvite").on('click', function(event) {
        //TODO
        var boardId = $("svg").attr("data-id");
      	inviteToBoard(boardId, this.getAttribute("data-username"));
        $("#inviteModal").modal("hide");
    });

    $("#acceptBoardInviteBtn").on('click',function(event){
       acceptBoardInvite($(this).attr("data-id"));
       $("#acceptModal").modal("hide");
    });
    $("#denyBoardInviteBtn").on('click',function(event){
       denyBoardInvite($(this).attr("data-id"));
    });

  	$('#inviteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var recipient = button.data('username');
        var modal = $(this);
        modal.find('.modal-title').text('Invite ' + recipient);
        modal.find('.modal-body').text("Are you sure you want to invite " +
            recipient + "?");
        modal.find('#confirmInvite').attr("data-username", recipient);

    });

    	$('#acceptModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var recipient = button.data("id");

        var name = recipient.split("_")[1].split("|")[0];

        var modal = $(this);
        modal.find('.modal-title').text('Accept invitation to join  ' + name);
        modal.find('#acceptBoardInviteBtn').attr("data-id",recipient.split("_")[1]);
        modal.find('#denyBoardInviteBtn').attr("data-id",recipient.split("_")[1]);
        modal.find('.modal-body').text("Accept or Deny to join " +
            name + "?");
        modal.find('#acceptModal').attr("data-username", recipient);
    });
});
/*****************************
 *
 *
 *------- LOGIN ---------------
 *
 *
 *****************************/
function checkLogin() {
    //Token
    //UserAgent
    //TimeDate
    //userid
    var userAgent = navigator.userAgent;
    var timeNow = Date.now();
    var userName = $("#username").val();

    if(userName != null && userName != ""){
       // alert(userAgent+"|"+timeNow+"|"+userName);
    }

    var info = $("#username").val() + "|" + $("#password").val() + "|" + userAgent;




    myXhr('get', {
        method: 'checkLoginSvs',
        a: 'login',
        data: info
    }).done(function(json) {
        if(json[0].Error){
            alert("BAD INPUT TRY AGAIN");
        }else{ // No error with sanitzation
        $('#logged').html('Logged in: ' + json[0].Logged);
        var status = json[0].Logged;
        var token = json[0].Token;
        if($("#greetBanner")){
            $("#greetBanner").text("Welcome " + json[0].Username);
            $("#loggedInAs").html("Logged in as: " +json[0].Username);
        }
        if(token != null){
            if(token == "Browser"){
                if($("#tokenMessage")){
                    $("#tokenMessage").text("We've detected a new sign on location, this location will be stored");
                }
            }else if(token == "Date"){
                if($("#tokenMessage")){
                    $("#tokenMessage").text("It's been a while since you've logged on, welcome back!");
                }
            }
        }

        //Success
        //Fail
        //true
        if (status == "success" || status == "true") {
            var location = document.location.href.split("/")[document.location.href.split("/").length-1];
            if(location == "login.php" || location == "index.html"){
               window.location.href = "board.php";
            }
            //Remove chat lobbies
             // getChatLobbies();
            getUserList();
            checkInvites();
            getBoards();
        }else{
            alert("Failed");
        }
        //TODO make sessions and stuff
        }
    });
}

function createLogin() {
    var info = $("#newUser").val() + "|" + $("#newPass").val();
    myXhr('get', {
        method: 'createLoginSvs',
        a: 'login',
        data: info
    }).done(function(json) {
        if(json[0].Error){
             alert("There was an error with your inputs, please fix them");
        }
    });
}

function getUserList(lobbyId) {
    if (!lobbyId) {
        lobbyId = "0";
    }
    myXhr('get', {
        method: 'getUserListSvs',
        a: 'login',
        data: lobbyId
    }).done(function(json) {
        console.log(json);
        for (i = 0; i < json.length; i++) {
            if(json[i].status == 1){
                //User IS online
                     $("#userList").append(
                "<br><span class='userListItem' data-toggle='modal' data-target='#inviteModal' data-username=" +
                json[i].username + ">" + json[i].username + "</span><div class='statusOnline'></div>");
            }else{
                 $("#userList").append(
                "<br><span class='userListItem' data-toggle='modal' data-target='#inviteModal' data-username=" +
                json[i].username + ">" + json[i].username + "</span><div class='statusOffline'></div");
            }

        }
    });
}

function logout() {
    myXhr('get', {
        method: 'logoutSvs',
        a: 'login',
        data: "null"
    }).done(function(json) {
        if (json[0].Logout == "Success") {
            $("#logoutBtn").hide("");
            $("#submitBtn").show();
            //Redirect to login
            window.location.href = "login.php";
        }
    });
}
/*****************************
 *
 *
 *------- CHAT ---------------
 *
 *
 *****************************/
function canJoinLobby(lobbyId) {

}

function getChat(lobbyId) {
    myXhr('get', {
        method: 'getChatSvs',
        a: 'chat',
        data: lobbyId
    }).done(function(json) {
        //I'm back with good data...  Do something on the page!
        var h = '';
        for (i = 0; i < json.length; i++) {
            h += json[i].username + ': ' + json[i].message +'<br/>';
        }
        $("#chatRoom").html(h);
        if ($('#inputChat').css('display') == 'none') {
            $("#inputChat").show();
            $("#submitChat").attr("name", lobbyId);
        }
        checkChat = setTimeout(function() {
            getChat(lobbyId);
        }, 2000);
    });
}
function getChatUsers(lobbyId){
         var h="";

    myXhr('get',{method:"getChatUsersSvs",a:"chat",data:lobbyId}).done(function(json){

       	for (i = 0; i < json.length; i++) {
            h +="<br/><span style='font-weight:bold;'>"+json[i].username+'</span><br/>';
        }
       $("#chatRoomUsers").html(h);
    });
}

function getChatLobbies() {
    myXhr('get', {
        method: 'getChatLobbiesSvs',
        a: 'chat',
        data: "null"
    }).done(function(json) {
        var a = "<div class='list-group'>";
        for (i = 0; i < json.length; i++) {
            a +=
                "<button class='list-group-item list-group-item-action' onclick='canJoinLobby(" +
                json[i].lobbyId + ");'>" + json[i].name + ' ID: ' + json[i].lobbyId;
            if (json[i].public == "1") {
                a += " </button>";
            } else if (json[i].public == "0") {
                a += " <span class='glyphicon glyphicon-lock'></span></button><br/>";
            }
        }
        a += "</div>";
        $('#lobbies').append(a);
    })
}

function sendChat() {
    var lobbyId = $("#submitChat").attr("name");
    var chatMessage = $("#chatMessage").val();
    myXhr('get', {
        method: 'sendChatSvs',
        a: 'chat',
        data: lobbyId + "|" + chatMessage
    }).always(function(json) {
        $("#chatMessage").val('');
        getChat(lobbyId);
    });
}

function checkInvites() {
    if (inviteTimeout) {
        clearTimeout(inviteTimeout);
    }
    //Data USerId
    //TODO HARDCODED USERID, BAD
    //Getting it from session ID
    myXhr('get', {
        method: 'checkChatInviteSvs',
        a: 'chat',
        data: "null"
    }).done(function(json) {
        if(json.length == 0){
            $("#InviteStatus").html('You have no invites yet!');
        }
        for (i = 0; i < json.length; i++) {
            $("#InviteStatus").html("");
            //Check if already exists
            if (!document.getElementById("invite_" + json[i].name + "|" + json[i].username)) {
                var inviteNotification = document.createElement("button");
                inviteNotification.setAttribute("class", "btn btn-primary");
                inviteNotification.setAttribute("data-toggle","modal");
                inviteNotification.setAttribute("data-target","#acceptModal");
                inviteNotification.setAttribute("data-id", "invite_" + json[i].name + "|" +
                    json[i].username);
                    inviteNotification.setAttribute("id", "invite_" + json[i].name + "|" +
                    json[i].username);
                inviteNotification.appendChild(document.createTextNode(
                    "You've been invited to join " + json[i].name + " by " + json[i].username
                ));
                //Add accept listener
                $("#InviteStatus").append(inviteNotification);
                $("#InviteStatus").append("<br/>");
            }
        }

    });
    inviteTimeout = setTimeout(function() {
        checkInvites();
        //15 SECONDS
    }, 15000);
}

function inviteToBoard(boardId, userId) {
    //Data is
    // boardid|Inviter|Invited
    alert(boardId);
    myXhr('get', {
        method: 'inviteToBoardSvs',
        a: 'board',
        data: boardId+"|" + userId
    }).done(function(json) {
        alert("INVITED GUY");
    });
}
/*****************************
 *
 *
 *------- BOARD ---------------
 *
 *
 *****************************/
function canJoinBoard(boardId){
if (checkChat) {
        $("#inputChat").hide();
        $("#submitChat").removeAttr("name");
        clearTimeout(checkChat);
    }
  return myXhr("get",{method:"canJoinBoardSvs",a:"board",data:boardId});
}

function getBoards() {
    myXhr('get', {
        method: 'getBoardsSvs',
        a: 'board',
        data: "null"
    }).done(function(json) {
        var list = $("#listOfBoards");
        // Lock Icon

        var ul = document.createElement("div");
        ul.setAttribute("class", "list-group ");
        for (i = 0; i < json.length; i++) {
        var lockStatus = document.createElement("span");
        lockStatus.setAttribute("class","glyphicon glyphicon-lock");
            var li = document.createElement("button");
            li.setAttribute("data-id", json[i].boardId);
            li.setAttribute("data-name", json[i].name);
            li.setAttribute("data-lobby", json[i].lobbyId);
            li.setAttribute("data-public", json[i].public);
            li.setAttribute("class", "list-group-item list-group-item-action");
            li.append(document.createTextNode(json[i].name+"  "));
                if(li.getAttribute("data-public")==0){
                    li.append(lockStatus);
                }
            whiteboard = new Whiteboard("whiteboard", json[i].ownerId, json[i].public,
                "100%", json[i].name, "red", "boardSpace");
            li.addEventListener('click', function() {
                var lobbyId = this.getAttribute("data-lobby");
                var boardId = this.getAttribute("data-id");
                var boardName = this.getAttribute("data-name");
                var publicBool = this.getAttribute("data-public");
            //Check to see if the user can actually join the
                canJoinBoard(this.getAttribute("data-id"),boardId).done(function(json){
                    var allowed = json[0].allowed;
                    if(allowed){
                        var svgcanvas = document.getElementById("boardSpace");
        				while (svgcanvas.hasChildNodes()) {
        					svgcanvas.removeChild(svgcanvas.lastChild);
        				}
        				whiteboard.create(boardId,boardName,publicBool);
        				loadBoard(boardId);

                        var colorBtn = document.createElement("button");
                        colorBtn.innerHTML = "Change Color";
                        colorBtn.setAttribute("id","changeColor");
                        colorBtn.setAttribute("data-toggle","modal");
                        colorBtn.setAttribute("data-target","#colorsModal");
                        document.getElementById("boardSpace").appendChild(colorBtn);

        				//Load the whiteboard chat
        				clearTimeout(checkChat);
        				getChat(lobbyId);
                        getChatUsers(lobbyId);
                    }else{
                        alert("Sorry your not allowed to join");
                    }
                });

            });
            ul.append(li);
        }
        list.append(ul);
    });
}

function createBoard(boardName, publicBool) {
    myXhr('get', {
        method: 'createBoardSvs',
        a: 'board',
        data: boardName+"|"+publicBool
    }).done(function(json) {
    });
}

function deleteBoard() {
    myXhr('get', {
        method: 'deleteBoardSvs',
        a: 'board',
        data: "Bills board"
    }).done(function() {
        alert('hi');
    });
}
//data: gameId|userId
function checkExpiredLogin() {
    myXhr('get', {
        method: 'checkExpiredLoginSvs',
        a: 'login',
        data: ""
    }).done(function(json) {
        $('#turn').html('Your turn: ' + json[0].turn);
        if (!json[0].turn) setTimeout(getTurnGame, 2500);
    });
}
//Type: Path, Square, Etc
//Points: Points for the type
//id: identifier board|timestamp    saveBoardStroke("path",pathData,boardId,strokeId
function saveBoardStroke(type, points, boardId, strokeId,color) {
    myXhr('post', {
        method: "saveStrokeSvs",
        a: 'board',
        data: "path" + "|" + points + "|" + boardId + "|" + strokeId +"|"+color
    }).done(function(json) {
    });
}

function loadBoard(boardId) {
    if (checkBoard) {
        clearTimeout(checkBoard);
    }
    myXhr('get', {
        method: "loadBoardSvs",
        a: 'board',
        data: boardId
    }).done(function(json) {
        //Check boardLock
        checkLockStatus(boardId,"nill").done(function(json){
                 var locked= json[0].Locked;
            //if it is locked
            if(locked == "true"){
                $("#locked").text("Board in use please wait!").attr("class","");
                    var inUse = document.createElement("span");
                inUse.setAttribute("class","glyphicon glyphicon-pencil");
                $("#locked").append(inUse);
              //  alert("NO WRITE");
            }else{
                    if(locked == "timed"){
                     $("#locked").text("You are drawing...");
                     if(timerCheck == null){
                    var fiveMinutes = 30,
                    display = document.querySelector('#timer');
                    timerCheck = "checkedOut";
                  //  timerTimeout =startTimer(fiveMinutes, display);
                    }
                 }else if(locked == "false"){
                 $("#locked").text("Board Free!").attr("class","");
                 $("#timer").text('');

                    }
                 }
        });
        for (i = 0; i < json.length; i++) {
            //Check if element already exists
            var checkExist = document.getElementById(json[i].userid + "|" + json[i].boardId +
                "|" + json[i].strokeId);
            if (!checkExist) {
                var board = new SvgElement(json[i].userid + "|" + json[i].boardId + "|" +
                    json[i].strokeId, "path", json[i].points, json[i].color);
                board.create();
            }

        }
        checkBoard = setTimeout(function() {
            loadBoard(boardId);
        }, 2000);
    });
}

function acceptBoardInvite(input){
    myXhr("get",{method:"updateInviteSvs",a:'board',data:input+"|1"}).done(function(json){
      alert("Accepted it");
    });
}
function denyBoardInvite(input){
    myXhr("get",{method:"updateInviteSvs",a:'board',data:input+"|0"}).done(function(json){
      alert("Deny it");
    });
}

/*****************************
 *
 *
 *------- XHR SHORTCUT ---------------
 *
 *
 *****************************/
//////////////////////////////////
//XHR shortcut
//GetOrPost - which is it?
//d - data looks like: {name:val,name2:val2...}
//id - id of the holder if I want to put a spinner in it...
function myXhr(GetOrPost, d, id) {
    return $.ajax({
        type: GetOrPost,
        async: true,
        cache: false,
        url: 'mid.php',
        dataType: 'json',
        data: d,
        beforeSend: function() {
            //turn on spinner if id sent in
            if (id) {
                $(id).append('<img src="someSpinner.gif" class="spin"/>');
            }
        }
    }).always(function() {
        //kill spinner
        if (id) {
            $(id).find('img.spin').fadeOut(1500, function() {
                $(this).remove();
            });
        }
    }).fail(function() {
        //handle failure...
    });
}

//*STYLE SCRIPPTS**//

//scroll to the bottom
//$('#chatRoom').scrollTop($('#chatRoom')[0].scrollHeight);

//Timer
function startTimer(duration, display) {
    var start = Date.now(),
        diff,
        minutes,
        seconds;
    function timer() {
        // get the number of seconds that have elapsed since
        // startTimer() was called
        diff = duration - (((Date.now() - start) / 1000) | 0);

        // does the same job as parseInt truncates the float
        minutes = (diff / 60) | 0;
        seconds = (diff % 60) | 0;

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (diff <= 0) {
            // add one second so that the count down starts at the full duration
            // example 05:00 not 04:59
            start = Date.now() + 1000;
        }
    };
    // we don't want to wait a full second before the timer starts
    timer();
    setInterval(timer, 1000);
}