<?php
    error_reporting(0);
    session_start();
    if(isset($_POST["stars"]))
    {
        require "connect.php";
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        $connect->set_charset('utf8mb4');
        $stars = $_POST["stars"];
        $review = (isset($_POST["review"]) && $_POST["review"]) ? (substr($_POST["review"], 0, 500)) : (null);
        $product = $_POST["product"];
        $query = $connect->prepare("INSERT INTO produkt_opinia(opinia, opis, produkt_id, uzytkownik_id) VALUES(?, ?, ?, ?)");
        $query->bind_param('dsii', $stars, $review, $product, $_SESSION["user_data"]["user_id"]);
        $query->execute();
        $connect->close();
        header("Location: product/".$product."/");
        exit();
    }
    if(!isset($_GET["id"]))
    {
        header("Location: product/1/");
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
    <title>MatiTechShop - wspaniałe oferty! | Strona główna</title>
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
    <!--[if IE 9]>
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
    <div class="fullscreen">
        <button type="button" title="Zamknij" data-toggle="tooltip">&#x0078;</button>
        <button id="back" type="button" title="Wstecz" data-toggle="tooltip">&lt;</button>
        <div id="imageInfo"></div>
        <img src="#" alt="image">
        <button id="forward" type="button" title="Dalej" data-toggle="tooltip">&gt;</button>
    </div>
    <main class="contentContainer">        
        <?php
            require "connect.php";
            $connect = new mysqli($host, $db_user, $db_password, $db_name);
            $connect->set_charset('utf8mb4');
            $query = $connect->prepare('SELECT * FROM produkt WHERE produkt_id = ?');
            $query->bind_param('i', $_GET["id"]);
            $query->execute();
            $result = $query->get_result();
            if($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                echo "<script>document.title='".$row["nazwa"]." | MatiTechShop';</script>";
                echo "<h2>Szczegóły produktu</h2>";
                $query3 = $connect->prepare('SELECT * FROM produkt_kategoria INNER JOIN kategoria USING(kategoria_id) WHERE produkt_id = ?');
                $query3->bind_param('i', $_GET["id"]);
                $query3->execute();
                $result3 = $query3->get_result();                
                if($result3->num_rows > 0)
                {
                    $tmp_arr = array();
                    while($row3 = $result3->fetch_assoc())
                        array_push($tmp_arr, "<a href='category/".$row3["kategoria_id"]."/'>".$row3["nazwa"]."</a>");
                    echo "<i>[Należy do kategorii: ".implode(", ", $tmp_arr)."]</i>";
                }
                if(isset($_SESSION["logged"]) && $_SESSION["user_data"]["position"] > 1)
                {
                    echo "<div class='modal fade' id='staticBackdrop' data-backdrop='static' data-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>";
                    echo "<div class='modal-dialog modal-dialog-centered'>";
                    echo "<form action='index.php' method='POST'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='staticBackdropLabel'>Potwierdź usunięcie</h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "Czy <strong>NA PEWNO chcesz USUNĄĆ</strong> produkt \"".$row["nazwa"]."\"?<br><br><strong>Ta operacja jest nieodwracalna!</strong>";
                    echo "</div>";
                    echo "<div class='modal-footer'>";
                    echo "<input type='hidden' name='mode' value='deleteProduct'>";
                    echo "<input type='hidden' name='item' value='".$row["produkt_id"]."'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Anuluj</button>";
                    echo "<button type='submit' class='btn btn-danger'>Usuń</button>";
                    echo "</div>";
                    echo "</div>";  
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='dropdown' id='productOptions'>";
                    echo "<button type='button' class='dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><span class='bi bi-gear-fill' title='Narzędzia administracyjne' data-toggle='tooltip'></span></button>";
                    echo "<div class='dropdown-menu dropdown-menu-right'>";
                    echo "<a class='dropdown-item' href='./productform.php?mode=edit&id=".$row["produkt_id"]."'>Edytuj produkt</a>";
                    echo "<a class='dropdown-item' id='deleteProduct' data-toggle='modal' data-target='#staticBackdrop'>Usuń ten produkt</a>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "<article class='productView'>";
                echo "<section class='productFeatures'>";
                echo "<button class='productSectionButton' type='button'>Zdjęcia</button>";
                echo "<div class='productSection'>";
                echo "<div>";
                $path = './img/productImages/'.$row["produkt_id"].'/';
                $files = array_diff(scandir($path), array(".", "..", "default"));   
                echo "<div class='imgContainer";
                if(count($files) == 0)
                    echo " oneImage";
                echo "'><img src='".$path."/"."default/".scandir($path."/"."default/")[2]."' alt='laptop' title='Zobacz zdjęcie w galerii' data-toggle='tooltip'></div>";                
                echo "<div";
                if(count($files) == 0)
                    echo " class='oneImage'";
                echo ">";
                foreach($files as $file)
                {
                    echo "<div class='imgContainer'>";
                    echo "<img src='".$path.$file."' alt='laptop' title='Zobacz zdjęcie w galerii' data-toggle='tooltip'>"; 
                    echo "</div>";
                }                  
                echo "</div>";  
                echo "</div>";      
                echo "</div>";
                $query2 = $connect->prepare('SELECT parametr, wartosc FROM produkt_parametr INNER JOIN parametr USING(parametr_id) WHERE produkt_id = ?');
                $query2->bind_param('i', $_GET["id"]);
                $query2->execute();
                $result2 = $query2->get_result();
                if($result2->num_rows > 0)
                {
                    echo "<button class='productSectionButton' type='button'>Dane techniczne</button>";
                    echo "<div class='productSection productData'>";
                    echo "<table>";
                    echo "<tr><th>Parametr</th><th>Wartość</th></tr>";
                    while($row2 = $result2->fetch_assoc())
                        echo "<tr><td>".$row2["parametr"]."</td><td>".$row2["wartosc"]."</td></tr>";
                    echo "</table>";
                    echo "</div>";
                }
                $result2->free_result();
                if($row["opis"] != "")
                {
                    echo "<button class='productSectionButton' type='button'>Opis</button>";
                    echo "<div class='productSection productDescription'><br>";
                    if($row["najlepsza_cecha"] != "")
                    echo "<span id='bestFeature'><span class='bi bi-star-fill'></span>".$row["najlepsza_cecha"]."</span><br><br>";
                    echo $row["opis"];
                    echo "<br><br></div>";
                }
                echo "<button class='productSectionButton' type='button'>Opinie</button>";
                echo "<div class='productSection'>";
                if(isset($_SESSION["logged"]))
                {
                    echo "<form action='product.php' method='POST'>";
                    echo "<h4>Dodaj swoją opinię</h4>";
                    echo "<div id='addNewOpinion'>";                    
                    echo "<div id='starCounter'>";
                    echo "<span><span class='bi bi-star-fill'></span>5,0</span>";
                    echo "<input type='range' min='1' max='5' value='5.0' step='0.1' orient='vertical' list='starTickmarks' name='stars' required>";
                    echo "<datalist id='starTickmarks'><option value='2'></option><option value='3'></option><option value='4'></option></datalist>";
                    echo "</div>";
                    echo "<div id='opinionDescription'>";
                    echo "<textarea maxlength='500' placeholder='Napisz swoją opinię o tym produkcie tutaj...' name='review'></textarea>";
                    echo "<p id='lettersCounter'>0/500</p>";
                    echo "</div></div>";
                    echo "<input type='hidden' name='product' value='".$_GET["id"]."'>";
                    echo "<input type='submit' id='opinionButton' value='Prześlij'>";
                    echo "</form>";
                }
                $queryReview = $connect->prepare('SELECT * FROM produkt_opinia INNER JOIN uzytkownik USING(uzytkownik_id) WHERE produkt_id = ? ORDER BY produkt_opinia_id DESC');
                $queryReview->bind_param('i', $_GET["id"]);
                $queryReview->execute();
                $resultReview = $queryReview->get_result();    
                if($resultReview->num_rows > 0)
                {
                    while($rowReview = $resultReview->fetch_assoc())
                    {
                        echo "<div class='productOpinion'><a href='#'>@".$rowReview["nazwa_uzytkownika"]."</a> | <b>Opinia: ".$rowReview["opinia"]."/5</b>";
                        if($rowReview["opis"])
                            echo "<br><i>".htmlspecialchars($rowReview["opis"])."</i>";
                        echo "</div>";
                    }                                        
                }
                else
                    echo "<div class='alert alert-primary'><strong>Ten produkt nie ma jeszcze opinii i recenzji.</strong></div>";        
                echo "</div>";
                echo "</section>";
                echo "<section class='productSummary'>";
                echo "<div>";
                echo "<h3>".$row["nazwa"]."</h3>";
                echo "<h6><span class='bi bi-info-circle-fill'></span> Kod produktu: ".str_pad($row["produkt_id"], 6, "0", STR_PAD_LEFT)."</h6>";
                echo "</div>";                        
                echo "<div>";
                $mainPrice = $row["cena"];
                if($row["promocja"])
                {
                    echo "<div class='discount'>".number_format($row["cena"], 2, ",", " ")."</div>";
                    $mainPrice = $row["promocja"];
                }                       
                echo "<div style='text-align: center; font-weight: bold; padding-bottom: 1rem;'>";
                echo "<div style='font-size: 3rem; display:inline-block; line-height: 1; margin-right: 3px;'>".number_format(floor($mainPrice), 0, '.', ' ')."</div>";
                echo "<div style='display: inline-block; vertical-align: top;'>";
                echo "<div style='font-size: 1.5rem; line-height: 1.4;'>".explode(".", $mainPrice)[1]."</div>";
                echo "<div style='font-size: 0.9rem; line-height: 1;'>zł</div>";
                echo "</div></div>";
                $amount = $row["ilosc"];
                if($amount > 0)
                {
                    echo "<button type='button' class='addToCart'><span class='bi bi-basket-fill'></span>Dodaj do koszyka</button>";
                    echo "<script>document.querySelector('.addToCart').addEventListener('click', function(){if (".$row["ilosc"]." > 0) {cart.addProduct(".$row["produkt_id"].", 1); showAlert('Pomyślnie dodano produkt do koszyka.', false, 2000);}}, false);</script>";
                    echo "<br>Dostępne sztuki: "; 
                }                                           
                if($amount <= 0)
                    echo "<span style='color: gray; font-weight: bold;'>Produkt niedostępny</span>";
                elseif($amount < 10)
                    echo "<span style='color: orangered; font-weight: bold;'>".$amount."</span>";
                elseif($amount < 100)
                    echo "<span style='color: #DAA520; font-weight: bold;'>".$amount."</span>";
                else 
                    echo "<span style='color: green; font-weight: bold;'>".$amount."</span>";
                echo "</div>";
                echo "<div><hr>";
                if($row["gwarancja"])
                    echo "<b>Gwarancja:</b> ".$row["gwarancja"]." miesiące/y<br>";
                if($row["cena"] >= 2000)
                    echo "<span style='color: green; font-weight: bold;'>Bezpłatna dostawa</span>";
                else
                    echo "<span style='color: blue; font-weight: bold;'>Dostawa od 9,99 zł</span>";
                echo "</div>";
                echo "</section>";
                echo "</article>";
            }
            else
                echo "<div class='alert alert-danger information'><strong>Błąd 404: Nieprawidłowy identyfikator produktu.</strong> <a href='index.php'>Wróć na stronę główną</a></div>";
            $result->free_result();
            $connect->close();
        ?> 
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="./js/gallery.js"></script>
    <script>
        [].forEach.call(document.querySelectorAll(".productSectionButton"), function(el){
            el.addEventListener("click", function(){
                $(this).toggleClass("activeSection");
                var section = this.nextElementSibling;
                if(section.style.maxHeight)
                    section.style.removeProperty("max-height");
                else
                    section.style.maxHeight = section.scrollHeight + "px";
            }, false);
            el.click();
        });
        window.addEventListener("resize", function(){
            [].forEach.call(document.querySelectorAll(".productSectionButton.activeSection"), function(el){
                var section = el.nextElementSibling;
                section.style.transition = "none";
                section.style.maxHeight = section.scrollHeight + "px";
                //Force reflow
                section.offsetHeight;
                section.style.removeProperty("transition");
            });
        }, false);
        $('[data-toggle="tooltip"]').tooltip();
    </script>
    <?php
        if(isset($_SESSION["logged"]))
            echo "<script src='./js/opinion.js'></script>";
    ?>
</body>
</html>