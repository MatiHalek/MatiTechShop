<?php
    error_reporting(0);
    session_start();
    if(isset($_SESSION["logged"]) && $_SESSION["logged"])
    {
        header("Location: ./");
        exit();
    }
    require "functions.php";
    if(isset($_POST["reg_login"]))
    {
        $success = true;
        $reg_login = $_POST["reg_login"];
        $reg_email = $_POST["reg_email"];
        $reg_password = $_POST["reg_password"];
        $reg_password2 = $_POST["reg_password2"];
        $reg_date = $_POST["reg_date"];
        $reg_phone = $_POST["reg_tel"];
        $tests = array(ValidateLogin($reg_login), ValidatePassword($reg_password, $reg_login), ValidatePassword2($reg_password2, $reg_password), ValidateEmail($reg_email), ValidateDate($reg_date),ValidateRegulations((isset($_POST["reg_regulations"]))?("true"):("false")), ValidateCaptcha());
        foreach($tests as $i)
        {
            if(!$i["passed"])
            {
                $success = false;
                $_SESSION["reg_error_".$i["parameter"]] = $i["note"];
            }               
        }  
        $_SESSION["remember_login"] = $reg_login;
        $_SESSION["remember_email"] = $reg_email;
        $_SESSION["remember_date"] = $reg_date;
        $_SESSION["remember_phone"] = $reg_phone;                
        if($success)
        {
            $pass_hash = password_hash($reg_password, PASSWORD_DEFAULT);
            require "connect.php";
            $connect = new mysqli($host, $db_user, $db_password, $db_name);
            $connect->set_charset('utf8mb4');
            $query = $connect->prepare("INSERT INTO uzytkownik(nazwa_uzytkownika, haslo, email, data_urodzenia, numer_telefonu, czy_zalogowany, stanowisko_id) VALUES(?, ?, ?, ?, ?, 1, 1)");
            $query->bind_param('sssss', $reg_login, $pass_hash, $reg_email, $reg_date, $reg_phone);
            $query->execute();
            
            unset($_SESSION["remember_login"]);
            unset($_SESSION["remember_email"]);
            unset($_SESSION["remember_date"]);
            unset($_SESSION["remember_phone"]);
            $_SESSION["logged"] = true;
            $_SESSION["user_id"] = mysqli_insert_id($connect);
            $_SESSION["username"] = $reg_login;
            $_SESSION["position"] = 1;
            $_SESSION["user_data"] = array(
                "user_id" => mysqli_insert_id($connect),
                "username" => $reg_login,
                "email" => $reg_email,
                "birth_date" => $reg_date,
                "phone_number" => $reg_phone,
                "first_name" => null,
                "surname" => null,
                "postcode" => null,
                "city" => null,
                "street" => null,
                "house_number" => null,
                "position" => 1
            );
            $connect->close();
            header("Location: ./");
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
    <title>Załóż nowe konto | MatiTechShop</title>
    <base href="http://127.0.0.1/MatiTechShop/">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="jquery-ui-1.13.2.custom/jquery-ui.min.css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LfiUfknAAAAAKF3I0Lw4sYPLhNeU2eEhLFvtd9C"></script>
    <link rel="stylesheet" href="style.css">
    <noscript>
        <link rel="stylesheet" href="noscriptstyle.css">
    </noscript>
    <script src="./js/cart.js"></script>
    <!--[if IE 9]>
        <link rel="stylesheet" href="ie9polyfill.css">
    <![endif]-->
</head>
<body id="registrationBackground">
   <?php
        header('Content-Type: text/html; charset=utf-8');
        include "animation.html";
        include "header.php";
   ?>   
    <main class="mainRegistration contentContainer">
        <article class="col-sm-10 col-md-8 col-lg-7" id="registrationForm">
            <?php
                if(isset($_SESSION["reg_error_captcha"]))
                {
                    echo "<div class='alert alert-danger font-weight-bold shadow'>Weryfikacja za pomocą systemu reCAPTCHA nie powiodła się. Prosimy spróbować ponownie.<br><small>Uwaga: Być może korzystasz z nieobsługiwanej przeglądarki. <a href='https://support.google.com/recaptcha/answer/6223828?hl=pl' target='_blank'>Dowiedz się więcej</a></small></div>";           
                    unset($_SESSION["reg_error_captcha"]);
                }
            ?>
            <h1>Zarejestruj się</h1>
            <h6>Wykorzystaj w pełni możliwości naszego sklepu</h6><br>
            <form action="registration" method="POST"> 
                <div class="input">
                    <label for="login" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Login</label>
                    <input type="text" name="reg_login" minlength="3" maxlength="15" placeholder="Login" id="login" class="form-control" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Login będzie służył do logowania do sklepu. Stanie się także Twoją nazwą użytkownika.</b><br>Musi być unikalny." value="<?php
                        if(isset($_SESSION["remember_login"]))
                        {
                            echo $_SESSION["remember_login"];
                            unset($_SESSION["remember_login"]);
                        }
                    ?>" required> 
                    <div>
                        <?php
                            if(isset($_SESSION["reg_error_login"]))
                            {
                                echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_login"]."</div>";
                                unset($_SESSION["reg_error_login"]);
                            }
                        ?>
                    </div>              
                </div>
                <div class="input">
                    <label for="password" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Hasło</label> 
                    <div class="input-group">                       
                        <input type="password" name="reg_password" placeholder="Hasło" id="password" class="form-control" minlength="8" maxlength="255" data-toggle="tooltip" data-placement="left" data-html="true" title="<b>Wymyśl silne hasło.</b><br>Nie udostępniaj go nikomu." required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" title="Pokaż znaki" data-toggle="tooltip"><span class="bi bi-eye-fill"></span></button>
                        </div>
                        <div>
                            <?php
                                if(isset($_SESSION["reg_error_password"]))
                                {
                                    echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_password"]."</div>";
                                    unset($_SESSION["reg_error_password"]);
                                }
                            ?>
                        </div>
                    </div>      
                </div> 
                <div class="input">
                    <label for="password2" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Powtórz hasło</label>                         
                    <div class="input-group"> 
                        <input type="password" name="reg_password2" class="form-control" placeholder="Powtórz hasło" id="password2" minlength="8" maxlength="255" data-toggle="tooltip" data-placement="left" data-html="true" title="<b>Powtórz wpisane wcześniej hasło.</b>" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" title="Pokaż znaki" data-toggle="tooltip"><span class="bi bi-eye-fill"></span></button>
                        </div>
                        <div>
                            <?php
                                if(isset($_SESSION["reg_error_password2"]))
                                {
                                    echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_password2"]."</div>";
                                    unset($_SESSION["reg_error_password2"]);
                                }
                            ?>
                        </div>
                    </div>                    
                </div> 
                <div class="input">
                    <label for="email" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>E-mail</label>
                    <input type="email" name="reg_email" class="form-control" placeholder="E-mail" id="email" maxlength="254" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Połącz konto ze swoim adresem e-mail.</b>" value="<?php
                        if(isset($_SESSION["remember_email"]))
                        {
                            echo $_SESSION["remember_email"];
                            unset($_SESSION["remember_email"]);
                        }
                    ?>" required>
                    <div>
                        <?php
                            if(isset($_SESSION["reg_error_email"]))
                            {
                                echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_email"]."</div>";
                                unset($_SESSION["reg_error_email"]);
                            }
                        ?>
                    </div>            
                </div>
                <div class="input">
                    <label for="date" class="labelText"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Data urodzenia</label>
                    <input type="date" name="reg_date" class="form-control" max="<?php echo date("Y-m-d");?>" id="date" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Podaj swoją datę urodzenia.</b>" value="<?php
                        if(isset($_SESSION["remember_date"]))
                        {
                            echo $_SESSION["remember_date"];
                            unset($_SESSION["remember_date"]);
                        }
                    ?>" required>
                    <div>
                        <?php
                            if(isset($_SESSION["reg_error_date"]))
                            {
                                echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_date"]."</div>";
                                unset($_SESSION["reg_error_date"]);
                            }
                        ?>
                    </div>             
                </div>
                <div class="input">
                    <label for="phone" class="labelText">Numer telefonu</label>  
                    <input type="tel" name="reg_tel" class="form-control" placeholder="Numer telefonu" id="phone" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Podaj swój numer telefonu.</b><i>(opcjonalne)</i><br>Uzupełnimy go automatycznie przy składaniu zamówienia." value="<?php
                        if(isset($_SESSION["remember_phone"]))
                        {
                            echo $_SESSION["remember_phone"];
                            unset($_SESSION["remember_phone"]);
                        }
                    ?>">             
                </div>
                <div>
                    <input type="checkbox" name="reg_regulations" id="regulations" required>                  
                    <label for="regulations"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Akceptuję <a href="./" target="_blank">regulamin</a> MatiTechShop.</label>   
                    <div>
                        <?php
                            if(isset($_SESSION["reg_error_regulations"]))
                            {
                                echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_regulations"]."</div>";
                                unset($_SESSION["reg_error_regulations"]);
                            }
                        ?>
                    </div>
                </div>                                             
                <input type="submit" value="Zarejestruj się" id="registrationButton" class="g-recaptcha" data-size="invisible" data-badge="left" data-sitekey="6LfiUfknAAAAAKF3I0Lw4sYPLhNeU2eEhLFvtd9C" data-callback='OnSubmit' data-action='submit'>
            </form>
            <hr>
            <strong>Masz już konto? </strong><a href="login">Zaloguj się</a>        
        </article>
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="jquery-ui-1.13.2.custom/datepicker-pl.js"></script>
    <script>        
        $("#login, #password, #password2, #email, #date, #regulations").on("keyup change input", function(){         
            var element = this;
            var data = new Object();
            data.property = element.id;
            if(element.id == "regulations")
                data.q = element.checked;
            else
                data.q = element.value;
            if(element.id == "password2")
                data.tmp = document.querySelector("#password").value;
            else if(element.id == "password")
                data.tmp = document.querySelector("#login").value;
            $.ajax({
            method: "POST",
            url: "test.php",
            data: data,
            success: 
            function(result){
                if(result.lastIndexOf("<div class='invalid-tooltip'>") == 0)
                {
                    $("#" + element.id + " ~ div:last-of-type").html(result);
                    $(element).removeClass("valid").addClass("invalid");
                }
                else
                {
                    $("#" + element.id + " ~ div:last-of-type").html("");
                    $(element).removeClass("invalid").addClass("valid");
                }
            }});
            if(element.id == "password" && document.querySelector("#password2").value)
                $("#password2").keyup();
            if(element.id == "login" && document.querySelector("#password").value)
                $("#password").keyup();
        });
        if(document.querySelector("#date").type != "date")
            $("#date").datepicker({
                dateFormat: "yy-mm-dd",
            });
        [].forEach.call(document.querySelectorAll("input[type='password'] ~ .input-group-append > button"), function(el){
            el.addEventListener("click", function(){
                if(this.parentElement.parentElement.children[0].type == "password")
                {
                    this.parentElement.parentElement.children[0].type = "text";
                    this.title = "Ukryj znaki";
                    this.innerHTML = "<span class='bi bi-eye-slash-fill'></span>";
                }
                else
                {
                    this.parentElement.parentElement.children[0].type = "password";
                    this.title = "Pokaż znaki";
                    this.innerHTML = "<span class='bi bi-eye-fill'></span>";
                }
                $(this).tooltip("dispose").tooltip("show");
            }, false);
        });
        $('[data-toggle="tooltip"]:not(#login, #password, #password2, #email, #date, #phone)').tooltip();
        $("#login, #password, #password2, #email, #date, #phone").tooltip({"trigger" : "focus"});
        if(window.matchMedia)
        {
            var mediaQueryList = window.matchMedia("(min-width: 768px)");
            function ToggleTooltips(mql)
            {
                if(!mql.matches)
                    $("#login, #password, #password2, #email, #date, #phone").tooltip("dispose");
                else
                    $("#login, #password, #password2, #email, #date, #phone").tooltip({"trigger" : "focus"});
            }
            if(mediaQueryList.addEventListener)
                mediaQueryList.addEventListener("change", ToggleTooltips, false);
            else
                mediaQueryList.addListener(ToggleTooltips);
            ToggleTooltips(mediaQueryList); 
        }
        function OnSubmit(token)
        {
            if(document.querySelector("main form").checkValidity())
                document.querySelector("main form").submit();
            else
                document.querySelector("main form").reportValidity();
        }
    </script>
</body>
</html>