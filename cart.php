<?php
    error_reporting(0);
    session_start();
    if(isset($_POST["postcode"]))
    {
        require "connect.php";
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        $connect->set_charset('utf8mb4');
        $success = true;
        $errors = array();
        if(isset($_POST["productIDs"], $_POST["productQuantities"]) && !empty($_POST["productIDs"] && !empty($_POST["productQuantities"])))
        {
            $productIDs = $_POST["productIDs"];
            $productQuantities = $_POST["productQuantities"];
            for($product = 0; $product < count($productIDs); $product++)
            {
                $query = $connect->prepare("SELECT * FROM produkt WHERE produkt_id = ?");
                $query->bind_param('i', $productIDs[$product]);
                $query->execute();
                $result = $query->get_result();
                if($result->num_rows != 1)
                {
                    array_push($errors, "Wykryto nieprawidłowe identyfikatory produktów.");
                    $success = false;
                }
                else
                {
                    $row = $result->fetch_assoc();
                    if($productQuantities[$product] > $row["ilosc"] || $productQuantities[$product] <= 0)
                    {
                        array_push($errors, "Wykryto nieprawidłowe wartości liczby produktów.");
                        $success = false;
                    }
                }
            }
        }
        else
        {
            array_push($errors, "Nie wykryto produktów w koszyku."); 
            $success = false;
        }            
        $deliveryOptions = array();
        $query = $connect->prepare("SELECT * FROM dostawa");
        $query->execute();
        $result = $query->get_result();
        while($row = $result->fetch_assoc())
            array_push($deliveryOptions, $row["dostawa_id"]);
        if(empty($_POST["deliveryOptions"]) || !in_array($_POST["deliveryOptions"], $deliveryOptions)) 
        {
            $_SESSION["delivery_error"] = "Proszę wybrać opcję dostawy.";
            $success = false;
        }
        else
            $deliveryOptions = $_POST["deliveryOptions"];
        $paymentOptions = array();
        $query = $connect->prepare("SELECT * FROM platnosc");
        $query->execute();
        $result = $query->get_result();
        while($row = $result->fetch_assoc())
            array_push($paymentOptions, $row["platnosc_id"]);
        if(empty($_POST["paymentOptions"]) || !in_array($_POST["paymentOptions"], $paymentOptions)) 
        {
            $_SESSION["payment_error"] = "Proszę wybrać opcję płatności.";
            $success = false;
        }
        else
            $paymentOptions = $_POST["paymentOptions"];
        $personalData = array("firstName" => "imię", "surname" => "nazwisko", "postcode" => "kod pocztowy", "city" => "miasto", "street" => "ulicę", "houseNumber" => "numer domu", "phoneNumber" => "numer telefonu", "emailAddress" => "adres e-mail");
        foreach($personalData as $data => $info)
        {
            if(empty($_POST[$data]) || ctype_space($_POST[$data]))
            {
                $_SESSION[$data."_error"] = "Proszę podać ".$info.".";
                $success = false;
            }               
            elseif($data == "postcode" && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $_POST[$data]))
            {
                $_SESSION[$data."_error"] = "Proszę podać poprawny kod pocztowy (format XX-XXX)";
                $success = false;
            }               
            elseif($data == "emailAddress" && !filter_var($_POST[$data], FILTER_VALIDATE_EMAIL))
            {
                $_SESSION[$data."_error"] = "Proszę podać poprawny adres e-mail.";
                $success = false;
            }                
        }
        if(!empty($errors))
            $_SESSION["cart_errors"] = $errors;
        if($success)
        {
            
            if(!$_SESSION["logged"])
            {
                $query = $connect->prepare("INSERT INTO uzytkownik(nazwa_uzytkownika, haslo, email, data_urodzenia, numer_telefonu, imie, nazwisko, kod_pocztowy, miasto, ulica, nr_domu, czy_zalogowany, stanowisko_id) VALUES(NULL, NULL, ?, NULL, ?, ?, ?, ?, ?, ?, ?, 0, NULL)");
                $query->bind_param('ssssssss', $_POST["emailAddress"], $_POST["phoneNumber"], $_POST["firstName"], $_POST["surname"], $_POST["postcode"], $_POST["city"], $_POST["street"], $_POST["houseNumber"]);
                $query->execute();
                $user_id = mysqli_insert_id($connect);
            }
            else
            {
                $query = $connect->prepare("UPDATE uzytkownik SET imie = ?, nazwisko = ?, kod_pocztowy = ?, miasto = ?, ulica = ?, nr_domu = ?, email = ?, numer_telefonu = ? WHERE uzytkownik_id = ?");
                $query->bind_param('ssssssssi', $_POST["firstName"], $_POST["surname"], $_POST["postcode"], $_POST["city"], $_POST["street"], $_POST["houseNumber"], $_POST["emailAddress"], $_POST["phoneNumber"], $_SESSION["user_data"]["user_id"]);
                $query->execute();
                $user_id = $_SESSION["user_data"]["user_id"];
            }           
            $query = $connect->prepare("INSERT INTO zamowienie(uzytkownik_id, data_zamowienia, dostawa_id, platnosc_id, status_id) VALUES(?, ?, ?, ?, 1)");
            $query->bind_param('isii', $user_id, date("Y-m-d H:i:s"), $deliveryOptions, $paymentOptions);
            $query->execute();
            $order_id = mysqli_insert_id($connect);
            for($product = 0; $product < count($productIDs); $product++)
            {
                $query = $connect->prepare("SELECT * FROM produkt WHERE produkt_id = ?");
                $query->bind_param('i', $productIDs[$product]);
                $query->execute();
                $result = $query->get_result();
                $row = $result->fetch_assoc();
                $finalPrice = ($row["promocja"]) ? ($row["promocja"]) : ($row["cena"]);
                $finalQuantity = $productQuantities[$product];
                $query = $connect->prepare("INSERT INTO produkt_zamowienie(zamowienie_id, produkt_id, ilosc, cena_za_sztuke) VALUES(?, ?, ?, ?)");
                $query->bind_param('iiid', $order_id, $row["produkt_id"], $finalQuantity, $finalPrice);
                $query->execute();
                $newQuantity = $row["ilosc"] - $finalQuantity;
                $query = $connect->prepare("UPDATE produkt SET ilosc = ? WHERE produkt_id = ?");
                $query->bind_param('ii', $newQuantity, $row["produkt_id"]);
                $query->execute();
            }
            $result->free_result();
            $connect->close();
            $_SESSION["authenticate_order"] = true;
            header("Location: order.php");
            exit();
        } 
        $result->free_result();
        $connect->close();          
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
    <title>Twój koszyk | MatiTechShop</title>
    <base href="http://127.0.0.1/sklep/">
    <link rel="shortcut icon" href="/sklep/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="/sklep/style.css">
    <noscript>
        <link rel="stylesheet" href="noscriptstyle.css">
    </noscript>
    <script src="./js/alert.js"></script>
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
   <div class="alert notification" role="alert"></div>
    <main class="contentContainer">
        <h2>Twój koszyk</h2>
        <form action="cart.php" method="POST" id="orderForm" >
            <fieldset></fieldset>    
            <?php
                require "connect.php";
                $connect = new mysqli($host, $db_user, $db_password, $db_name);
                $connect->set_charset('utf8mb4');
                $query = $connect->prepare("SELECT * FROM dostawa");
                $query->execute();
                $result = $query -> get_result();
                echo "<fieldset>";
                echo "<h3>Dostawa</h3>";
                echo "<div class='alert alert-primary information'><span class='bi bi-truck'></span> <strong>Darmowa dostawa od 2000,00 zł. </strong></div>";
                $radioId = 1;
                while($row = $result->fetch_assoc())
                {                                     
                    echo "<div class='optionLabel'>";
                    echo "<input type='radio' data-cost='".$row["koszt"]."' name='deliveryOptions' value='".$row["dostawa_id"]."' id='deliveryRadio".$radioId."' required>";
                    echo "<label for='deliveryRadio".($radioId++)."'>";
                    echo "<span>".$row["nazwa_dostawy"]."</span><span>".str_replace(".", ",", $row["koszt"])." zł</span>";
                    echo "</label></div>";
                }   
                if(isset($_SESSION["delivery_error"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["delivery_error"]."</div>";
                    unset($_SESSION["delivery_error"]);
                }
                $query = $connect->prepare("SELECT * FROM platnosc");
                $query->execute();
                $result = $query -> get_result();
                echo "</fieldset>";
                echo "<fieldset>";
                echo "<h3>Płatność</h3>";
                $radioId = 1;
                while($row = $result->fetch_assoc())
                {
                    echo "<div class='optionLabel'>";
                    echo "<input type='radio' name='paymentOptions' value='".$row["platnosc_id"]."' id='paymentRadio".$radioId."' required>";
                    echo "<label for='paymentRadio".($radioId++)."'>";
                    echo $row["nazwa_platnosci"];
                    echo "</label></div>";
                } 
                if(isset($_SESSION["payment_error"]))
                {
                    echo "<div class='invalid-tooltip'>".$_SESSION["payment_error"]."</div>";
                    unset($_SESSION["payment_error"]);
                }
                echo "</fieldset>";
                $result->free_result();
                $connect->close();
            ?>
            <fieldset>
                <h3>Dane</h3>
                <?php
                ?>
                <div>
                    <input type="text" name="firstName" class="form-control" placeholder="Imię" maxlength="30" required id="formFirstName" value="<?php
                        if(isset($_SESSION["user_data"]))
                            echo $_SESSION["user_data"]["first_name"];
                    ?>">
                    <label for="formFirstName" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Imię</label>
                    <?php
                        if(isset($_SESSION["firstName_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["firstName_error"]."</div>";
                            unset($_SESSION["firstName_error"]);
                        }
                    ?> 
                </div>      
                <div>
                    <input type="text" name="surname" class="form-control" placeholder="Nazwisko" maxlength="50" required id="formSurname" value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["surname"];
                        ?>">
                    <label for="formSurname" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Nazwisko</label>
                    <?php
                        if(isset($_SESSION["surname_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["surname_error"]."</div>";
                            unset($_SESSION["surname_error"]);
                        }
                    ?>  
                </div>   
                <div>
                    <input type="text" name="postcode" class="form-control" placeholder="Kod pocztowy" maxlength="6" pattern="[0-9]{2}-[0-9]{3}" title="Proszę wpisać poprawny kod pocztowy." id="formPostcode" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["postcode"];
                        ?>">
                    <label for="formPostcode" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Kod pocztowy</label>
                    <?php
                        if(isset($_SESSION["postcode_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["postcode_error"]."</div>";
                            unset($_SESSION["postcode_error"]);
                        }
                    ?> 
                </div>                     
                <div>
                    <input type="text" name="city" class="form-control" placeholder="Miasto" maxlength="50" id="formCity" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["city"];
                        ?>">
                    <label for="formCity" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Miasto</label>
                    <?php
                        if(isset($_SESSION["city_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["city_error"]."</div>";
                            unset($_SESSION["city_error"]);
                        }
                    ?> 
                </div>   
                <div>
                    <input type="text" name="street" class="form-control" placeholder="Ulica" maxlength="50" id="formStreet" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["street"];
                        ?>">
                    <label for="formStreet" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Ulica</label>
                    <?php
                        if(isset($_SESSION["street_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["street_error"]."</div>";
                            unset($_SESSION["street_error"]);
                        }
                    ?> 
                </div>            
                <div>
                    <input type="text" name="houseNumber" class="form-control" placeholder="Numer domu" maxlength="10" id="formHouseNumber" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["house_number"];
                        ?>">
                    <label for="formHouseNumber" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Numer domu (lokalu)</label>
                    <?php
                        if(isset($_SESSION["houseNumber_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["houseNumber_error"]."</div>";
                            unset($_SESSION["houseNumber_error"]);
                        }
                    ?> 
                </div>
                <div>
                    <input type="tel" name="phoneNumber" class="form-control" placeholder="Numer telefonu" id="formPhoneNumber" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["phone_number"];
                        ?>">
                    <label for="formPhoneNumber" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Numer telefonu</label>
                    <?php
                        if(isset($_SESSION["phoneNumber_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["phoneNumber_error"]."</div>";
                            unset($_SESSION["phoneNumber_error"]);
                        }
                    ?>  
                </div>
                <div>
                    <input type="email" name="emailAddress" class="form-control" placeholder="Adres email" id="formEmail" required value="<?php
                            if(isset($_SESSION["user_data"]))
                                echo $_SESSION["user_data"]["email"];
                        ?>"> 
                    <label for="formEmail" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>E-mail</label>
                    <?php
                        if(isset($_SESSION["emailAddress_error"]))
                        {
                            echo "<div class='invalid-tooltip'>".$_SESSION["emailAddress_error"]."</div>";
                            unset($_SESSION["emailAddress_error"]);
                        }
                    ?>  
                </div>            
            </fieldset>
            <fieldset>
                <div>
                    <h3>Podsumowanie:</h3>
                    <h5 id="totalProductsCost">Kwota: </h5>
                    <h5 id="deliveryCost">Dostawa: </h5><hr>
                    <h3 id="totalCost">Łącznie do zapłaty: </h3>
                </div>
                <input type="submit" value="Złóż zamówienie">
            </fieldset>          
        </form>
    </main>
    <?php
        include "footer.php";
    ?>
    <script>
        function formatNumber(number)
        {
            var newNumber = number;
            if(parseFloat(number) >= 1000)
                newNumber = Math.floor(parseFloat(number) / 1000) + " " + ("00" + (parseFloat(number) % 1000).toFixed(2)).slice(-6);
            newNumber = newNumber.replace(".", ",");
            return newNumber;
        }
        function formatId(id)
        {           
            var old = id.toString();
            var id_format = "";
            for(var j = 0; j < 6; j++)
            {
                if(j < 6 - old.length)
                    id_format += "0";
                else
                    id_format += old[j - (6 - old.length)];
            }
            return id_format;
        }
        var totalPrice = 0;
        function loadCart()
        {
            $('#orderForm > fieldset:first-of-type button[data-toggle="tooltip"]').tooltip("dispose");
            totalPrice = 0;
            document.querySelector("fieldset").innerHTML = "";
            if(cart.products.length > 0)
            {
                document.querySelector("fieldset").innerHTML = "<h3>Produkty</h3>";
                function loadProducts(i)
                {
                    var id = cart.products[i].productId.toString();
                    var count = cart.products[i].amount;
                    var otherProperties = new Array();
                    $.ajax({
                        method: "POST",
                        url: "./ajax/getCartData.php",
                        data: {"id" : id, "count" : count},
                        dataType: "json",
                        success: function(result){
                            otherProperties = result;
                            if(otherProperties.length > 1)
                            {
                                cart.updateProduct(otherProperties[0], otherProperties[3]);
                                var cartItem  = document.createElement("div");
                                cartItem.className ="cartItem";
                                cartItem.innerHTML = "<div class='cartItemImage'><img src='img/productImages/" + otherProperties[0] + "/default/" + otherProperties[6] + "' alt='image'></div><div class='cartItemTitle'><h4><a href='product/" + otherProperties[0]+ "/' target='_blank'>" + (i + 1) + ". " + otherProperties[1].toString().replace(/"/g, '\"') + "</a></h4><small>Kod produktu: " + formatId(otherProperties[0]) + "</small></div><div class='cartItemAmount'><button type='button' class='decrementButton' " + (otherProperties[3] == 1 ? " disabled" : "data-toggle='tooltip' data-trigger='hover' title='Zmniejsz'") + "><span class='bi bi-dash-lg'></span></button><input type='number' class='quantity' min='1' max='" + otherProperties[5] +"' value='" + otherProperties[3] + "'><button type='button' class='incrementButton' " + (otherProperties[3] == otherProperties[5] ? "disabled" : "data-toggle='tooltip' data-trigger='hover' title='Zwiększ'") + "><span class='bi bi-plus-lg'></span></button></div><div class='cartItemPrice'><div>" + otherProperties[3] + " x " + formatNumber(otherProperties[2]) + " zł</div><div>" + formatNumber(otherProperties[4]) + " zł</div></div><div class='cartItemRemove'><button type='button' title='Usuń produkt z koszyka' data-toggle='tooltip'><span class='bi bi-trash-fill'></span></button></div><input type='hidden' name='productIDs[]' value='" + otherProperties[0] + "'><input type='hidden' name='productQuantities[]' value='" + otherProperties[3] + "'>";
                                document.querySelector("fieldset").appendChild(cartItem);  
                                totalPrice +=otherProperties[4];                     
                            }
                            else
                                cart.removeProduct(otherProperties[0]);
                            try
                            {
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                            catch(undefined)
                            {
                                if(window.console)
                                    console.log("Tooltips cannot be displayed correctly. Probably you are using unsupported browser.");
                            }
                        },
                        complete : function(){
                            i++;
                            if(i < cart.products.length)
                                loadProducts(i);
                        }
                    });
                }
                loadProducts(0);
            }
            else
            {
                document.querySelector("fieldset").innerHTML = "<div class='alert alert-danger information'><span class='bi bi-cart-x-fill'></span> <strong>Brak produktów w koszyku. </strong><a href='index.php'>Wróć do sklepu</a></div>";
                [].forEach.call(document.querySelectorAll("#orderForm > fieldset:not(:first-of-type)"), function(el){
                    el.setAttribute("disabled", "");
                });
                document.querySelector("fieldset:last-of-type > div").style.display = "none";
            }       
            try
            {
               $('[data-toggle="tooltip"]').tooltip(); 
            }         
            catch(undefined)
            {
                if(window.console)
                    console.log("Tooltips cannot be displayed correctly. Probably you are using unsupported browser.");
            }
        }
        $(document).on("change, input", ".quantity", function(){
            var id = parseInt(this.parentElement.parentElement.children[1].children[1].textContent.replace("Kod produktu: ", ""));
            cart.updateProduct(id, this.value);
            loadCart();
        });
        $(document).on("click", ".decrementButton", function(){
            if(this.nextElementSibling.value > 1)
            {
                this.nextElementSibling.value--;
                var id = parseInt(this.parentElement.parentElement.children[1].children[1].textContent.replace("Kod produktu: ", ""));
                cart.addProduct(id, -1);
                loadCart();
            }                
        });
        $(document).on("click", ".incrementButton", function(){
            if(this.previousElementSibling.value < parseInt(this.previousElementSibling.getAttribute("max")))
            {
                this.previousElementSibling.value++;
                var id = parseInt(this.parentElement.parentElement.children[1].children[1].textContent.replace("Kod produktu: ", ""));
                cart.addProduct(id, 1);
                loadCart();
            }                
        });
        $(document).on("click", ".cartItemRemove", function(){
            var id = parseInt(this.parentElement.children[1].children[1].textContent.replace("Kod produktu: ", ""));
            cart.removeProduct(id);
            this.parentElement.parentElement.removeChild(this.parentElement);
            location.reload();
        });      
        loadCart();
        function summarize()
        {
            totalPrice = parseFloat(totalPrice).toFixed(2);
            document.querySelector("#totalProductsCost").textContent = "Cena produktów: " + formatNumber(totalPrice) + " zł";
            [].forEach.call(document.querySelectorAll(".optionLabel label span:last-of-type"), function(el){
                if(totalPrice >= 2000.00)
                    el.style.display = "none";                  
                else
                    el.style.display = "block";                  
            });
            if(totalPrice >= 2000.00)
            {
                document.querySelector("#deliveryCost").textContent = "Dostawa: 0,00 zł";
                document.querySelector("#totalCost").textContent = "Łącznie do zapłaty: " + formatNumber(totalPrice) + " zł";
                document.querySelector("fieldset:last-of-type > div").style.display = "block";
            }
            else
            {
                var deliveryCost = -1;
                if(document.querySelector("[name='deliveryOptions']:checked"))
                    deliveryCost = parseFloat(document.querySelector("[name='deliveryOptions']:checked").getAttribute("data-cost"));
                if(deliveryCost == -1)
                    document.querySelector("fieldset:last-of-type > div").style.display = "none";
                else
                {
                    document.querySelector("#deliveryCost").textContent = "Dostawa: " + formatNumber(deliveryCost.toString()) + " zł";
                    document.querySelector("#totalCost").textContent = "Łącznie do zapłaty: " + formatNumber((parseFloat(totalPrice) + parseFloat(deliveryCost)).toFixed(2)) + " zł";
                    document.querySelector("fieldset:last-of-type > div").style.display = "block";
                }
            }
        }
        $(document).ajaxStop(summarize);
        [].forEach.call(document.querySelectorAll("[name='deliveryOptions']"), function(el){
            el.addEventListener("change", function(){
                summarize();
                $("html, body").animate({scrollTop : document.body.scrollHeight}, 1200);
            }, false);
        });
        [].forEach.call(document.querySelectorAll("fieldset:nth-of-type(4) input"), function (el){
            el.addEventListener("input", function fn(){
                el.removeEventListener("input", fn);
                (function(){$(el).addClass("validatable");}).apply(this, arguments);
            });
        });
    </script>
    <?php
        if(isset($_SESSION["cart_errors"]))
        {
            $message = "Wystąpiły nieoczekiwane błędy podczas składania zamówienia: <ul>";
            foreach($_SESSION["cart_errors"] as $error)
                $message.="<li style=\"font-weight: normal;\">$error</li>";
            $message.="</ul>";
            $message.="Prosimy spróbować ponownie.";
            echo "<script>";
            echo "showAlert('$message', true, 7000);";
            echo "</script>";
            unset($_SESSION["cart_errors"]);
        }
    ?>
</body>
</html>