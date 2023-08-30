<?php
    error_reporting(0);
    session_start();
    if(!isset($_SESSION["authenticate_order"]) || $_SESSION["authenticate_order"] !== true)
    {
        header("Location: index.php");
        exit();
    }
    else
        unset($_SESSION["authenticate_order"]);
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
    <title>Zamówienie zostało złożone! | MatiTechShop</title>
    <base href="http://127.0.0.1/sklep/">
    <link rel="shortcut icon" href="/sklep/img/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css" integrity="sha384-LrVLJJYk9OiJmjNDakUBU7kS9qCT8wk1j2OU7ncpsfB3QS37UPdkCuq3ZD1MugNY" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="/sklep/style.css">
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
        <div class='alert alert-success information'><strong>Zamówienie zostało pomyślnie złożone. Dziękujemy!</strong></div>
        <a href="index.php">Wróć do strony głównej</a>
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <script>
        cart.removeAll();
    </script>
</body>
</html>