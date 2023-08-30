<?php
    error_reporting(0);
    require "../connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    $id = $_POST["id"];
    $query = $connect->prepare("SELECT * FROM produkt WHERE produkt_id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    $result = $query->get_result();
    $tmp = array($id);
    if($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        if($row["ilosc"] > 0)
        {
            $price = $row["cena"];
            if($row["promocja"])
                $price = $row["promocja"];
            $quantity = 0;
            if(is_numeric($_POST["count"]))
            {
                if($_POST["count"] > $row["ilosc"])
                    $quantity = $row["ilosc"];
                elseif($_POST["count"] < 1)
                    $quantity = 1;
                else
                    $quantity = floor($_POST["count"]);               
            }
            else
                $quantity = 1;
            $filename = scandir("../img/productImages/$id/default")[2];
            $tmp = array($row["produkt_id"], $row["nazwa"], $price, $quantity, $price * $quantity, $row["ilosc"], $filename);
        }      
    }   
    $connect->close();
    echo json_encode($tmp);
    
?>