$("#starCounter > input").on("input change", function(){
    document.querySelector("#starCounter > span").innerHTML = "<span class='bi bi-star-fill'></span>" + parseFloat(this.value).toFixed(1).toString().replace(".", ",")}
);
$("#opinionDescription > textarea").on("input keyup", function(){
    document.querySelector("#lettersCounter").textContent = this.value.length + "/500";
});