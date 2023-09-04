<?php
    error_reporting(0);
    session_start();
    if(!isset($_SESSION["logged"]) || $_SESSION["user_data"]["position"] <= 1)
    {
        header("Location: index.php");
        exit();
    }
    if(isset($_POST["mode"]))
    {
        $success = true;
        $editing = ($_POST["mode"] == "edit") ? (true) : (false);
        $productName = $_POST["productTitle"];
        if(mb_strlen($productName) < 3 || mb_strlen($productName) > 200 || ctype_space($productName) )
        {
            $success = false;
            $_SESSION["product_error_name"] = "Nazwa produktu musi zawierać od 3 do 200 znaków.";
        }
        $productCategory = (isset($_POST["productCategory"])) ? ($_POST["productCategory"]) : (null);
        if(!$productCategory)
        {
            $success = false;
            $_SESSION["product_error_category"] = "Proszę wybrać co najmniej jedną kategorię.";
        }
        $productDescription = $_POST["productDescription"];
        if(mb_strlen($productDescription) < 15 || mb_strlen($productDescription) > 2000  || ctype_space($productDescription))
        {
            $success = false;
            $_SESSION["product_error_description"] = "Opis produktu musi zawierać od 15 do 2 000 znaków.";
        }
        $productPrice = $_POST["productPrice"];
        if(!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $productPrice) || (float)$productPrice < 0.01 ||(float)$productPrice > 999999.99)
        {
            $success = false;
            $_SESSION["product_error_price"] = "Proszę podać poprawną cenę z zakresu od 0,01 zł do 999 999,99 zł.";
        }
        $productDiscount = (isset($_POST["productDiscount"]) && $_POST["productDiscount"]) ? ($_POST["productDiscount"]) : (null);
        if(isset($_POST["productIsOnSale"]) && (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $productDiscount) || (float)$productDiscount >= (float)$productPrice))
        {
            $success = false;
            $_SESSION["product_error_discount"] = "Podana promocyjna cena nie jest poprawna.";
        }
        $productBestFeature = $_POST["productBestFeature"];
        if($productBestFeature && (ctype_space($productBestFeature) || mb_strlen($productBestFeature) < 5 || mb_strlen($productBestFeature) > 30))
        {
            $success = false;
            $_SESSION["product_error_bestFeature"] = "Proszę podać poprawny ciąg znaków liczący od 5 do 30 znaków.";
        }
        $productAmount = $_POST["productAmount"];
        if(!filter_var($productAmount, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 1000000))))
        {
            $success = false;
            $_SESSION["product_error_amount"] = "Proszę podać poprawną liczbę całkowitą z zakresu od 0 do 1 000 000.";
        }
        $productGuarantee = ($_POST["productGuarantee"]) ? ($_POST["productGuarantee"]) : (null);
        if(!filter_var($productGuarantee, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 1000))))
        {
            $success = false;
            $_SESSION["product_error_guarantee"] = "Wartość powinna być poprawną liczbą całkowitą z zakresu od 1 do 1 000.";
        }
        if(isset($_POST["productParameterName"]))
        {
            for($i = 0; $i < count($_POST["productParameterName"]); $i++)
            {
                if(ctype_space($_POST["productParameterName"][$i]) || ctype_space($_POST["productParameterValue"][$i]) || mb_strlen($_POST["productParameterName"][$i]) < 2 || mb_strlen($_POST["productParameterName"][$i]) > 100 || mb_strlen($_POST["productParameterValue"][$i]) < 1 || mb_strlen($_POST["productParameterValue"][$i]) > 100)
                {
                    $success = false;
                    $_SESSION["product_error_parameters"] = "Wygląda na to, że nie wszystkie pola są wypełnione poprawnie.";
                    break;
                }
            }
        }
        function validateFile($name, $size, $tmp_name, $error)
        {
            $extensions = array("png", "jpg", "jpeg", "gif", "bmp", "tiff");
            $tmp = explode(".", $name);
            $file_ext = strtolower(end($tmp));
            $errorMessages = array();
            if($size > 2097152)
                array_push($errorMessages, "Plik jest za duży: maksymalny rozmiar pliku wynosi 2 MB.");           
            if(!in_array($file_ext, $extensions))
                array_push($errorMessages, "Błędne rozszerzenie pliku: wybierz plik graficzny o rozszerzeniu .png, .jpg, .jpeg, .gif, .bmp lub .tiff.");
            if(count($errorMessages) == 0)
            {
                $info = getimagesize($tmp_name);
                list($width, $height) = $info;
                if($width < 500)
                    array_push($errorMessages, "Szerokość obrazu jest za mała ($width px): powinna wynosić minimum 500 pikseli.");
                if($height < 500)
                {
                    array_push($errorMessages, "Wysokość obrazu jest za mała ($height px): powinna wynosić minimum 500 pikseli.");
                } 
            }
            if(count($errorMessages) > 0)
            {
                $_SESSION["product_error_".$error] = $errorMessages;
                return false;
            }                
            return true;  
        }
        if(!$editing && !is_uploaded_file($_FILES["productDefaultImage"]["tmp_name"]))
        {
            $success = false;
            $_SESSION["product_error_defaultImage"] = array("Proszę przesłać zdjęcie produktu.");
        }
        if(is_uploaded_file($_FILES["productDefaultImage"]["tmp_name"]))
        {
            if(!validateFile($_FILES["productDefaultImage"]["name"], $_FILES["productDefaultImage"]["size"], $_FILES["productDefaultImage"]["tmp_name"], "defaultImage"))
                $success = false;
        }
        if(!$editing)
        {
            for($i = 0; $i < count($_FILES["productOtherImages"]["type"]); $i++) 
            {
                if(is_uploaded_file($_FILES["productOtherImages"]["tmp_name"][$i]) && !validateFile($_FILES["productOtherImages"]["name"][$i], $_FILES["productOtherImages"]["size"][$i], $_FILES["productOtherImages"]["tmp_name"][$i], "otherImages"))
                {
                    $success = false;
                    break;
                }           
            }
        }      
        if($success)
        {
            if($editing)
                $returnedId = $_POST["id"];
            require "connect.php";
            $connect = new mysqli($host, $db_user, $db_password, $db_name);
            $connect->set_charset('utf8mb4');
            if($editing)
            {
                $query = $connect->prepare('UPDATE produkt SET nazwa = ?, opis = ?, cena = ?, promocja = ?, najlepsza_cecha = ?, ilosc = ?, gwarancja = ? WHERE produkt_id = ?');
                $query->bind_param('ssddsiii', $productName, $productDescription, $productPrice, $productDiscount, $productBestFeature, $productAmount, $productGuarantee, $returnedId);
            }
            else
            {
                $query = $connect->prepare('INSERT INTO produkt(nazwa, opis, cena, promocja, najlepsza_cecha, ilosc, gwarancja) VALUES(?, ?, ?, ?, ?, ?, ?)');           
                $query->bind_param('ssddsii', $productName, $productDescription, $productPrice, $productDiscount, $productBestFeature, $productAmount, $productGuarantee);
            }           
            $query->execute();
            if(!$editing)
                $returnedId = mysqli_insert_id($connect);
            else
            {
                $query->prepare('DELETE FROM produkt_kategoria WHERE produkt_id = ?');
                $query->bind_param('i', $returnedId);
                $query->execute();
            }
            foreach($productCategory as $category)
            {
                $query = $connect->prepare('INSERT INTO produkt_kategoria(produkt_id, kategoria_id) VALUES(?, ?)');           
                $query->bind_param('ii', $returnedId, $category);
                $query->execute();
            }           
            if($editing)
            {
                $query->prepare('DELETE FROM produkt_parametr WHERE produkt_id = ?');
                $query->bind_param('i', $returnedId);
                $query->execute();
            }
            if($_POST["productParameterName"] != null)
            {
                function search($arr, $val)
                {
                    foreach($arr as $key => $value)
                    {
                        if($value == $val)
                            return $key;
                    }
                    return false;
                }
                $parameters = array();
                $query = $connect->prepare("SELECT * FROM parametr");
                $query->execute();
                $result = $query->get_result();
                while($row = $result->fetch_assoc())
                    $parameters[$row["parametr_id"]] = $row["parametr"];
                for($i = 0; $i < count($_POST["productParameterName"]); $i++)
                {
                    $parameterIndex = null;
                    if(!search($parameters, $_POST["productParameterName"][$i]))
                    {
                        $query = $connect->prepare("INSERT INTO parametr(parametr) VALUES(?)");
                        $query->bind_param('s', $_POST["productParameterName"][$i]);
                        $query->execute();
                        $parameterIndex = mysqli_insert_id($connect);
                    }
                    else
                        $parameterIndex = search($parameters, $_POST["productParameterName"][$i]);
                    $query = $connect->prepare("INSERT INTO produkt_parametr(produkt_id, parametr_id, wartosc) VALUES(?, ?, ?)");
                    $query->bind_param('iis', $returnedId, $parameterIndex, $_POST["productParameterValue"][$i]);
                    $query->execute();
                }
            } 
            if($editing && is_uploaded_file($_FILES["productDefaultImage"]["tmp_name"]))
                array_map("unlink", glob("img/productImages/".$returnedId."/default/*"));        
            $file_name = $_FILES["productDefaultImage"]["name"];
            $tmp_name = $_FILES["productDefaultImage"]["tmp_name"];
            mkdir("img/productImages/".$returnedId);
            mkdir("img/productImages/".$returnedId."/default");
            $target_dir = "img/productImages/".$returnedId."/"."default/";
            move_uploaded_file($tmp_name, $target_dir.$file_name);
            if(!$editing)
            {
                $target_dir = "img/productImages/".$returnedId."/";
                if($_FILES["productOtherImages"] != null)
                {
                    for($i = 0; $i < count($_FILES["productOtherImages"]["type"]); $i++)
                    {
                        $file_name = $_FILES["productOtherImages"]["name"][$i];
                        $tmp_name = $_FILES["productOtherImages"]["tmp_name"][$i];
                        move_uploaded_file($tmp_name, $target_dir.$file_name);
                    }
                }
            };          
            $connect->close();
            header("Location: product/".$returnedId."/");
            exit();
        }       
    }   
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Ogromny wybór, wspaniałe produkty i niskie ceny w MatiTechShop! Wiele okazji dla każdego, kto chce kupić urządzenie. Uczta dla wszystkich fanów technologii. Zapraszamy!">
    <meta name="keywords" content="sklep, elektronika, telefony, laptopy, tablety, akcesoria, oferty, niskie ceny, promocje, okazje">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Mateusz Marmuźniak">
    <title>Dodaj nowy produkt | MatiTechShop</title>
    <base href="http://127.0.0.1/sklep/">
    <link rel="shortcut icon" href="/sklep/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="/sklep/style.css">
    <noscript>
        <link rel="stylesheet" href="noscriptstyle.css">
    </noscript>
    <script src="./js/cart.js"></script>
    <!--[if lte IE 9]>
        <link rel="stylesheet" href="ie9polyfill.css">
    <![endif]-->
</head>
<body>
   <?php
        header('Content-Type: text/html; charset=utf-8');
        include "animation.html";
        include "header.php";
   ?>   
    <main class="contentContainer">
        <?php         
            $editError = false;
            $isEditModeEnabled = false;  
            require "connect.php";
            $connect = new mysqli($host, $db_user, $db_password, $db_name);
            $connect->set_charset('utf8mb4');            
            if(isset($_GET["mode"]) && $_GET["mode"] == "edit")
            {
                $editError = false;
                if(isset($_GET["id"]))               
                {
                    $id = $_GET["id"];
                    $query = $connect->prepare('SELECT * FROM produkt WHERE produkt_id = ?');           
                    $query->bind_param('i', $id);
                    $query->execute();
                    $result = $query->get_result();
                    if($result->num_rows != 1)
                        $editError = true;
                    else
                    {
                        $isEditModeEnabled = true;
                        $categories = array();
                        $editRow = $result->fetch_assoc();
                        $query = $connect->prepare('SELECT * FROM produkt_kategoria WHERE produkt_id = ?');           
                        $query->bind_param('i', $id);
                        $query->execute();
                        $result = $query->get_result();
                        while($productCategory = $result->fetch_assoc())
                            array_push($categories, $productCategory["kategoria_id"]);
                    }  
                }
                else
                    $editError = true;         
            }         
            if(!$editError)
            {
                echo "<form action='productform.php";
                if($isEditModeEnabled)
                    echo "?mode=edit&id=".$_GET["id"];
                elseif(isset($_GET["categoryid"]))
                    echo "?categoryid=".$_GET["categoryid"];
                echo "' method='POST' enctype='multipart/form-data' id='productForm'>";
                if($isEditModeEnabled)
                {
                    echo "<h2>Edytuj produkt</h2>";
                    echo "<script>document.title = 'Edytowanie produktu | MatiTechShop';</script>";
                }                   
                else
                    echo "<h2>Dodaj produkt do naszej oferty</h2>";
                echo "<fieldset><legend>Ogólne</legend>";
                echo "<label>Nazwa:";
                echo "<input type='text' name='productTitle' minlength='3' maxlength='200' class='form-control' placeholder='np. Komputer DELL'"; 
                if($isEditModeEnabled)
                    echo " value='".htmlspecialchars($editRow["nazwa"])."'";
                echo "required>";
                echo "</label>";
                if(isset($_SESSION["product_error_name"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_name"]."</div>";
                    unset($_SESSION["product_error_name"]);
                }
                echo "<label>Kategoria:";
                echo "<select name='productCategory[]' class='form-control' size='4' required multiple>";
                echo "<optgroup label='Kategorie produktów'>";
                $query = $connect->prepare('SELECT kategoria_id, nazwa FROM kategoria');
                $query->execute();
                $result = $query->get_result();
                while($row = $result->fetch_assoc())
                {
                    echo "<option value='".$row["kategoria_id"]."'";
                    if($isEditModeEnabled && in_array($row["kategoria_id"], $categories))
                        echo " selected";
                    if(!$isEditModeEnabled && isset($_GET["categoryid"]) && $_GET["categoryid"] == $row["kategoria_id"])
                        echo " selected";
                    echo ">".$row["nazwa"]."</option>";
                }                                   
                $result->free_result();
                echo "</optgroup></select></label>";
                if(isset($_SESSION["product_error_category"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_category"]."</div>";
                    unset($_SESSION["product_error_category"]);
                }
                echo "<label>Opis:";   
                echo "<textarea name='productDescription' minlength='15' maxlength='2000' cols='30' rows='10' placeholder='Opisz szczegóły tego produktu' class='form-control' required>";
                if($isEditModeEnabled)
                    echo htmlspecialchars($editRow["opis"]);
                echo "</textarea></label>";
                if(isset($_SESSION["product_error_description"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_description"]."</div>";
                    unset($_SESSION["product_error_description"]);
                }
                echo "</fieldset>";
                echo "<fieldset><legend>Zdjęcia</legend>";                    
                echo "<p>Domyślne zdjęcie:</p> <label class='fileDropArea' title='Wybierz lub przeciągnij plik' data-toggle='tooltip' data-trigger='hover'>";
                if($isEditModeEnabled)
                    echo "<div style='background-image: url(./img/productImages/".$editRow["produkt_id"]."/"."default/".scandir("./img/productImages/".$editRow["produkt_id"]."/"."default/")[2].");'></div>";
                echo "<span class='bi bi-file-earmark-arrow-up-fill'></span><span id='uploadedFileName'></span>";
                echo "<input type='file' name='productDefaultImage' accept='.png, .jpg, .jpeg, .gif, .bmp, .tiff'";
                if($isEditModeEnabled)
                    echo "></label><h6 style='font-size: 0.8rem; color: rgb(90, 90, 90);'>Uwaga: Zdjęcie w tle jest obecnie ustawione jako domyślne. Przesłanie innego pliku będzie oznaczać jego zmianę.</h6>";
                else
                    echo " required></label>";
                if(isset($_SESSION["product_error_defaultImage"]))
                {
                    echo "<div class='invalid-tooltip'>";
                    echo implode("<br>", $_SESSION["product_error_defaultImage"]);
                    echo "</div>";
                    unset($_SESSION["product_error_defaultImage"]);
                }
                if(!$isEditModeEnabled)
                {
                    echo "<label>Pozostałe zdjęcia <i>(opcjonalne)</i>: ";                      
                    echo "<input type='file' name='productOtherImages[]' accept='.png, .jpg, .jpeg., gif, .bmp, .tiff' multiple></label>";
                    if(isset($_SESSION["product_error_otherImages"]))
                    {
                        echo "<div class='invalid-tooltip'>Co najmniej jeden z plików nie może zostać przesłany: <br>";
                        echo implode("<br>", $_SESSION["product_error_otherImages"]);
                        echo "</div>";
                        unset($_SESSION["product_error_otherImages"]);
                    }
                }             
                echo "</fieldset><fieldset><legend>Parametry</legend>";                   
                echo "<label>Najlepsza cecha produktu <i>(opcjonalne)</i>:";                     
                echo "<input type='text' name='productBestFeature' minlength='5' maxlength='30' placeholder='np. Wydajny procesor' class='form-control'";
                if($isEditModeEnabled && $editRow["najlepsza_cecha"])
                    echo " value='".htmlspecialchars($editRow["najlepsza_cecha"])."'";
                echo "></label>";
                if(isset($_SESSION["product_error_bestFeature"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_bestFeature"]."</div>";
                    unset($_SESSION["product_error_bestFeature"]);
                }
                echo "<div><p>Dane techniczne <i>(opcjonalne)</i>:</p>";
                $query = $connect->prepare('SELECT DISTINCT parametr FROM parametr');
                $query->execute();
                $result = $query->get_result();
                if($result->num_rows > 0)
                {
                    echo "<datalist id='parameters'>";
                    while($row = $result->fetch_assoc())
                        echo "<option value='".$row["parametr"]."'>";
                    echo "</datalist>";
                }                      
                $result->free_result();
                echo "<button type='button' class='btn btn-info' id='addParameter'><span class='bi bi-database-fill-add'></span> Dodaj parametr</button>";     
                echo "<div id='parameterList'></div></div>";
                #echo "<h6 style='font-size: 0.8rem; color: rgb(90, 90, 90);'>Uwaga: Jeżeli produkt ma więcej niż 5 parametrów, na liście produktów z poziomu kategorii będzie widocznych tylko pięć pierwszych (oznaczonych specjalną ramką).</h6>";
                if(isset($_SESSION["product_error_parameters"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_parameters"]."</div>";
                    unset($_SESSION["product_error_parameters"]);
                }
                echo "</fieldset>";         
                echo "<fieldset><legend>Cena</legend>";                   
                echo "<label>Cena [zł]:";                       
                echo "<input type='number' name='productPrice' step='0.01' min='0.01' max='999999.99' class='form-control'";
                if($isEditModeEnabled)
                    echo " value='".$editRow["cena"]."'";
                else
                    echo " value='1000'";
                echo " required></label>";  
                if(isset($_SESSION["product_error_price"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_price"]."</div>";
                    unset($_SESSION["product_error_price"]);
                }             
                echo "<label><input type='checkbox' name='productIsOnSale' id='isOnSale'";
                if($isEditModeEnabled && $editRow["promocja"])
                    echo " checked";
                echo "> Produkt na promocji</label>";
                echo "<label>Promocyjna cena [zł]:";
                echo "<input type='number' id='specialOffer' name='productDiscount' step='0.01' min='0.01' max='999999.99' class='form-control'";
                if($isEditModeEnabled)
                {
                    if($editRow["promocja"])
                       echo " value='".$editRow["promocja"]."'"; 
                    else
                        echo " disabled";
                }                   
                else
                    echo " value='900' disabled";
                echo "></label>";
                if(isset($_SESSION["product_error_discount"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_discount"]."</div>";
                    unset($_SESSION["product_error_discount"]);
                }
                echo "</fieldset>";
                echo "<fieldset><legend>Pozostałe</legend>";                    
                echo "<label>Liczba dostępnych sztuk:";                      
                echo "<input type='number' min='0' max='1000000' step='1' name='productAmount' class='form-control'";
                if($isEditModeEnabled)
                    echo " value='".$editRow["ilosc"]."'";
                else
                    echo " value='100'";
                echo " required></label>";
                if(isset($_SESSION["product_error_amount"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_amount"]."</div>";
                    unset($_SESSION["product_error_amount"]);
                }
                echo "<label>Gwarancja produktu w miesiącach <i>(opcjonalne)</i>:";                      
                echo "<input type='number' name='productGuarantee' min='1' max='1000' step='1' class='form-control'";
                if($isEditModeEnabled)
                {
                    if($editRow["gwarancja"])
                        echo " value='".$editRow["gwarancja"]."'";
                }                    
                else
                    echo " value='24'";
                echo "></label>";
                if(isset($_SESSION["product_error_guarantee"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["product_error_guarantee"]."</div>";
                    unset($_SESSION["product_error_guarantee"]);
                }
                echo "</fieldset>";
                echo "<input type='hidden' name='mode' value='";
                    if($isEditModeEnabled)
                        echo "edit";
                    else
                        echo "add";
                echo "'>";
                if($isEditModeEnabled)
                    echo "<input type='hidden' name='id' value='".$editRow["produkt_id"]."'>";
                echo "<input type='submit' value='";
                    if($isEditModeEnabled)
                        echo "Zatwierdź zmiany";
                    else
                        echo "Dodaj produkt";
                echo "' id='productFormButton'>";
                echo "</form>";
            }
            else
                echo "<div class='alert alert-danger information'><strong>Nieprawidłowy identyfikator produktu.</strong></div>";
        ?>      
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="./js/dragAndDrop.js"></script>
    <script>
        if(document.querySelector("#isOnSale") != null)
        {
            document.querySelector("#isOnSale").addEventListener("change", function(){
            if(document.querySelector("#specialOffer").disabled)
            {
                document.querySelector("#specialOffer").removeAttribute("disabled");
                document.querySelector("#specialOffer").setAttribute("required", "");                
            }               
            else
            {
                document.querySelector("#specialOffer").setAttribute("disabled", "");
                document.querySelector("#specialOffer").removeAttribute("required");
            }                
            }, false);
        }        
        function addParameter(parameter, value)
        {
            if(document.querySelector("#parameterList").children.length >= 10)
                return;
            var newDiv = document.createElement("div")
            document.querySelector("#parameterList").appendChild(newDiv);
            var newParameter = document.createElement("input");
            newParameter.type = "text";
            newParameter.name = "productParameterName[]";
            newParameter.className = "form-control";
            newParameter.placeholder = "np. Karta graficzna";   
            newParameter.value = parameter;        
            newDiv.appendChild(newParameter);
            newParameter.setAttribute("required", "");
            newParameter.setAttribute("minlength", "2");
            newParameter.setAttribute("maxlength", "100");
            newParameter.setAttribute("list", "parameters");
            newParameter = document.createElement("input");
            newParameter.type = "text";
            newParameter.name = "productParameterValue[]";
            newParameter.className = "form-control";
            newParameter.placeholder = "np. NVIDIA GeForce RTX 4090";
            newParameter.value = value;
            newParameter.setAttribute("required", "");
            newParameter.setAttribute("minlength", "1");
            newParameter.setAttribute("maxlength", "100");
            newDiv.appendChild(newParameter);
            newParameter = document.createElement("button");
            newParameter.className = "btn btn-danger";
            newParameter.type = "button";
            newParameter.innerHTML = "<span class='bi bi-trash-fill'></span>";
            newParameter.title = "Usuń parametr";
            newParameter.setAttribute("data-toggle", "tooltip");           
            newParameter.addEventListener("click", function(){               
                this.parentElement.parentElement.removeChild(this.parentElement);
                $(this).tooltip("dispose");
            }, false);
            newDiv.appendChild(newParameter);
            $(newParameter).tooltip();
        }
        if(document.querySelector("#addParameter") != null)
        {
            document.querySelector("#addParameter").addEventListener("click", function(){
                addParameter("", "");
            }, false);
        }
        window.addEventListener("beforeunload", function(e){
            var confirmationMessage = "Jeżeli wyjdziesz z tej strony, wprowadzone zmiany zostaną utracone.";
            (e || window.event).returnValue = confirmationMessage;
            return confirmationMessage;
        }, false);
        $('[data-toggle="tooltip"]').tooltip();
    </script>
    <?php
        if($isEditModeEnabled)
        {
            $query = $connect->prepare('SELECT parametr, wartosc FROM produkt_parametr INNER JOIN parametr USING(parametr_id) WHERE produkt_id = ? LIMIT 10');
            $query->bind_param('i', $id);
            $query->execute();
            $result = $query->get_result();
            if($result->num_rows > 0)
            {
                echo "<script>";
                while($row = $result->fetch_assoc())
                    echo "addParameter('".htmlspecialchars($row["parametr"])."', '".htmlspecialchars($row["wartosc"])."');";
                echo "</script>";                  
            }                      
            $result->free_result();
        }       
        $connect->close();
    ?>
</body>
</html>