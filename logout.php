<?php
    error_reporting(0);
    session_start();
    session_unset();
    header("Location: index.php");
?>