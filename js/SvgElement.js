var svgns="http://www.w3.org/2000/svg";

function SvgElement(name,type,points,color,brushSize){
        this.name = name;
        this.type = type;
        this.points = points;
        this.color = color;
        this.brushSize = brushSize;

}

SvgElement.prototype={
 create:function(){
    var svgele = document.createElementNS(svgns,this.type);
    svgele.setAttribute("fill", "none");
    svgele.setAttribute("stroke-linejoin", "round");
    svgele.setAttribute("stroke", this.color);
    svgele.setAttributeNS(null,"d",this.points);
    svgele.setAttributeNS(null,"id",this.name);
    svgele.setAttribute("stroke-width",this.brushSize);
    document.getElementsByTagName("svg")[0].appendChild(svgele);

 }

}