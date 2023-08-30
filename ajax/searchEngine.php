<?php
    error_reporting(0);
    require "../connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    $search = $_POST["search"];
    $query = $connect->prepare("SELECT * FROM kategoria");
    $query->execute();
    $result = $query->get_result();
    $htmlText = "";
    if($result->num_rows > 0)
    {        
        while($row = $result->fetch_assoc())
        {
            $query2 = $connect->prepare("SELECT * FROM produkt_kategoria INNER JOIN produkt USING(produkt_id) WHERE kategoria_id = ? AND POSITION(? IN nazwa) > 0 LIMIT 4");
            $query2->bind_param('is', $row["kategoria_id"], $search);
            $query2->execute();
            $result2 = $query2->get_result();
            if($result2->num_rows > 0)
            {
                $htmlText .= "<a href='category/".$row["kategoria_id"]."/' class='categoryResults'>".$row["nazwa"]."</a>";
                while($row2 = $result2->fetch_assoc())
                    $htmlText .= "<a href='product/".$row2["produkt_id"]."/' class='productResults'><span class='searchTitle'>".$row2["nazwa"]."</span><span class='searchPrice'>".(($row2["promocja"])?(number_format($row2["promocja"], 2, ",", " ")):(number_format($row2["cena"], 2, ",", " ")))." zł</span></a>";
                $result2->free_result();
            }          
            else if(str_contains(mb_strtolower($row["nazwa"]), mb_strtolower($search)))
                $htmlText .= "<a href='index.php?categoryid=".$row["kategoria_id"]."' class='categoryResults'>".$row["nazwa"]."</a>";
        }    
        $result->free_result();  
    }
    $connect->close();
    if(empty($htmlText))
        $htmlText = "<div><i>Nie znaleziono wyników.</i></div>";
    echo $htmlText;  
?>