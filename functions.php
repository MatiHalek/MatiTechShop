<?php
    error_reporting(0);
    function validateLogin($item)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(empty($item))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę podać login."];
        if(!preg_match("/^[0-9A-Za-z]{1}[0-9A-Za-z_]{1,13}[0-9A-Za-z]{1}$/", $item))
        {
            if(strlen($item) < 3 || strlen($item) > 15)
                return ["parameter" => $function, "passed" => false, "note" => "Login musi zawierać od 3 do 15 znaków."];
            if(str_ends_with($item, "_") || str_starts_with($item, "_"))
                return ["parameter" => $function, "passed" => false, "note" => "Znak _ nie może znajdować się na początku lub końcu loginu."];            
            return ["parameter" => $function, "passed" => false, "note" => "Login może zawierać tylko wielkie i małe litery, cyfry i znak _."];
        }
        if(substr_count($item, "_") > 1)       
            return ["parameter" => $function, "passed" => false, "note" => "Login może zawierać maksymalnie jeden znak _."];        
        require "connect.php";
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        $connect->set_charset("utf8mb4");
        $query = $connect->prepare("SELECT * FROM uzytkownik WHERE nazwa_uzytkownika = ?");
        $query->bind_param('s', $item);
        $query->execute();
        $result = $query->get_result();
        $howManyRows = $result->num_rows;
        $connect->close();
        if($howManyRows > 0)
            return ["parameter" => $function, "passed" => false, "note" => "Taki login już istnieje."];
        return ["parameter" => $function, "passed" => true, "note" => null];          
    }
    function validatePassword($item)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(empty($item))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę wprowadzić hasło."];
        if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()])[a-zA-Z\d!@#$%^&*()]{8,255}$/", $item))
            return ["parameter" => $function, "passed" => false, "note" => "Hasło musi zawierać minimum 8 znaków (w tym cyfry, małe i duże litery oraz znaki specjalne)."];
        return ["parameter" => $function, "passed" => true, "note" => null]; 
    }
    function validatePassword2($item, $check)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(empty($item))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę wprowadzić hasło."];
        if($item != $check)
            return ["parameter" => $function, "passed" => false, "note" => "Wprowadzone hasła nie są identyczne."];
        return ["parameter" => $function, "passed" => true, "note" => null]; 
    }
    function validateEmail($item)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(empty($item))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę podać adres e-mail."];
        $item2 = filter_var($item, FILTER_SANITIZE_EMAIL);
        if(filter_var($item2, FILTER_VALIDATE_EMAIL) == false || $item != $item2)
            return ["parameter" => $function, "passed" => false, "note" => "Ten adres e-mail jest nieprawidłowy."];
        require "connect.php";
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        $connect->set_charset("utf8mb4");
        $query = $connect->prepare("SELECT uzytkownik_id FROM uzytkownik WHERE email = ?");
        $query->bind_param('s', $item);
        $query->execute();
        $result = $query->get_result();
        $howManyRows = $result->num_rows;
        $connect->close();
        if($howManyRows > 0)
            return ["parameter" => $function, "passed" => false, "note" => "Taki email jest już przypisany do innego konta."];
        return ["parameter" => $function, "passed" => true, "note" => null];          
    }
    function validateDate($item)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(empty($item))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę podać poprawną datę."];
        $date_arr = explode("-", $item);
        if(count($date_arr) != 3 || !checkdate((int)$date_arr[1], (int)$date_arr[2], (int)$date_arr[0]))
            return ["parameter" => $function, "passed" => false, "note" => "Proszę podać poprawną datę (format RRRR-MM-DD)."];
        if(mktime(0, 0, 0, $date_arr[1], $date_arr[2], $date_arr[0]) > strtotime("- 13 years"))
            return ["parameter" => $function, "passed" => false, "note" => "Aby utworzyć konto musisz mieć ukończone 13 lat."];
        return ["parameter" => $function, "passed" => true, "note" => null];
    }
    function validateRegulations($item)
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        if(!filter_var($item, FILTER_VALIDATE_BOOLEAN))
            return ["parameter" => $function, "passed" => false, "note" => "Aby kontynuować, musisz zaakceptować regulamin."];
        return ["parameter" => $function, "passed" => true, "note" => null];
    }
    function validateCaptcha()
    {
        $function = substr(strtolower(__FUNCTION__), 8);
        $secret = "6LdsoMAiAAAAAFPiefBMoxRUiy1TJlpuTXp0ZkBz";
        $check = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$_POST["g-recaptcha-response"]);
        $answer = json_decode($check);
        if(!($answer->success))
            return ["parameter" => $function, "passed" => false, "note" => "Zweryfikuj, że jesteś człowiekiem."];
        return ["parameter" => $function, "passed" => true, "note" => null];
    }
?>