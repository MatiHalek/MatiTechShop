var tips = new Array(
    "Możesz przeglądać zdjęcia produktu bez wchodzenia na jego szczegóły.", 
    "Abc"
    );
    var iSpeed = 100; // time delay of print out
    var iIndex = 0; // start printing array at this posision
    var iArrLength = tips[0].length; // the length of the text array
    var iScrollAt = 20; // start scrolling up at this many lines
     
    var iTextPos = 0; // initialise text position
    var sContents = ''; // initialise contents variable
    var iRow; // initialise current row    
function Write()
{
    sContents =  ' ';
    iRow = Math.max(0, iIndex-iScrollAt);
    var destination = document.querySelector("#typedText");  
    while ( iRow < iIndex ) {
    sContents += tips[iRow++] + '<br />';
    }
    destination.innerHTML = sContents + tips[iIndex].substring(0, iTextPos) + "_";
    if ( iTextPos++ == iArrLength ) {
    iTextPos = 0;
    iIndex++;
    if ( iIndex != tips.length ) {
    iArrLength = tips[iIndex].length;
    setTimeout("Write()", 500);
    }
    } else {
    setTimeout("Write()", iSpeed);
    }
} 
Write();