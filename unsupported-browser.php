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
    <title>Zaktualizuj swoją przeglądarkę | Strona główna</title>
    <base href="http://127.0.0.1/MatiTechShop/">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <style>
        *
        {
            margin: 0;
            padding: 0;
        }
        body
        {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            padding: 0;
        }
        .browsers li
        {
            list-style-type: none;
            display: inline-block;
            vertical-align: top;
            background-color: white;
            border: 2px solid dodgerblue;
            width: 86px;
            height: 86px;
            font-weight: bold;
            padding: 5px;
            font-size: 14px;
        }
        .browsers li a
        {
            color: black;
            text-decoration: none;
        }
        .browsers img
        {
            border: 0;
        }
        h2
        {
            margin: 5px;
        }
        @supports(display: grid)
        {
            body > div
            {
                height: initial !important;
                width: initial !important;
                position: initial !important;
                margin: initial !important;
            }
            body
            {
                display: grid;
                place-items: center;
            }
        }
    </style>
    <!--[if lt IE 8]>
        <style>
            .browsers li
            {
                display: inline;
                zoom: 1;
                margin: 2px;
            }
        </style>
    <![endif]-->
</head>
<body style="height: 100%; background-color: rgb(176, 196, 222); text-align: center;">
    <div style="border-radius: 5px; box-shadow: 0 2px 16px 3px rgba(0, 0, 0, .2); width: 800px; background-color:white; left: 50%; margin-left: -400px; position: absolute; top: 50%; margin-top: -200px;">
        <img src="./img/unsupportedBrowser/logo.jpg" alt="Logo">
        <h2>Nieobsługiwana przeglądarka</h2>
        <div style="background-color: rgb(215, 215, 215); margin: 10px; padding: 5px;">
        <b>Korzystasz z przeglądarki, która nie jest w stanie zapewnić poprawnego działania strony MatiTechShop.</b>
            <p>Na szczęście zapewniamy bardzo szerokie wsparcie wielu przeglądarek. Użyj jednej z nich, aby móc skorzystać z naszego sklepu:</p>
        </div>
        <ul class="browsers">
        <li><a href="http://google.com/chrome"><img src="./img/unsupportedBrowser/chrome.jpg" alt="Google Chrome" width="60"><br>Chrome 49+</a></li>
            <li><a href="http://microsoft.com/edge"><img src="./img/unsupportedBrowser/edge.jpg" alt="Google Chrome" width="60"><br>Edge 12+</a></li>
            <li><a href="http://apple.com/safari"><img src="./img/unsupportedBrowser/safari.jpg" alt="Google Chrome" width="60"><br>Safari 9+</a></li>
            <li><a href="http://mozilla.org/firefox"><img src="./img/unsupportedBrowser/firefox.jpg" alt="Google Chrome" width="60"><br>Firefox 52+</a></li>
            <li><a href="http://opera.com"><img src="./img/unsupportedBrowser/opera.jpg" alt="Google Chrome" width="60"><br>Opera 36+</a></li>
            <li><a href="http://microsoft.com/ie"><img src="./img/unsupportedBrowser/ie.jpg" alt="Google Chrome" width="60"><br>IE 9+</a></li>
        </ul>
        <div style="text-align: right; color: gray; margin: 5px; font-style: italic;">
            &copy; 2025 MH Corp.
        </div>
    </div>
</body>
</html>