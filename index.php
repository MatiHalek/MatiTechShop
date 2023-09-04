<?php
    error_reporting(0);
    session_start();
    if(isset($_POST["mode"], $_SESSION["logged"]) && $_SESSION["user_data"]["position"] > 1)
    {
        require "connect.php";
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        $connect->set_charset('utf8mb4');
        if($_POST["mode"] == "delete")
        {
            $query = $connect->prepare("DELETE FROM kategoria WHERE kategoria_id = ?");
            $query->bind_param('i', $_POST["item"]);
            $query->execute();
        }
        if($_POST["mode"] == "deleteProduct")
        {
            $query = $connect->prepare("DELETE FROM produkt WHERE produkt_id = ?");
            $query->bind_param('i', $_POST["item"]);
            $query->execute();
        }
        if($_POST["mode"] == "add")
        {
            $errors = [];
            if(empty($_POST["categoryName"]))
                array_push($errors, "Nazwa kategorii nie może być pusta.");
            if($_FILES["categoryImage"]["error"] !== 0)
                array_push($errors, "Wystąpił nieoczekiwany błąd podczas przesyłania pliku. Upewnij się, że przesłano zdjęcie i spróbuj ponownie.");
            else
            {
                $category_name = htmlspecialchars($_POST["categoryName"]).mb_substr(0, 25);
                $file_name = $_FILES["categoryImage"]["name"];
                $tmp_name = $_FILES["categoryImage"]["tmp_name"];
                $target_dir = "img/categoryImages/";
                $extensions = array("png", "jpg", "jpeg", "gif", "bmp", "tiff");
                $tmp = explode(".", $file_name);
                $file_ext = strtolower(end($tmp));
                if($_FILES["categoryImage"]["size"] > 2097152)
                    array_push($errors, "Plik jest za duży: maksymalny rozmiar pliku wynosi 2 MB.");
                if(!in_array($file_ext, $extensions))
                    array_push($errors, "Błędne rozszerzenie pliku: wybierz plik graficzny o rozszerzeniu .png, .jpg, .jpeg, .gif, .bmp lub .tiff.");
                else
                {
                    $info = getimagesize($tmp_name);
                    list($width, $height) = $info;
                    if($width < 500)
                        array_push($errors, "Szerokość obrazu jest za mała ($width px): powinna wynosić minimum 500 pikseli.");
                    if($height < 500)
                        array_push($errors, "Wysokość obrazu jest za mała ($height px): powinna wynosić minimum 500 pikseli.");
                    if(count($errors) == 0)
                    {
                        if(!move_uploaded_file($tmp_name, $target_dir.$file_name))
                            array_push($errors, "Wystąpił nieoczekiwany błąd podczas przesyłania pliku. Spróbuj ponownie.");
                    }  
                }                                 
            }
            if(empty($errors))
            {
                $query = $connect->prepare("INSERT INTO kategoria(nazwa, zdjecie) VALUES(?, ?)");
                $query->bind_param('ss', $category_name, $file_name);
                $query->execute();
            }    
            else
                $_SESSION["file_errors"] = $errors;
        }
        $connect->close();
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
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="index.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Potwierdź usunięcie</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Czy <strong>NA PEWNO chcesz USUNĄĆ</strong> kategorię "<span>-</span>" wraz z <span>-</span> produktami wchodzącymi w skład tej kategorii?<br><br><strong>Ta operacja jest nieodwracalna!</strong><br><small>Jeżeli dany produkt należy jeszcze do innej kategorii, nie zostanie usunięty.</small>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="mode" value="delete">
                        <input type="hidden" name="item" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-danger">Usuń</button>               
                    </div>
                </div>  
            </form>
        </div>
    </div>
    <main class="contentContainer">
            <?php
                require "connect.php";
                $connect = new mysqli($host, $db_user, $db_password, $db_name);
                $connect->set_charset('utf8mb4');
                $admin = (isset($_SESSION["logged"]) && $_SESSION["user_data"]["position"] > 1) ? true : false;
                if(!isset($_GET["categoryid"]))
                {
                    echo "<h2>Kategorie produktów</h2>";
                    if($admin)
                    {
                        echo "<label class='toggleSwitch'>";
                        echo "<input type='checkbox' class='toggleEditMode'>";
                        echo "<div class='toggler'></div>";
                        echo "</label>";
                    }
                    echo "<div class='categoryView'>";
                    $query = $connect->prepare('SELECT kategoria_id, nazwa, zdjecie, COUNT(produkt_id) AS ilosc FROM kategoria LEFT JOIN produkt_kategoria USING(kategoria_id) GROUP BY kategoria_id');
                    $query->execute();
                    $result = $query->get_result();
                    if($result->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                            echo "<a href='./category/".$row["kategoria_id"]."/' class='category' id='c".$row["kategoria_id"]."'>";
                            echo "<button type='button' title='Usuń tę kategorię' class='deleteCategory editable d-none' data-toggle='modal' data-target='#staticBackdrop'>&times;</button>";
                            echo "<div class='categoryDetails'>";
                            echo "<div class='categoryImage' style='background-image: url(img/categoryImages/".$row["zdjecie"].");'></div>";
                            echo "<div class='categoryName'>";
                            echo "<h3 tabindex='0'>".$row["nazwa"]."</h3>";
                            echo "<h4>(".$row["ilosc"].")</h4>";
                            echo "</div>";
                            echo "</div>";
                            echo "</a>";
                        }
                    }
                    if($admin)
                    {
                        echo "<a class='editable d-none category'>";
                        echo "<div class='categoryDetails'>";
                        echo "<div id='addNewCategory' title='Dodaj nową kategorię' data-toggle='tooltip'></div>";
                        echo "<form action='index.php' method='POST' enctype='multipart/form-data'>";                       
                        echo "<label title='Wybierz lub przeciągnij plik' data-toggle='tooltip' data-trigger='hover' class='fileDropArea'><i class='bi bi-file-earmark-arrow-up-fill'></i><span id='uploadedFileName'></span><input type='file' name='categoryImage' accept='.png, .jpg, .jpeg, .gif, .bmp, .tiff' required></label>";
                        echo "<div class='categoryName'>";
                        echo "<textarea rows='2' name='categoryName' maxlength='25' placeholder='Nazwa kategorii' required>Nowa kategoria</textarea>";
                        echo "</div>";
                        echo "<input type='hidden' name='mode' value='add'>";
                        echo "<input type='submit' value='Dodaj'>";
                        echo "</form>";
                        echo "</div>";
                        echo "</a>";
                    }
                    echo "</div>";             
                }
                else
                {                   
                    $query3 = $connect->prepare('SELECT nazwa FROM kategoria WHERE kategoria_id= ?');
                    $query3->bind_param('i', $_GET["categoryid"]);
                    $query3->execute();
                    $result3 = $query3->get_result();
                    if($result3->num_rows <= 0)
                        echo "<div class='alert alert-danger information'><strong>Błąd 404: Nieprawidłowy identyfikator kategorii.</strong> <a href='./'>Wróć na stronę główną</a></div>";     
                    else
                    {
                        $query = $connect->prepare('SELECT * FROM produkt INNER JOIN produkt_kategoria USING(produkt_id) WHERE kategoria_id= ?');
                        $query->bind_param('i', $_GET["categoryid"]);
                        $query->execute();
                        $result = $query->get_result();
                        $row3 = $result3->fetch_assoc();
                        echo "<h2>".$row3["nazwa"]." <small>(Wyniki: ".$result->num_rows.")</small></h2>";
                        if($admin)
                            echo "<a href='productform.php?categoryid=".$_GET["categoryid"]."' id='addNewProduct' title='Dodaj nowy produkt do tej kategorii' data-toggle='tooltip' data-trigger='hover'><span class='bi bi-pencil-fill'></span> Dodaj</a>";
                        echo "<script>document.title='".$row3["nazwa"]." - wspaniałe oferty w MatiTechShop';</script>";
                        if($result->num_rows <= 0)
                            echo "<div class='alert alert-danger information'><strong>Nie znaleziono produktów w tej kategorii.</strong> <a href='./'>Wróć na stronę główną</a></div>";
                        else
                        {
                            while($row = $result->fetch_assoc())
                            {
                                echo "<div class='product'>";
                                echo "<div class='productTitle'>";
                                echo "<a href='product/".$row["produkt_id"]."/'>";
                                echo "<h4>".$row["nazwa"]."</h4>";
                                echo "</a>";
                                echo "<span class='productCode' title='Kod produktu' data-toggle='tooltip'>(".str_pad($row["produkt_id"], 6, "0", STR_PAD_LEFT).")</span>";
                                echo "</div>";
                                echo "<div class='productInfo'>";
                                echo "<div class='productReview'>";
                                $queryOpinion = $connect->prepare("SELECT COUNT(produkt_opinia_id) AS liczba, ROUND(AVG(opinia), 1) AS srednia FROM produkt_opinia WHERE produkt_id = ?");
                                $queryOpinion->bind_param('i', $row["produkt_id"]);
                                $queryOpinion->execute();
                                $resultOpinion = $queryOpinion->get_result();
                                $rowOpinion = $resultOpinion->fetch_assoc();
                                $gradientId = 1;
                                if($rowOpinion["liczba"] > 0)
                                {                               
                                    echo "<div title='".$rowOpinion["liczba"]." osób oceniło ten produkt na ".str_replace(".", ",", $rowOpinion["srednia"])."/5' data-toggle='tooltip'>";
                                    for($i = 1; $i <= 5; $i++)
                                    {
                                        $gradient = false;
                                        echo "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512' width='25' height='25'>";
                                        if($i == (floor($rowOpinion["srednia"]) + 1) && !is_int($rowOpinion["srednia"]))
                                        {
                                            $offset = round($rowOpinion["srednia"] - floor($rowOpinion["srednia"]), 1) * 100;
                                            echo "<defs>";
                                            echo "<linearGradient id='gradient".$gradientId."'>";
                                            echo "<stop offset='".$offset."%' stop-color='gold'/>";
                                            echo "<stop offset='".$offset."%' stop-color='grey'/>";
                                            echo "</linearGradient>";
                                            echo "</defs>";
                                            $gradient = true;
                                        }
                                        echo "<path fill='";
                                        if($gradient)
                                            echo "url(#gradient".($gradientId++).")";
                                        elseif($i <= floor($rowOpinion["srednia"]))
                                            echo "gold";
                                        else
                                            echo "grey";
                                        echo "' d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/>";
                                        echo "</svg>";
                                    }
                                    echo "<span class='font-weight-bold ml-2'>".str_replace(".", ",", $rowOpinion["srednia"])."</span>";
                                    echo "<span class='text-primary ml-1'>(".$rowOpinion["liczba"].")</span>";
                                    echo "</div>";                               
                                }
                                $queryCustomers = $connect->prepare("SELECT COUNT(*) AS ilosc, SUM(liczba) AS suma FROM(SELECT uzytkownik_id, SUM(ilosc) AS liczba FROM `produkt_zamowienie` INNER JOIN zamowienie USING(zamowienie_id) WHERE produkt_id = ? GROUP BY uzytkownik_id) AS test");
                                $queryCustomers->bind_param('i', $row["produkt_id"]);
                                $queryCustomers->execute();
                                $resultCustomers = $queryCustomers->get_result();
                                $rowCustomers = $resultCustomers->fetch_assoc();
                                if($rowCustomers["ilosc"] > 0)
                                {
                                    echo "<div class='ml-auto' title='".$rowCustomers["ilosc"]." osób kupiło ".$rowCustomers["suma"]." produktów' data-toggle='tooltip'>";
                                    echo $rowCustomers["ilosc"]." <span class='bi bi-people-fill'></span>";
                                    echo $rowCustomers["suma"]." <span class='bi bi-bag-plus-fill'></span>";
                                    echo "</div>";
                                }                          
                                echo "</div>";
                                echo "<div class='productDetails'>";
                                echo "<div class='productImage'>";
                                $path = './img/productImages/'.$row["produkt_id"].'/';
                                $files = array_diff(scandir($path), array(".", "..", "default"));
                                if(count($files) > 0)
                                {
                                    echo "<button type='button' class='btnBack' title='Wstecz' data-toggle='tooltip'>&lt;</button>";
                                    echo "<button type='button' class='btnForward' title='Dalej' data-toggle='tooltip'>&gt;</button>";
                                }                           
                                if($row["najlepsza_cecha"])
                                    echo "<div class='bestFeature'><span class='bi bi-star-fill'></span>".$row["najlepsza_cecha"]."</div>";
                                echo "<a href='product.php?id=".$row["produkt_id"]."'>";
                                echo "<div>";                                                        
                                echo "<img src='".$path."/"."default/".scandir($path."/"."default/")[2]."' alt='laptop'>";
                                foreach($files as $file)
                                    echo "<img src='".$path.$file."' alt='laptop'>";                                                                                            
                                echo "</div>";
                                echo "</a>";
                                echo "</div>";
                                echo "<div class='productDesc'>";
                                echo "<div class='productProperties' style='overflow-y: auto; height: calc(100% - 60px);'>";
                                $query2 = $connect->prepare('SELECT parametr, wartosc FROM produkt_parametr INNER JOIN parametr USING(parametr_id) WHERE produkt_id= ? LIMIT 5');
                                $query2->bind_param('i', $row["produkt_id"]);
                                $query2->execute();
                                $result2 = $query2->get_result();
                                if($result2->num_rows > 0)
                                {
                                    echo "<ul>";
                                    while($row2 = $result2->fetch_assoc())
                                        echo "<li class='text-uppercase'>".$row2["parametr"].": <span class='font-weight-bold ml-1'>".$row2["wartosc"]."</span></li>";                            
                                    echo "</ul>";
                                }                            
                                echo "</div>";
                                echo "<div class='sellerInfo'>";
                                echo "<div>Sprzedaje</div>";
                                echo "<div>";
                                echo "<a href='#'>@MatiHalek</a>";
                                echo "<span><span class='bi bi-hand-thumbs-up-fill'></span>100%</span>";
                                echo "</div>";
                                echo "</div>";                                                    
                                echo "</div>"; 
                                echo "</div>";                                                          
                                echo "</div>";
                                echo "<div class='priceInfo'>";
                                echo "<div>";
                                $mainPrice = $row["cena"];
                                if($row["promocja"])
                                {
                                    echo "<div class='discount'>".number_format($row["cena"], 2, ",", " ")."</div>";
                                    $mainPrice = $row["promocja"];
                                } 
                                echo "<div style='text-align: center; font-weight: bold; padding-bottom: 1rem;'>";
                                echo "<div style='font-size: 3rem; display:inline-block; line-height: 1;'>".number_format(floor($mainPrice), 0, '.', ' ')."</div>";
                                echo "<div style='display: inline-block; vertical-align: top;'>";
                                echo "<div style='font-size: 1.5rem; line-height: 1.4;'>".explode(".", $mainPrice)[1]."</div>";
                                echo "<div style='font-size: 0.9rem; line-height: 1;'>zł</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div style='text-align: center;'>";
                                $amount = $row["ilosc"];
                                if($amount > 0)
                                    echo "<button type='button' class='addToCart' id='btnAdd".$row["produkt_id"]."'><span class='bi bi-basket-fill'></span>Dodaj do koszyka</button>";                      
                                echo "</div>";
                                echo "<div style='text-align: center;'>";
                                if($amount > 0)
                                    echo "Dostępność: ";
                                if($amount <= 0)
                                    echo "<span style='color: gray; font-weight: bold;'>Produkt niedostępny</span>";
                                elseif($amount < 10)
                                    echo "<span style='color: orangered; font-weight: bold;'>Mała ilość</span>";
                                elseif($amount < 100)
                                    echo "<span style='color: #DAA520; font-weight: bold;'>Średnia ilość</span>";
                                else 
                                    echo "<span style='color: green; font-weight: bold;'>Duża ilość</span>";
                                echo "</div>";
                                echo "</div>";                                        
                                echo "</div>";        
                                echo "</div>";
                            } 
                        }                       
                    }                                                     
                }
                $result->free_result();
                $connect->close();
            ?>         
    </main>
    <?php
        include "footer.php";
    ?>
    <script src="./js/slider.js"></script>
    <script>
        [].forEach.call(document.querySelectorAll(".addToCart"), function(el){
            el.addEventListener("click", function(){
                cart.addProduct(el.id.replace("btnAdd", ""), 1);
                showAlert("Pomyślnie dodano produkt do koszyka.", false, 2000);
            }, false);
        });
    </script>
    <?php
        if($admin)
            echo "<script src='./js/editMode.js'></script>";
    ?>
    <script>
        $('[data-toggle="tooltip"]').tooltip();
    </script>
    <?php
        if(isset($_SESSION["file_errors"]))
        {
            $message = "Wystąpiły błędy podczas dodawania kategorii: <ul>";
            foreach($_SESSION["file_errors"] as $error)
                $message.="<li style=\"font-weight: normal;\">$error</li>";
            $message.="</ul>";
            echo "<script>";
            echo "showAlert('$message', true, 7000);";
            echo "</script>";
            unset($_SESSION["file_errors"]);
        }
    ?>
</body>
</html>