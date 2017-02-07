var checkChat;
var checkBoard;
var inviteTimeout;
var timerCheck = null;
var timerTimeout;
var whiteboard = null;
$(document).ready(function () {
        checkExpiredLogin();
        checkLogin();
        $("#submitBtn").on('click', function (event) {checkLogin();});
        $("#newUserBtn").on('click', function (event) {
            if($("#newUser").val() != "" && $("#newPass").val()!= ""){
            createLogin();
            }
            });
        $("#submitChat").on('click', function (event) {sendChat();});
        $("#logoutBtn").on('click', function (event) {logout();});

        $("#submitNewBoard").on('click', function (event) {
                var boardName = $("#whiteboardNameInput").val();
                if(boardName != ""){
                var publicBool = $('input[name=publicRadio]:checked', '#createWb').val();
                createBoard(boardName, publicBool);
                $("#newBoardModal").modal("hide");
                }
            });

        $("#confirmInvite").on('click', function (event) {
                var boardId = $("svg").attr("data-id");
                inviteToBoard(boardId, this.getAttribute("data-username"));
                $("#inviteModal").modal("hide");
                checkInviteHistory();
            });

        $("#confirmUninvite").on('click', function (event) {
                 removeFromBoard(this.getAttribute("data-name"), this.getAttribute("data-username"));
                $("#uninviteModal").modal("hide");
                getBoards();
                checkInviteHistory();
            });

        $("#acceptBoardInviteBtn").on('click', function (event) {
                acceptBoardInvite($(this).attr("data-id"));
                $("#acceptModal").modal("hide");
                checkInviteHistory();
            });

        $("#denyBoardInviteBtn").on('click', function (event) {
                denyBoardInvite($(this).attr("data-id"));
            });

        $('#inviteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var recipient = button.data('username');
                var modal = $(this);
                modal.find('.modal-title').text('Invite ' + recipient);
                modal.find('.modal-body').text("Are you sure you want to invite " +
                        recipient + "?");
                modal.find('#confirmInvite').attr("data-username", recipient);
            });
            //Uninvite modal
                $('#uninviteModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var recipient = button.data('username');
                var boardName = button.data('board');
                var modal = $(this);
                modal.find('.modal-title').text('Uninvite ' + recipient);
                modal.find('.modal-body').text("Are you sure you want to uninvite " +
                        recipient + " from " + boardName + " ?");
                modal.find('#confirmUninvite').attr("data-username", recipient);
                modal.find('#confirmUninvite').attr("data-name", boardName);
            });

        $('#acceptModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget)
                var recipient = button.data("id");
                var name = recipient.split("_")[1].split("|")[0];
                var modal = $(this);
                modal.find('.modal-title').text('Accept invitation to join  ' + name);
                modal.find('#acceptBoardInviteBtn').attr("data-id", recipient.split("_")[1]);
                modal.find('#denyBoardInviteBtn').attr("data-id", recipient.split("_")[1]);
                modal.find('.modal-body').text("Do you want to accept to join " +
                        name + "?");
                modal.find('#acceptModal').attr("data-username", recipient);
            });

        $("#confirmDeleteBoard").on('click',function(event){
           var id = $("#whiteBoardName").text();
           deleteBoard(id);
        });
        //Button to go back to main lobby
        $("#backbtn").on('click',function(event){
            $("#whiteBoardName").html("Select a Whiteboard!");
            $("#locked").text("Main Lobby");
            $("#chatRoomUsers").text("");
            $("#boardSpace").html("");
            clearTimeout(checkBoard);
            clearTimeout(checkChat);
            getChat(-1);
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
    var info = $("#username").val() + "|" + $("#password").val() + "|" + userAgent;

    myXhr('post', {
            method: 'checkLoginSvs',
            a: 'login',
            data: info
        }).done(function (json) {
            if (json[0].Error || json[0].Logged == "fail") {
                    window.location.href = "login.php";
            } else { // No error with sanitzation
                $('#logged').html('Logged in: ' + json[0].Logged);
                var status = json[0].Logged;
                var token = json[0].Token;
                if ($("#greetBanner")) {
                    $("#greetBanner").text("Welcome " + json[0].Username);
                    $("#loggedInAs").html(json[0].Username);
                    //Public chat
                    clearTimeout(checkChat);
                    getChat(-1);
                }
                if (token != null) {
                    if (token == "Browser") {
                        if ($("#tokenMessage")) {
                            $("#tokenMessage").text("We've detected a new sign on location, this location will be stored");
                        }
                    } else if (token == "Date") {
                        if ($("#tokenMessage")) {
                            $("#tokenMessage").text("It's been a while since you've logged on, welcome back!");
                        }
                    }
                }
                //Success
                //Fail
                //true
                if (status == "success" || status == "true") {
                    var location = document.location.href.split("/")[document.location.href.split("/").length - 1];
                    if (location == "login.php" || location == "index.html") {
                        window.location.href = "board.php";
                    }
                    //Remove chat lobbies
                    getUserList();
                    checkInviteHistory();
                    checkInvites();
                    getBoards();
                } else {
                    var loginError = $("#loginError");
                    loginError.html("Your username or password was incorrect, please try again");
                    loginError.removeClass("hidden");
                }
            }
        });
}

function createLogin() {
    var info = $("#newUser").val() + "|" + $("#newPass").val();
    myXhr('post', {
            method: 'createLoginSvs',
            a: 'login',
            data: info
        }).done(function (json) {
            if (json[0].Error) {
                var loginError = $("#loginError");
                    loginError.html("Username and password cannot be blank - or already exists.");
                    loginError.removeClass("hidden");
              //  alert("There was an error with your inputs, please fix them");
            }else{
                if(json[0].Create != "fail"){
                                window.location.href = "board.php";
                }
            var loginError = $("#loginError");
                    loginError.html("Account name already taken - Or your inputs are invalid");
                    loginError.removeClass("hidden");
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
        }).done(function (json) {
            for (i = 0; i < json.length; i++) {
                var currentUser = $("#loggedInAs").text();
                if(json[i].username == currentUser){
                    $("#userList").append(
                    "<br><span class='userListItem no-click'>" + json[i].username + "</span><div class='statusOnline'></div>");
                }else if (json[i].status == 1) {
                    //User IS online
                    $("#userList").append(
                            "<br><span class='userListItem' data-toggle='modal' data-target='#inviteModal' data-username=" +
                            json[i].username + ">" + json[i].username + "</span><div class='statusOnline'></div>");
                } else {
                    $("#userList").append(
                            "<br><span class='userListItem' data-toggle='modal' data-target='#inviteModal' data-username=" +
                            json[i].username + ">" + json[i].username + "</span><div class='statusOffline'></div");
                }
            }
            var currentUsers = $("#chatRoomUsers span");
            if(currentUsers.length > 0){
                for(var i = 0; i < currentUsers.length; i++){
                    if($.inArray(currentUsers[i].innerHTML,$(".userListItem").text())){
                     $(".userListItem[data-username='"+currentUsers[i].innerHTML+"']").removeAttr("data-toggle").addClass("no-click");
                    }
                }
            }
            //Disable buttons if no board is loaded

            if(currentUsers.length == 0){
                $(".userListItem").removeAttr("data-toggle").addClass("no-click");
            }else{
                $(".userListItem").removeClass("no-click");
            }

        });
}

function logout() {
    myXhr('post', {
            method: 'logoutSvs',
            a: 'login',
            data: "null"
        }).done(function (json) {
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
        }).done(function (json) {
            var h = '';
            for (i = 0; i < json.length; i++) {
                h += json[i].username + ': ' + json[i].message + '<br/>';
            }
            $("#chatRoom").html(h);
            if ($('#inputChat').css('display') == 'none') {
                $("#inputChat").show();
                $("#submitChat").attr("name", lobbyId);
            }
            checkChat = setTimeout(function () {
                getChat(lobbyId);
            }, 2000);
        });
}

function getChatUsers(lobbyId) {
    var h = "";
    myXhr('get', {
            method: "getChatUsersSvs",
            a: "chat",
            data: lobbyId
        }).done(function (json) {
            for (i = 0; i < json.length; i++) {
                h += "<br/><span style='font-weight:bold;'>" + json[i].username + '</span><br/>';
                if($.inArray(json[i].username), $(".userListItem").text()){
                    $(".userListItem[data-username='"+json[i].username+"'").removeAttr("data-toggle").addClass("no-click");
                }
            }
            $("#chatRoomUsers").html(h);
        });
}

function getChatLobbies() {
    myXhr('get', {
            method: 'getChatLobbiesSvs',
            a: 'chat',
            data: "null"
        }).done(function (json) {
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
            $('#lobbies').appendChild(a);
        })
}

function sendChat() {
    clearTimeout(checkChat);
    var lobbyId = $("#submitChat").attr("name");
    var chatMessage = $("#chatMessage").val();
    myXhr('post', {
            method: 'sendChatSvs',
            a: 'chat',
            data: lobbyId + "|" + chatMessage
        }).always(function (json) {
            $("#chatMessage").val('');
            getChat(lobbyId);
        });
}

function checkInviteHistory(){
    myXhr('get',{
        method:"checkInviteHistorySvs",
        a:"chat",
        data:"null"
    }).done(function(json){
        if(json.length > 0){
            $("#inviteHistory").html('');
            var inviteText = "<table class='table tableoverflow'><tr><th>Name</th><th>Board</th><th>Status</th></tr>";
            for(i = 0; i < json.length;i++){
                inviteText += "<tr><td>"+ json[i].username + "</td><td>" + json[i].name + "</td>";
                if(json[i].accepted == 1){
                    inviteText += "<td style='color:green;font-weight:bold;cursor:pointer;' data-toggle='modal' data-target='#uninviteModal' data-username=" +
                            json[i].username +" data-board=" +
                            json[i].name +">Accepted</td></tr>";
                }else{
                      inviteText += "<td style='color:red;'>Pending</td></tr>";
                }
            }
                inviteText += "</table>";
                $("#inviteHistory").append(inviteText);
        }
    });
}

function checkInvites() {
    if (inviteTimeout) {
        clearTimeout(inviteTimeout);
    }
    //Data USerId
    myXhr('get', {
            method: 'checkChatInviteSvs',
            a: 'chat',
            data: "null"
        }).done(function (json) {
            if (json.length == 0) {
                $("#InviteStatus").html('You have no invites yet!');
            }else{
                $("#InviteStatus").html("");
            }
            for (i = 0; i < json.length; i++) {
                //Check if already exists
                if (!document.getElementById("invite_" + json[i].name + "|" + json[i].username)) {
                    var inviteNotification = document.createElement("button");
                    inviteNotification.setAttribute("class", "btn btn-primary");
                    inviteNotification.setAttribute("data-toggle", "modal");
                    inviteNotification.setAttribute("data-target", "#acceptModal");
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
    inviteTimeout = setTimeout(function () {
        checkInvites();
        //Retrieves invite history
        checkInviteHistory();
        //Checks for new boards
        getBoards();
        //15 SECONDS
    }, 15000);
}

function inviteToBoard(boardId, userId) {
    //Data is
    // boardid|Inviter|Invited
    myXhr('post', {
            method: 'inviteToBoardSvs',
            a: 'board',
            data: boardId + "|" + userId
        }).done(function (json) {
        });
}

function removeFromBoard(boardName, userId) {
    //Data is
    // boardid|Inviter|Invited
    myXhr('get', {
            method: 'removeFromBoardSvs',
            a: 'board',
            data: boardName + "|" + userId
        }).done(function (json) {
        });
}
/*****************************
 *
 *
 *------- BOARD ---------------
 *
 *
 *****************************/
function canJoinBoard(boardId) {
    if (checkChat) {
        $("#inputChat").hide();
        $("#submitChat").removeAttr("name");
        clearTimeout(checkChat);
    }
    return myXhr("get", {
        method: "canJoinBoardSvs",
        a: "board",
        data: boardId
    });
}

function getBoards() {
    myXhr('get', {
            method: 'getBoardsSvs',
            a: 'board',
            data: "null"
        }).done(function (json) {
        var list = $("#listOfBoards");
            // Lock Icon
            list.html("");
            var ul = document.createElement("div");
            ul.setAttribute("class", "list-group ");
            for (i = 0; i < json.length; i++) {
                var lockStatus = document.createElement("span");
                lockStatus.setAttribute("class", "glyphicon glyphicon-lock");
                var li = document.createElement("button");
                li.setAttribute("data-id", json[i].boardId);
                li.setAttribute("data-name", json[i].name);
                li.setAttribute("data-lobby", json[i].lobbyId);
                li.setAttribute("data-public", json[i].public);
                li.setAttribute("class", "list-group-item list-group-item-action");
                li.appendChild(document.createTextNode(json[i].name + "  "));
                if (li.getAttribute("data-public") == 0) {
                    li.appendChild(lockStatus);
                }
                whiteboard = new Whiteboard("whiteboard", json[i].ownerId, json[i].public,
                    "100%", json[i].name, "black", "boardSpace");
                li.addEventListener('click', function () {
                    var lobbyId = this.getAttribute("data-lobby");
                    var boardId = this.getAttribute("data-id");
                    var boardName = this.getAttribute("data-name");
                    var publicBool = this.getAttribute("data-public");
                    //Check to see if the user can actually join the
                    canJoinBoard(this.getAttribute("data-id"), boardId).done(function (json) {
                            var allowed = json[0].allowed;
                            if (allowed) {
                                var svgcanvas = document.getElementById("boardSpace");
                                while (svgcanvas.hasChildNodes()) {
                                    svgcanvas.removeChild(svgcanvas.lastChild);
                                }
                                whiteboard.create(boardId, boardName, publicBool);
                                loadBoard(boardId);
                                //Add toolbarDiv
                                document.getElementById("boardSpace").appendChild(createToolbar(boardId,json[0].Owner));

                                //If board Owner load Delete Board button


                                //Load the whiteboard chat
                                clearTimeout(checkChat);
                                getChat(lobbyId);
                                getChatUsers(lobbyId);
                            } else {
                                alert("Sorry your not allowed to join");
                            }
                        });
                });
                ul.appendChild(li);
            }
            list.append(ul);
        });

}

function createBoard(boardName, publicBool) {
    myXhr('post', {
            method: 'createBoardSvs',
            a: 'board',
            data: boardName + "|" + publicBool
        }).done(function (json) {
            getBoards();
        });
}
function deleteBoard(boardName) {
    myXhr('post', {
            method: 'deleteBoardSvs',
            a: 'board',
            data: boardName
        }).done(function (json) {
            if(json[0].Delete == "Successful"){
            location.reload();
            }
        });
}

function checkExpiredLogin() {
    myXhr('get', {
            method: 'checkExpiredLoginSvs',
            a: 'login',
            data: ""
        }).done(function (json) {
        });
}
//Type: Path, Square, Etc
//Points: Points for the type
//id: identifier board|timestamp    saveBoardStroke("path",pathData,boardId,strokeId,color, brushSize
function saveBoardStroke(type, points, boardId, strokeId, color, brushSize) {
    myXhr('post', {
            method: "saveStrokeSvs",
            a: 'board',
            data: "path" + "|" + points + "|" + boardId + "|" + strokeId + "|" + color + "|" + brushSize
        }).done(function (json) {});
}

function loadBoard(boardId) {
    if (checkBoard) {
        clearTimeout(checkBoard);
    }
    myXhr('get', {
            method: "loadBoardSvs",
            a: 'board',
            data: boardId
        }).done(function (json) {
            if(json == ""){
               $("#whiteboard").html("");
            }
            //Check boardLock
            checkLockStatus(boardId, "nill").done(function (json) {
                    var locked = json[0].Locked;
                    //if it is locked
                    if (locked == "true") {
                        $("#locked").text(json[0].Username+" is drawing please wait!").attr("class", "");
                        $("#toolBar button").prop("disabled",true);
                        var inUse = document.createElement("span");
                        inUse.setAttribute("class", "glyphicon glyphicon-pencil");
                        $("#locked").append(inUse);
                        //  alert("NO WRITE");
                    } else {
                        if (locked == "timed") {
                            $("#locked").text("You are drawing...");
                            if (timerCheck == null) {
                                var fiveMinutes = 30,
                                    display = document.querySelector('#timer');
                                timerCheck = "checkedOut";

                                //  timerTimeout =startTimer(fiveMinutes, display);
                            }
                        } else if (locked == "false") {
                            $("#toolBar button").prop("disabled",false);
                            $("#locked").text("Board Free!").attr("class", "");
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
                        json[i].strokeId, "path", json[i].points, json[i].color, json[i].brushSize);
                    board.create();
                }

            }
            checkBoard = setTimeout(function () {
                loadBoard(boardId);
            }, 2000);
        });
}

function acceptBoardInvite(input) {
    myXhr("post", {
            method: "updateInviteSvs",
            a: 'board',
            data: input + "|1"
        }).done(function (json) {
            if (json[0].Accepted == "Success") {
                getBoards();
                checkInvites();
            }
        });
}

function denyBoardInvite(input) {
    myXhr("get", {
            method: "updateInviteSvs",
            a: 'board',
            data: input + "|0"
        }).done(function (json) {
            alert("Deny it");
        });
}

//***TOOLBAR DIV**//
function createToolbar(boardId,owner){
    var rowDiv = document.createElement("div");
    rowDiv.setAttribute("class","row");
    rowDiv.setAttribute("id","toolBar");
    var brushTools = document.createElement("div");
    brushTools.setAttribute("class","col-md-12");
    var boardTools = document.createElement("div");
    boardTools.setAttribute("class","col-md-12");
    boardTools.setAttribute("id","boardTools");


    var colorBtn = document.createElement("button");
    colorBtn.innerHTML = "Change Color";
    colorBtn.setAttribute("id", "changeColor");
    colorBtn.setAttribute("class", "btn btn-primary col-md-3");
    colorBtn.setAttribute("data-toggle", "modal");
    colorBtn.setAttribute("data-target", "#colorsModal");
    brushTools.appendChild(colorBtn);
    var decreaseSizeBtn = document.createElement("button");
    decreaseSizeBtn.innerHTML = "-";
    decreaseSizeBtn.setAttribute("id", "decreaseBtn");
    decreaseSizeBtn.setAttribute("class","btn btn-info col-md-1 col-md-offset-7");
    decreaseSizeBtn.addEventListener("click", function (event) {
        whiteboard.decreaseBrush();
    });
    brushTools.appendChild(decreaseSizeBtn);
    var increaseSizeBtn = document.createElement("button");
    increaseSizeBtn.innerHTML = "+";
    increaseSizeBtn.setAttribute("class","btn btn-info col-md-1");
    increaseSizeBtn.setAttribute("id", "increaseBtn");
    increaseSizeBtn.addEventListener("click", function (event) {
        whiteboard.increaseBrush();
    });
    brushTools.appendChild(increaseSizeBtn);

    var clearBtn = document.createElement("button");
    clearBtn.innerHTML = "Clear Board";
    clearBtn.setAttribute("class","btn btn-warning col-md-3");
    clearBtn.setAttribute("id","clearBoardBtn");
    clearBtn.addEventListener('click',function(){
        myXhr('post',{method:'clearBoardSvs',a:'board',data:boardId}).done(function(){
            alert("Cleared");
            loadBoard(boardId);
        });
    });
    boardTools.appendChild(clearBtn);

    var finishPlaceHolder = document.createElement("div");
    finishPlaceHolder.setAttribute("id","pHforFinish");
    finishPlaceHolder.setAttribute("class","col-md-3 col-md-offset-1");
    boardTools.appendChild(finishPlaceHolder);
    if(owner == "true"){
                var deleteBoardBtn = document.createElement("button");
                deleteBoardBtn.innerHTML="Delete Board";
                deleteBoardBtn.setAttribute("id","DeleteBoardBtn");
                deleteBoardBtn.setAttribute("class","btn btn-danger col-md-3 col-md-offset-2");
                deleteBoardBtn.setAttribute("data-toggle", "modal");
                deleteBoardBtn.setAttribute("data-target", "#deleteBoardModal");
                boardTools.appendChild(deleteBoardBtn);
            }
    rowDiv.appendChild(brushTools);
    rowDiv.appendChild(boardTools);


    return rowDiv;
}

 /*****************************
 *
 *
 *------- MISC  ---------------
 *
 *
 *****************************/

 function uninvite(name,board){
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
            beforeSend: function () {
                //turn on spinner if id sent in
                if (id) {
                    $(id).appendChild('<img src="someSpinner.gif" class="spin"/>');
                }
            }
        }).always(function () {
            //kill spinner
            if (id) {
                $(id).find('img.spin').fadeOut(1500, function () {
                        $(this)
                            .remove();
                    });
            }
        }).fail(function () {
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