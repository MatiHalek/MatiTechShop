var fullscreen = document.querySelector(".fullscreen");
var fullscreenImage = document.querySelector(".fullscreen img");
var imageInfo = document.querySelector("#imageInfo");
var title = document.querySelector("h3").textContent;
var images = [].slice.call(document.querySelectorAll(".productSection img"));
var currentImage = -1;
images.forEach(function (element, index){
    element.addEventListener("click", function(){
        currentImage = index;
        fullscreenImage.setAttribute("src", element.getAttribute("src"));
        fullscreenImage.setAttribute("alt", element.getAttribute("alt"));
        $(".fullscreen").addClass("fullscreen-active");
        imageInfo.innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + (index + 1) + "/" + images.length + "</h5>";
        document.body.style.overflow = "hidden";
        fullscreenImage.style.animation = "0.5s normal 0s forwards 1 imageIn ease-in-out";
    }, false)
});
fullscreen.addEventListener("click", function(event){
    if(event.currentTarget !== event.target)
        return;
    $(this).removeClass("fullscreen-active");
    document.body.style.overflow = "visible";
}, false);
document.querySelector(".fullscreen button:first-of-type").addEventListener("click", function(){
    $(".fullscreen").removeClass("fullscreen-active");
    document.body.style.overflow = "visible";
}, false);
document.querySelector("#forward").addEventListener("click", function(){
    if(currentImage < images.length - 1)
    {
        fullscreenImage.setAttribute("src", images[currentImage + 1].getAttribute("src"));
        fullscreenImage.setAttribute("alt", images[currentImage + 1].getAttribute("alt"));
        imageInfo.innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + (currentImage + 2) + "/" + images.length + "</h5>";
    }
    else
    {
        fullscreenImage.setAttribute("src", images[0].getAttribute("src"));
        fullscreenImage.setAttribute("alt", images[0].getAttribute("alt"));
        imageInfo.innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>1/" + images.length + "</h5>";
    }
    currentImage = ++currentImage % images.length;
}, false);
document.querySelector("#back").addEventListener("click", function(){
    if(currentImage > 0)
    {
        fullscreenImage.setAttribute("src", images[currentImage - 1].getAttribute("src"));
        fullscreenImage.setAttribute("alt", images[currentImage - 1].getAttribute("alt"));
        imageInfo.innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + currentImage + "/" + images.length + "</h5>";
        currentImage--;
    }
    else
    {
        fullscreenImage.setAttribute("src", images[images.length - 1].getAttribute("src"));
        fullscreenImage.setAttribute("alt", images[images.length - 1].getAttribute("alt"));
        imageInfo.innerHTML = "<h4>" + title + "</h4><h5 style='color: orange;'>" + images.length + "/" + images.length + "</h5>";
        currentImage = images.length - 1;
    } 
}, false);
[].slice.call(document.querySelectorAll(".imgContainer")).forEach(function (element) {
        element.addEventListener("mouseover", function(){
        this.firstElementChild.style.filter="brightness(35%)";
    }, false);
    element.addEventListener("mouseout", function(){
        this.firstElementChild.style.filter="none";
    }, false);   
});