var svgns="http://www.w3.org/2000/svg";
var color = "black";
function Whiteboard(name,owner,publicBool,size, title, borderColor, parent){
    this.name = name;
    this.owner = owner;
    this.publicBool = publicBool;
    this.width = size;
    this.height = size;
    this.title = title;
    this.borderColor = borderColor;
    this.parent = parent;
    this.color = "black"
}

Whiteboard.prototype={
 create:function(boardId,title, publicBool){
  var whiteboard = document.createElementNS(svgns,"svg");
    whiteboard.setAttributeNS(null,"width",this.width);
    whiteboard.setAttributeNS(null,"height",this.height);
    if(this.height == "100%"){
             whiteboard.setAttributeNS(null,"height","400px");
    }
    whiteboard.setAttributeNS(null,"id",this.name);
    whiteboard.setAttributeNS(null,"data-id",boardId);
    whiteboard.setAttributeNS(null,"style","border: 2px solid "+this.borderColor+";");

    $("#whiteBoardName").html(title);
    document.getElementById(this.parent).appendChild(whiteboard);
    var button = document.createElement("button");
    button.innerHTML = "Clear Board";
    button.addEventListener('click',function(){
        myXhr('get',{method:'clearBoardSvs',a:'board',data:boardId}).done(function(){
            alert("Cleared");
            loadBoardData(boardId);
        });
    });
    document.getElementById(this.parent).appendChild(button);





    //Add the drawing listeners

    //Check here if private or public
    //If private then...
    //InitPrivate();

    init(this.name);
    /*if(publicBool == 1){
             init(this.name);
    }else{
        initPrivate(this.name);
    }
    */

 },
 changeColor:function(newColor){
    color = newColor;
    console.log("OWLS" + color);
 }

}
      /*SVG*/
var svgns="http://www.w3.org/2000/svg";
var svgCanvas;
var svgPath;
var drawingTurn =0;
    function init(id){
        svgCanvas= document.getElementById(id);
        svgCanvas.addEventListener("mousedown", startDrawTouch, false);
        svgCanvas.addEventListener("mousemove", continueDrawTouch, false);
        svgCanvas.addEventListener("mouseup", endDrawTouch, false);
    }

    function initPrivate(id){
        //TODO MAKE PRIVATE CHECK TURN BASED EVENTYLISTENERS
        svgCanvas= document.getElementById(id);
        svgCanvas.addEventListener("mousedown", startDrawTouchPrivate, false);
        svgCanvas.addEventListener("mousemove", continueDrawTouchPrivate, false);
        svgCanvas.addEventListener("mouseup", endDrawTouchPrivate, false);
    }
    function startDrawTouch(event){
        //Update whiteboard to locked status
        //Need boardId and userId
        var boardId = $("svg").attr("data-id");
        myXhr("get",{method:"checkLockSvs",a:"board",data:boardId+"|null"}).done(function(json){
          //  alert(json[0].Locked);
            var locked= json[0].Locked;
            //if it is locked
                if(locked == "true"){
                $("#locked").text("Board in use please wait!").attr("class","");
                var inUse = document.createElement("span");
                inUse.setAttribute("class","glyphicon glyphicon-pencil");
                inUse.setAttribute("class","glyphicon glyphicon-pencil");
                $("#locked").append(inUse);
              //  alert("NO WRITE");
            }else {
                 if(locked == "timed"){
                     $("#locked").text("You are drawing...");
                 }else if(locked == "false"){
                 $("#locked").text("Board Free!").attr("class","");
                 $("#timer").text("");
            }
                //ADD BUTTON TO FINISH DRAWING
                if(!$("#clearTurn").length){
                var endTurnBtn = document.createElement("button");
                endTurnBtn.innerHTML = "Finish Turn";
                endTurnBtn.setAttribute("id","clearTurn");
                endTurnBtn.addEventListener('click',function(){
                    checkLockStatus(boardId,1);
                    $("#clearTurn").remove();
                    timerCheck = null;
                 $("#timer").text("");          
                           });
                 document.getElementById("boardSpace").appendChild(endTurnBtn);
            }
                   svgPath =  document.createElementNS("http://www.w3.org/2000/svg","path");
                    svgPath.setAttribute("fill", "none");
                    svgPath.setAttribute("stroke-linejoin", "round");
                    svgPath.setAttribute("stroke", color);
                    svgPath.setAttribute("d", "M" + event.offsetX  + "," + event.offsetY);
                    svgCanvas.appendChild(svgPath);
                    lockBoard(boardId);
            }
            //Add Tools Buttons

        });

    }

    function checkLockStatus(boardId,override){
          return myXhr('get',{method:'checkLockSvs',a:'board',data:boardId+"|"+override}).done(function(json){

                    });
    }
    function lockBoard(boardId){
            myXhr("get",{method:"lockBoardSvs",a:"board",data:boardId}).done(function(){
                                        //Locks the board to the user
                        });
    }
    function continueDrawTouch(event){
    if (svgPath){
            var pathData = svgPath.getAttribute("d");
            pathData = pathData + " L" + event.offsetX + "," + event.offsetY;
            svgPath.setAttribute("d", pathData);
            //Max length is 3000
            if(svgPath.getAttribute("d").length > 3000){
                endDrawTouch(event);
                      pathData="";
            }
        }
    }
    function endDrawTouch(event){
    if (svgPath){
        drawingTurn++;
            var pathData = svgPath.getAttribute("d");
            pathData = pathData + " L" + event.offsetX + "," + event.offsetY;
            svgPath.setAttribute("d", pathData);
            //Hardcoded boardId|userId|Turn

            //Get dynamic id
            var boardId = document.getElementsByTagName("svg")[0].getAttribute("data-id");
            var strokeId = new Date().valueOf();
            svgPath.setAttribute("id","2|"+boardId+"|"+strokeId);
            console.log("saving as..." + color);
            saveBoardStroke("path",pathData,boardId,strokeId,color);
            svgPath = null;
        }
    }