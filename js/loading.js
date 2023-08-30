function DrawLoadingBar()
{
    ctx.clearRect(0, 0, 100, 100);
    ctx.beginPath();
    ctx.arc(50, 50, 40, startAngle * (Math.PI / 180), endAngle * (Math.PI / 180));           
    ctx.stroke();
    ctx.closePath();
    startAngle = (startAngle + 6) % 360;
    endAngle = (endAngle + 6) % 360;
    if(window.requestAnimationFrame)
        loadingAnimation = requestAnimationFrame(DrawLoadingBar);
}
var canvas = document.querySelector("canvas");
canvas.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);
var ctx = canvas.getContext("2d");
if(window.requestAnimationFrame)
    var loadingAnimation = requestAnimationFrame(DrawLoadingBar);
else
    var loadingAnimation = setInterval(DrawLoadingBar, 17);
ctx.strokeStyle = "#FFFFFF";
ctx.lineWidth = 15;
var startAngle = 180;
var endAngle = 315;       
window.addEventListener("load", function(){
    setTimeout(function(){
        if(window.requestAnimationFrame)
            cancelAnimationFrame(loadingAnimation);
        else
            clearInterval(loadingAnimation);
        document.querySelector("#loadingScreen").style.display = "none"; 
    }, 500);                  
}, false);