<?php
    error_reporting(0);
    $name =  htmlspecialchars($_POST["value"].mb_substr(0, 25));
    $id = $_POST["id"];
    require "connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    $query = $connect->prepare("UPDATE kategoria SET nazwa= ? WHERE kategoria_id = ?");
    $query->bind_param('si', $name, $id);
    $query->execute();
    $query = $connect->prepare("SELECT nazwa FROM kategoria WHERE kategoria_id = ?");
    $query->bind_param('i', $id);
    $query->execute();
    echo $query->get_result()->fetch_assoc()["nazwa"];
    $connect->close();
?>