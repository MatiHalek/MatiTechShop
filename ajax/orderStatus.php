<?php
    error_reporting(0);
    require "../connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    $orderId = substr($_POST["orderId"], 12);
    $selectedOption = $_POST["selectedOption"];
    $query = $connect->prepare("UPDATE zamowienie SET status_id = ? WHERE zamowienie_id = ?");
    $query->bind_param('ii', $selectedOption, $orderId);
    $query->execute();
    echo "success";
?>