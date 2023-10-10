<?php
    error_reporting(0);
    session_start();
    if(isset($_SESSION["logged"]) && $_SESSION["logged"])
    {
        header("Location: index.php");
        exit();
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
    <title>Zaloguj się | MatiTechShop</title>
    <base href="http://127.0.0.1/sklep/">
    <link rel="shortcut icon" href="/sklep/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="/sklep/style.css">
    <noscript>
        <link rel="stylesheet" href="noscriptstyle.css">
    </noscript>
    <script src="./js/cart.js"></script>
    <!--[if IE 9]>
        <link rel="stylesheet" href="ie9polyfill.css">
    <![endif]-->
</head>
<body id="loginBackground">
   <?php
        header('Content-Type: text/html; charset=utf-8');
        include "animation.html";
        include "header.php";
   ?>
    <main class="contentContainer">
        <article id="loginForm">
            <section>
                <?php
                    if(isset($_SESSION["error"]))
                    {
                        echo $_SESSION["error"];
                        unset($_SESSION["error"]);
                    }
                ?>
                <h1>Zaloguj się</h1>
                <form action="logging-in.php" method="post">
                    <div>                       
                        <input type="text" id="username" name="username" placeholder="Login" required>
                        <label for="username"><span title="To pole jest wymagane." class='requiredField' data-toggle="tooltip"><sup>*</sup></span>Login</label>
                    </div>
                    <div>
                        <input type="password" id="password" name="password" placeholder="Hasło" required>
                        <label for="password"><span title="To pole jest wymagane." class='requiredField' data-toggle="tooltip"><sup>*</sup></span>Hasło</label>
                    </div>
                    <input type="submit" value="Zaloguj się" id="loginButton">
                </form><br>
                <div>
                    <strong>Nie masz jeszcze konta? </strong><a href="registration">Zarejestruj się</a>
                </div>
            </section>
            <section>
                <div>
                    <div>
                        <b>Czy wiesz, że...</b>
                        <p id="typedText">Możesz przeglądać zdjęcia produktu bez wchodzenia na jego szczegóły.</p>
                    </div>
                </div>
            </section>
        </article>
    </main>
    <?php
        include "footer.php";
    ?>
    <script>
        $('[data-toggle="tooltip"]').tooltip();
    </script>
</body>
</html>