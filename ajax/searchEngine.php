<?php
    error_reporting(0);
    require "../connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    $search = $_POST["search"];
    $query = $connect->prepare("SELECT kategoria_id, nazwa, COUNT(produkt_id) AS products FROM kategoria LEFT JOIN produkt_kategoria USING(kategoria_id) GROUP BY kategoria_id ORDER BY products DESC");
    $query->execute();
    $result = $query->get_result();
    $htmlText = "";
    if($result->num_rows > 0)
    {        
        while($row = $result->fetch_assoc())
        {
            $query2 = $connect->prepare("(SELECT * FROM produkt_kategoria INNER JOIN produkt USING(produkt_id) WHERE kategoria_id = ? AND POSITION(TRIM(?) IN nazwa) > 0 AND ilosc > 0) UNION (SELECT * FROM produkt_kategoria INNER JOIN produkt USING(produkt_id) WHERE kategoria_id = ? AND POSITION(TRIM(?) IN nazwa) > 0 AND ilosc = 0) ORDER BY produkt_id DESC LIMIT 4");
            $query2->bind_param('isis', $row["kategoria_id"], $search, $row["kategoria_id"], $search);
            $query2->execute();
            $result2 = $query2->get_result();
            if($result2->num_rows > 0)
            {
                $htmlText .= "<a href='category/".$row["kategoria_id"]."' class='categoryResults' tabindex='-1'>Kategoria: ".$row["nazwa"]."</a>";
                while($row2 = $result2->fetch_assoc())
                    $htmlText .= "<a href='product/".$row2["produkt_id"]."' class='productResults' tabindex='-1'><div class='searchImage'><div style='background-image: url(img/productImages/".$row2["produkt_id"]."/default/".scandir("../img/productImages/".$row2["produkt_id"]."/default")[2].");'></div></div><div class='searchTitle' title='".$row2["nazwa"]."'>".$row2["nazwa"]."</div><div class='searchPrice'>".(($row2["promocja"])?("<s>".number_format($row2["cena"], 2, ",", " ")."</s>".number_format($row2["promocja"], 2, ",", " ")):(number_format($row2["cena"], 2, ",", " ")))." zł</div></a>";
                $result2->free_result();
            }          
            else if(mb_strpos(mb_strtolower($row["nazwa"]), mb_strtolower($search)) > -1)
                $htmlText .= "<a href='category/".$row["kategoria_id"]."' class='categoryResults'>Kategoria: ".$row["nazwa"]."</a>";
        }    
        $result->free_result();  
    }
    $connect->close();
    if(empty($htmlText))
        $htmlText = "<div><i>Nie znaleziono wyników.</i></div>";
    echo $htmlText;  
?>