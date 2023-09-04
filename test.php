<?php
    error_reporting(0);
    require "functions.php";
    $ajax_property = "Validate".ucfirst($_POST["property"]);
    $ajax_value = $_POST["q"];
    if(isset($_POST["tmp"]))
        $ajax_success = call_user_func($ajax_property, $ajax_value, $_POST["tmp"]);
    else
        $ajax_success = call_user_func($ajax_property, $ajax_value);
    if(!$ajax_success["passed"])
        echo "<div class='invalid-tooltip'>".$ajax_success["note"]."</div>";
    else
        echo "<div class='valid-tooltip'></div>";
?>