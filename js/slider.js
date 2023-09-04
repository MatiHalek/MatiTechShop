[].forEach.call(document.querySelectorAll(".productImage > a > div"), function(e){
    e.setAttribute("data-imageselected", "1");
});
[].forEach.call(document.querySelectorAll(".btnForward, .btnBack"), function(e){
    e.addEventListener("click", function(){
    var images = this.parentElement.lastElementChild.firstElementChild;
    var currentImage = [].slice.call(images.getAttribute("data-imageselected"));
    if((" " + this.className + " ").indexOf(" btnForward ") > -1)
        var newImage = currentImage % images.childElementCount + 1;
    else
        var newImage = (currentImage == 1) ? (images.childElementCount) : (currentImage - 1);
    images.style.msTransform = "translateX(-" + 100 * (newImage - 1) + "%)";
    images.style.transform = "translateX(-" + 100 * (newImage - 1) + "%)";
    images.setAttribute("data-imageselected", newImage);
}, false)});