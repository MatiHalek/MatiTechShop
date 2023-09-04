<?php
    error_reporting(0);
    session_start();
    if(!isset($_SESSION["logged"]) || $_SESSION["user_data"]["position"] <= 1)
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
    <title>Zarządzanie zamówieniami | MatiTechShop</title>
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
        <h2>Zarządzanie zamówieniami</h2> 
        <div id="orders">
            <div>
                <table id="orderTable">
                    <tr>
                        <?php
                            $sort = "zamowienie_id";
                            $tableHeaders = array("#", "Użytkownik", "Produkty", "Dostawa", "Płatność", "Data zamówienia", "Status");
                            $sortOptions = array(null, "user", null, "delivery", "payment", "order_date", "status");
                            $sortColumn = array(null, "nazwisko", null, "dostawa_id", "platnosc_id", "data_zamowienia", "status_id");
                            for($i = 0; $i < count($tableHeaders); $i++)
                            {
                                echo "<th>";
                                if($i == 0)
                                {
                                    echo "<a href='ordermanage' title='Sortuj według tej kolumny' data-toggle='tooltip'>".$tableHeaders[$i]."</a></th>";
                                    continue;
                                }
                                if($sortOptions[$i])
                                {
                                    echo "<a href='ordermanage.php?sort=".$sortOptions[$i]."' data-toggle='tooltip'";
                                    if(isset($_GET["sort"]) && $_GET["sort"] == $sortOptions[$i])
                                    {
                                        echo " title='Posortowano według tej kolumny'><span class='bi bi-funnel-fill'></span>";   
                                        $sort = $sortColumn[$i];
                                    } 
                                    else
                                        echo " title='Sortuj według tej kolumny'>";                                                                      
                                    echo $tableHeaders[$i]."</a>";
                                }
                                else
                                    echo $tableHeaders[$i];                               
                                echo "</th>";
                            }                                                   
                        ?>
                    </tr>
                    <?php
                        require "connect.php";
                        $connect = new mysqli($host, $db_user, $db_password, $db_name);
                        $connect->set_charset('utf8mb4');
                        $status = array();
                        $query = $connect->prepare("SELECT * FROM status");
                        $query->execute();
                        $result = $query->get_result();
                        while($row = $result->fetch_assoc())
                            $status[$row["status_id"]] = $row["status"];
                        $query = $connect->prepare("SELECT * FROM zamowienie INNER JOIN uzytkownik USING(uzytkownik_id) INNER JOIN dostawa USING(dostawa_id) INNER JOIN platnosc USING(platnosc_id) ORDER BY $sort");
                        $query->execute();
                        $result = $query->get_result();
                        while($row = $result->fetch_assoc())
                        {
                            $query2 = $connect->prepare("SELECT * FROM produkt_zamowienie INNER JOIN produkt USING(produkt_id) WHERE zamowienie_id = ?");
                            $query2->bind_param('i', $row["zamowienie_id"]);
                            $query2->execute();
                            $result2 = $query2->get_result();
                            $rowspan = $result2->num_rows;
                            $products = array();
                            echo "<tbody><tr>";
                            echo "<td rowspan='$rowspan' class='idColumn'>".$row["zamowienie_id"]."</td>";
                            echo "<td rowspan='$rowspan'><span style='";
                            if($row["nazwa_uzytkownika"])
                                echo "color: blue;'><a href='#'>@".$row["nazwa_uzytkownika"]."</a>";
                            else
                                echo "color: red;'>Użytkownik niezarejestrowany";
                            echo "</span><br>";
                            echo "<span style='font-weight: bold;'>".$row["imie"]." ".$row["nazwisko"]."</span><br>";
                            echo "ul. ".$row["ulica"]." ".$row["nr_domu"]."<br>".$row["kod_pocztowy"]." ".$row["miasto"]."<br>";
                            echo $row["email"]."<br>tel. ".$row["numer_telefonu"];
                            echo "</td>";
                            while($row2 = $result2->fetch_assoc())
                                array_push($products,  "<td><a href='product/".$row2["produkt_id"]."/' target='_blank'>".((mb_strlen($row2["nazwa"]) > 33)?(substr($row2["nazwa"], 0, 30)."..."):($row2["nazwa"]))."</a><br>".$row2["ilosc"]." x ".number_format($row2["cena"], 2, ",", " ")." zł</td>");
                            echo $products[0];
                            echo "<td rowspan='$rowspan'>".$row["nazwa_dostawy"]."</td>";
                            echo "<td rowspan='$rowspan'>".$row["nazwa_platnosci"]."</td>";
                            echo "<td rowspan='$rowspan'>".$row["data_zamowienia"]."</td>";
                            echo "<td rowspan='$rowspan'><select id='selectStatus".$row["zamowienie_id"]."'>";
                            foreach($status as $key => $value)
                            {
                                echo "<option value='".$key."'";
                                if($key == $row["status_id"])
                                    echo " selected";
                                echo ">".$value."</option>";
                            }
                            echo "</select></td>";
                            echo "</tr>";
                            if(count($products) > 1)
                            {
                                for($i = 1; $i < count($products); $i++)
                                    echo "<tr class='subRows'>".$products[$i]."</tr>";
                            }
                            echo "</tbody>";
                        }
                        $result->free_result();
                        $query->execute();
                        $connect->close();
                    ?>
                </table>
            </div>
        </div>      
    </main>
    <?php
        include "footer.php";
    ?>
    <script>
        [].forEach.call(document.querySelectorAll("select"), function(el){
            el.addEventListener("change", function(){
                $.ajax({
                    method: "POST",
                    url: "./ajax/orderStatus.php",
                    data: {"orderId" : el.id, "selectedOption" : el.value},
                    success: function(result){
                        if(result == "success")
                            showAlert("Pomyślnie zmieniono status zamówienia.", false, 2000);
                        else
                            showAlert("Proces zmiany statusu zamówienia nie powiódł się. Spróbuj ponownie.", true, 2000);
                    }
                });
            }, false);
        });
        $("[data-toggle='tooltip']").tooltip();
    </script>
</body>
</html>