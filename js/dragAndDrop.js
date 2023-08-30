var dragArea = document.querySelector(".fileDropArea");
dragArea.addEventListener("drop", function(evt){
    evt.preventDefault();
    $(this).removeClass("active");                  
    var fileInput = document.querySelector(".fileDropArea input[type='file']");
    fileInput.files = evt.dataTransfer.files;
    if(fileInput.files[0])
        document.querySelector("#uploadedFileName").textContent = fileInput.files[0].name;                          
}, false);
dragArea.addEventListener("dragover", function(evt){
    evt.preventDefault();
    $(this).addClass("active");
}, false);
dragArea.addEventListener("dragleave", function(){
    $(this).removeClass("active");
}, false);
document.querySelector(".fileDropArea input[type='file']").addEventListener("change", function(){
    if(this.files[0])
        document.querySelector("#uploadedFileName").textContent = this.files[0].name;
    else
        document.querySelector("#uploadedFileName").textContent = "";
}, false);