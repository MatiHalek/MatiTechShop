var editMode = false;
if(document.querySelector(".toggleEditMode"))
{
    document.querySelector(".toggleEditMode").addEventListener("change", function(){
        if(editMode)
        {
            Array.prototype.forEach.call(document.querySelectorAll(".editable"), function (e){
                $(e).addClass("d-none");                       
            });           
            Array.prototype.forEach.call(document.querySelectorAll(".category"), function(el){
                el.setAttribute("draggable", "true");
            });        
            this.parentElement.style.backgroundColor = "gray";
            sessionStorage.removeItem("editMode");
            editMode = false;
        }
        else
        {
            Array.prototype.forEach.call(document.querySelectorAll(".editable"), function (e){
                $(e).removeClass("d-none");
            });
            Array.prototype.forEach.call(document.querySelectorAll(".category"), function(el){
                el.setAttribute("draggable", "false");
            });
            document.querySelector("#addNewCategory").addEventListener("click", function(){
                this.style.transform = "scale(0)";
                this.style.opacity = "0";
                this.style.visibility = "hidden";
            }, false);
            var dragArea = document.querySelector(".category.editable label");
            dragArea.addEventListener("drop", function(evt){
                evt.preventDefault();
                $(this).removeClass("active");                  
                var fileInput = document.querySelector(".category.editable input[type='file']");
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
            document.querySelector(".category.editable input[type='file']").addEventListener("change", function(){
                if(this.files[0])
                    document.querySelector("#uploadedFileName").textContent = this.files[0].name;
                else
                    document.querySelector("#uploadedFileName").textContent = "";
            }, false);
            this.parentElement.style.backgroundColor = "dodgerblue";
            sessionStorage.setItem("editMode", "true");
            editMode = true;
        }
    }, false);
    if(sessionStorage.getItem("editMode"))
    {
        var onchangeEvent;
        if(typeof(Event) == "function")
            onchangeEvent = new Event("change");
        else
        {
            onchangeEvent = document.createEvent("Event");
            onchangeEvent.initEvent("change", true, true);
        }
        document.querySelector(".toggleEditMode").dispatchEvent(onchangeEvent);
        document.querySelector(".toggleEditMode").checked = "checked";
    }
}       
Array.prototype.forEach.call(document.querySelectorAll(".category:not(:last-of-type)"), function (e){
    e.addEventListener("click", function(e){
        if(editMode)
            e.preventDefault();
    }, false);
});
Array.prototype.forEach.call(document.querySelectorAll(".deleteCategory"), function(e){
    e.addEventListener("click", function(){
        document.querySelector("#staticBackdrop input[name='item']").value = e.parentElement.id.substring(1);
        document.querySelector("#staticBackdrop .modal-body span:first-of-type").innerText = e.parentElement.lastElementChild.children[1].firstElementChild.textContent;
        document.querySelector("#staticBackdrop .modal-body span:last-of-type").innerText = e.parentElement.lastElementChild.children[1].lastElementChild.textContent.slice(1, -1);
    }, false);
});
var previousValue;
$(".category").on("focus", "h3", function(){
    if(!editMode)
        return;
    previousValue = $(this).text();
    $(this).replaceWith(el = "<textarea rows='2' placeholder='Nazwa kategorii' maxlength='25' id='editCategory'>" + $(this).text()+ "</textarea>");
    $("#editCategory").focus();
});
$(".category:not(:last-of-type)").on("blur", "textarea", function(){
    var element = this;          
    var newValue = $(element).val();      
    var tmp = document.createElement("h3");
    tmp.setAttribute("tabindex", "0");
    tmp.textContent = previousValue;
    $(element).replaceWith(tmp); 
    if(newValue == "" || newValue == previousValue)
        return;           
    $.ajax({
        method: "POST",
        url: "update.php",
        data: {"id" : tmp.parentElement.parentElement.parentElement.id.substring(1), "value" : newValue.substring(0, 25)},
        success: function(result)
        {
            tmp.textContent = result;
            showAlert("Pomyślnie zmieniono nazwę kategorii.", false, 2000);
        }
    });                 
});