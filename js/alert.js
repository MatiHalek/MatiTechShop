function showAlert(message, isFailed, timeout)
{    
    var icon = "bi-check-circle-fill";
    $(".notification").removeClass("alert-danger").addClass("alert-success");
    if(isFailed)
    {
        $(".notification").removeClass("alert-success").addClass("alert-danger");
        icon = "bi-x-circle-fill";
    }       
    $(".notification").html("<span class='bi " + icon + "'></span>&nbsp;" + message).slideDown().delay(timeout).slideUp();
}