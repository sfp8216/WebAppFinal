$(document).ready(function() {
   $("#toggleLogin").on("click",function(event){
        $("#loginForm").slideToggle();
        $("#createForm").slideToggle();
    });
    $("#toggleCreate").on("click",function(event){
        $("#createForm").slideToggle();
        $("#loginForm").slideToggle();
   });
    $(".pickColor").on("click",function(event){
        console.log($(this).text());
        whiteboard.changeColor($(this).text());
    });
});