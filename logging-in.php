<?php
    error_reporting(0);
    session_start();
    if(!isset($_POST["username"]) || !isset($_POST["password"]))
    {
        header("Location: login.php");
        exit();
    }
    require "connect.php";
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    $connect->set_charset('utf8mb4');
    if($connect->connect_errno != 0)
        echo "Error: ".$polaczenie->connect_errno;
    else
    {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $query = $connect->prepare('SELECT * FROM uzytkownik WHERE nazwa_uzytkownika= ? AND czy_zalogowany = 1');
        $query->bind_param('s', $username);
        $query->execute();
        if($result=$query->get_result())
        {
            if($result->num_rows > 0)
            {
                $row=$result->fetch_assoc();
                if(password_verify($password, $row["haslo"]))
                {
                    $_SESSION["logged"] = true;
                    $_SESSION["user_id"] = $row["uzytkownik_id"];
                    $_SESSION["username"] = $row["nazwa_uzytkownika"];
                    $_SESSION["position"] = $row["stanowisko_id"];
                    $_SESSION["user_data"] = array(
                        "user_id" => $row["uzytkownik_id"],
                        "username" => $row["nazwa_uzytkownika"],
                        "email" => $row["email"],
                        "birth_date" => $row["data_urodzenia"],
                        "phone_number" => $row["numer_telefonu"],
                        "first_name" => $row["imie"],
                        "surname" => $row["nazwisko"],
                        "postcode" => $row["kod_pocztowy"],
                        "city" => $row["miasto"],
                        "street" => $row["ulica"],
                        "house_number" => $row["nr_domu"],
                        "position" => $row["stanowisko_id"]
                    );
                    unset($_SESSION["error"]);
                    $result->free_result();
                    header("Location: index.php");
                    exit();
                }
                else
                {
                    $_SESSION["error"] = "<div class='alert alert-danger'><strong>Nieprawidłowa nazwa użytkownika lub hasło.</strong></div>";
                    header("Location: login.php");
                    exit();
                }             
            }
            else
            {
                $_SESSION["error"] = "<div class='alert alert-danger'><strong>Nieprawidłowa nazwa użytkownika lub hasło.</strong></div>";
                header("Location: login.php");
                exit();
            }
        }       
        $connect->close();
    }   
?>