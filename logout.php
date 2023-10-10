<?php
    error_reporting(0);
    session_start();
    session_unset();
    if(isset($_SERVER['HTTP_REFERER']))
        header("Location: ".$_SERVER['HTTP_REFERER']);  
    else
        header("Location: ./");
?>