<?php
    session_start();
    if(isset($_SESSION["logged"]) && $_SESSION["logged"] == true)
    {
        header("Location: index.php");
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
        $tests = array(validateLogin($reg_login), validatePassword($reg_password), validatePassword2($reg_password2, $reg_password), validateEmail($reg_email), validateDate($reg_date), validateRegulations((isset($_POST["reg_regulations"]))?("true"):("false")), validateCaptcha());
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
            header("Location: index.php");
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
    <base href="http://127.0.0.1/sklep/">
    <link rel="shortcut icon" href="/sklep/img/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="jquery-ui-1.13.2.custom/jquery-ui.min.css">
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <link rel="stylesheet" href="/sklep/style.css">
    <script src="./js/cart.js"></script>
    <!--[if lte IE 9]>
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
        <article class="col-sm-10 col-md-7" id="registrationForm">
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
                            <button type="button" class="btn btn-secondary" title="Pokaż"><span class="bi bi-eye-fill"></span></button>
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
                            <button type="button" class="btn btn-secondary" title="Pokaż"><span class="bi bi-eye-fill"></span></button>
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
                    <input type="date" name="reg_date" class="form-control" max="<?php echo date("Y-m-d");?>" id="date" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Wybierz swoją datę urodzenia.</b><br>Pozwoli to na lepsze spersonalizowanie ofert." value="<?php
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
                    <input type="tel" name="reg_tel" class="form-control" placeholder="Numer telefonu" id="phone" data-toggle="tooltip" data-placement="right" data-html="true" title="<b>Podaj swój numer telefonu.</b><i>(opcjonalne)</i>" value="<?php
                        if(isset($_SESSION["remember_phone"]))
                        {
                            echo $_SESSION["remember_phone"];
                            unset($_SESSION["remember_phone"]);
                        }
                    ?>">             
                </div>
                <div>
                    <input type="checkbox" name="reg_regulations" id="regulations" required>                  
                    <label for="regulations"><span title="To pole jest wymagane." class="requiredField" data-toggle="tooltip"><sup>*</sup></span>Akceptuję <a href="#">regulamin</a> MatiTechShop.</label>   
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
                <div>
                    <div class="g-recaptcha" id="captcha" name="reg_recaptcha" data-sitekey="6LdsoMAiAAAAAJLFIG1GF--fzgSMB2eVVgWyOMZ1" data-callback='onSubmit' data-action='submit'></div>     
                    <div>
                        <?php
                            if(isset($_SESSION["reg_error_captcha"]))
                            {
                                echo "<div class='invalid-tooltip'>".$_SESSION["reg_error_captcha"]."</div>";
                                unset($_SESSION["reg_error_captcha"]);
                            }
                        ?>
                    </div>
                </div>                                 
                <input type="submit" value="Zarejestruj się" id="registrationButton">
            </form>
            <hr>
            <strong>Masz już konto? </strong><a href="login">Zaloguj się</a>        
        </article>
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <script src="jquery-ui-1.13.2.custom/jquery-ui.min.js"></script>
    <script src="jquery-ui-1.13.2.custom/datepicker-pl.js"></script>
    <script>        
        $("#login, #password, #password2, #email, #date, #regulations").on("keyup change", function(){
            var element = this;
            $.ajax({
            method: "POST",
            url: "test.php",
            data: {"property" : element.id, "q" : ((element.id == "regulations") ? (element.checked) : (element.value)), "tmp" : ((element.id == "password2") ? (document.querySelector("#password").value) : ("false"))},
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
            if(element.id == "password")
                $("#password2").keyup();
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
                    this.title = "Ukryj";
                    this.innerHTML = "<span class='bi bi-eye-slash-fill'></span>";
                }
                else
                {
                    this.parentElement.parentElement.children[0].type = "password";
                    this.title = "Pokaż";
                    this.innerHTML = "<span class='bi bi-eye-fill'></span>";
                }
            }, false);
        });
        $('[data-toggle="tooltip"]:not(#login, #password, #password2, #email, #date, #phone)').tooltip();
        $("#login, #password, #password2, #email, #date, #phone").tooltip({"trigger" : "focus"});
    </script>
</body>
</html>