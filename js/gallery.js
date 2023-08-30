var title = document.querySelector("h3").innerText;
var images = Array.prototype.slice.call(document.querySelectorAll(".productSection img"));
images.forEach(function (element, index){
    element.addEventListener("click", function(){
        document.querySelector(".fullscreen img").setAttribute("src", element.getAttribute("src"));
        document.querySelector(".fullscreen img").setAttribute("alt", element.getAttribute("alt"));
        document.querySelector(".fullscreen").style.display = "flex";
        document.querySelector("#imageInfo").innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + (index + 1) + "/" + images.length + "</h5>";
        document.body.style.overflow = "hidden";
        document.querySelector(".fullscreen img").style.animation = "0.5s normal 0s forwards 1 imageIn ease-in-out";
    }, false)
});
document.querySelector(".fullscreen").addEventListener("click", function(event){
    if(event.currentTarget !== event.target)
        return;
    this.style.display = "none";
    document.body.style.overflow = "visible";
}, false);
document.querySelector(".fullscreen button:first-of-type").addEventListener("click", function(){
    document.querySelector(".fullscreen").style.display = "none";
    document.body.style.overflow = "visible";
}, false);
document.querySelector("#forward").addEventListener("click", function(){
    var index = -1;
    images.some(function(el, i){
        if(el.getAttribute("src") == document.querySelector(".fullscreen img").getAttribute("src"))
        {
            index = i;
            return true;
        }
    });
    if(index < images.length - 1)
    {
        document.querySelector(".fullscreen img").setAttribute("src", images[index + 1].getAttribute("src"));
        document.querySelector(".fullscreen img").setAttribute("alt", images[index + 1].getAttribute("alt"));
        document.querySelector("#imageInfo").innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + (index + 2) + "/" + images.length + "</h5>";
    }
    else
    {
        document.querySelector(".fullscreen img").setAttribute("src", images[0].getAttribute("src"));
        document.querySelector(".fullscreen img").setAttribute("alt", images[0].getAttribute("alt"));
        document.querySelector("#imageInfo").innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>1/" + images.length + "</h5>";
    }
}, false);
document.querySelector("#back").addEventListener("click", function(){
    var index = -1;
    images.some(function(el, i){
        if(el.getAttribute("src") == document.querySelector(".fullscreen img").getAttribute("src"))
        {
            index = i;
            return true;
        }
    });
    if(index > 0)
    {
        document.querySelector(".fullscreen img").setAttribute("src", images[index - 1].getAttribute("src"));
        document.querySelector(".fullscreen img").setAttribute("alt", images[index - 1].getAttribute("alt"));
        document.querySelector("#imageInfo").innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + index + "/" + images.length + "</h5>";
    }
    else
    {
        document.querySelector(".fullscreen img").setAttribute("src", images[images.length - 1].getAttribute("src"));
        document.querySelector(".fullscreen img").setAttribute("alt", images[images.length - 1].getAttribute("alt"));
        document.querySelector("#imageInfo").innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + images.length + "/" + images.length + "</h5>";
    } 
}, false);
Array.prototype.slice.call(document.querySelectorAll(".imgContainer")).forEach(function (element) {
        element.addEventListener("mouseover", function(){
        this.firstElementChild.style.filter="brightness(35%)";
    }, false)
});
Array.prototype.slice.call(document.querySelectorAll(".imgContainer")).forEach(function (element) {
        element.addEventListener("mouseout", function(){
        this.firstElementChild.style.filter="none";
    }, false)
});