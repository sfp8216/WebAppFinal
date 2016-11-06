var svgns="http://www.w3.org/2000/svg";

function Whiteboard(name,owner,status,size, title, borderColor, parent){
    this.name = name;
    this.owner = owner;
    this.status = status;
    this.width = size+"px";
    this.height = size+"px";
    this.title = title;
    this.borderColor = borderColor;
    this.parent = parent;

}

Whiteboard.prototype={
 create:function(boardId,title){
  var whiteboard = document.createElementNS(svgns,"svg");
    whiteboard.setAttributeNS(null,"width",this.width);
    whiteboard.setAttributeNS(null,"height",this.height);
    whiteboard.setAttributeNS(null,"id",this.name);
    whiteboard.setAttributeNS(null,"data-id",boardId);
    whiteboard.setAttributeNS(null,"style","border: 2px solid "+this.borderColor+";");
    var titlebar = document.createElementNS(svgns,"text");
    titlebar.setAttributeNS(null,"x",10);
    titlebar.setAttributeNS(null,"y",10);
    var titleNode = document.createTextNode(title);
    titlebar.appendChild(titleNode);
    whiteboard.appendChild(titlebar);
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
    init(this.name);

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
    function startDrawTouch(event){
        svgPath =  document.createElementNS("http://www.w3.org/2000/svg","path");
        svgPath.setAttribute("fill", "none");
        svgPath.setAttribute("stroke-linejoin", "round");
        svgPath.setAttribute("stroke", "blue");
        svgPath.setAttribute("d", "M" + event.offsetX  + "," + event.offsetY);
        svgCanvas.appendChild(svgPath);
    }
    function continueDrawTouch(event){
    if (svgPath){
            var pathData = svgPath.getAttribute("d");
            pathData = pathData + " L" + event.offsetX + "," + event.offsetY;
            svgPath.setAttribute("d", pathData);
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
            saveBoardStroke("path",pathData,boardId,strokeId);
            svgPath = null;
        }
    }