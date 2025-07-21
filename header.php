<?php
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $unsupported = false;
    if (preg_match('/MSIE (\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 9) $unsupported = true;
    } elseif (preg_match('/Trident\/.*rv:(\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 9) $unsupported = true;
    } elseif (preg_match('/Chrome\/(\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 49) $unsupported = true;
    } elseif (preg_match('/Firefox\/(\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 52) $unsupported = true;
    } elseif (preg_match('/Version\/(\d+).*Safari/i', $ua, $m)) {
        if ((int)$m[1] < 9) $unsupported = true;
    } elseif (preg_match('/OPR\/(\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 36) $unsupported = true;
    } elseif (preg_match('/Opera\/(\d+)/i', $ua, $m)) {
        if ((int)$m[1] < 36) $unsupported = true;
    }
?>
<script>
    var unsupportedBrowser = <?= $unsupported ? 'true' : 'false' ?>;
    if (unsupportedBrowser)
        window.location.href = "/MatiTechShop/unsupported-browser.php";
</script>
<header>
    <nav>
        <a href="./" id="logo" title="MatiTechShop - technologia dla każdego!" data-toggle="tooltip" data-html="true">
            <img src="/MatiTechShop/img/logo_full.png" alt="Logo sklepu MatiTechShop" height="70" width="130">
            <img src="/MatiTechShop/img/logo_small.png" alt="Logo sklepu MatiTechShop" height="70" width="70">
        </a>       
        <form action="./" method="GET">
           <label>
                <span class="bi bi-search"></span>
                <?php
                    echo "<input type='search' name='search' placeholder='Szukaj...' maxlength='200' id='inputSearch' title='Wyszukaj w sklepie' data-toggle='tooltip' data-trigger='hover'";
                    if(isset($_GET["search"]))
                        echo " value='".htmlspecialchars($_GET["search"])."'";
                    echo ">";
                ?> 
                <div id="searchResults"></div>
            </label> 
        </form>       
        <div style="display: flex;">
        <a href="cart" id="goToCart" title="Koszyk" data-toggle="tooltip" aria-label="Koszyk"><span class='bi bi-basket-fill'></span>
            <div id="cartItemCounter">-</div>
        </a>                                    
    <?php
        error_reporting(0);
        if(session_status() === PHP_SESSION_NONE)
            session_start();                 
        if(isset($_SESSION["logged"]) && $_SESSION["logged"])
        {
            echo "<div class='btn-group'><button type='button' class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='logInButton' aria-label='Konto'><span class='bi bi-person-circle'></span><span>";
            $userInfo = $_SESSION["username"];
            if((int)($_SESSION["user_data"]["position"]) == 2)
                $userInfo .= "&nbsp;<img src='img/verified.png' alt='zweryfikowane' width='18' height='18' data-toggle='tooltip' data-placement='bottom' data-html='true' title='<b><img src=\"img/verified.png\" alt=\"zweryfikowane\" width=\"20\" height=\"20\"> OFICJALNE</b><small>To konto ma status zweryfikowanego, ponieważ należy do moderatora tego serwisu.</small>' id='verified'>";
            if((int)($_SESSION["user_data"]["position"]) == 3)
                $userInfo .= "&nbsp;<img src='img/verifiedOwner.png' alt='zweryfikowane' width='18' height='18' data-toggle='tooltip' data-placement='bottom' data-html='true' title='<b><img src=\"img/verifiedOwner.png\" alt=\"zweryfikowane\" width=\"20\" height=\"20\"> OFICJALNE</b><small><br>To konto ma status zweryfikowanego, ponieważ należy do właściciela tego serwisu.</small>' id='verified'>";
            echo "$userInfo</span></button><div class='dropdown-menu dropdown-menu-right'>";
            echo "<div class='dropdown-header'>$userInfo</div><div class='dropdown-divider'></div>";
            if((int)($_SESSION["user_data"]["position"]) > 1)
                echo "<a class='dropdown-item' href='ordermanage'><span class='bi bi-kanban-fill'></span> Zarządzaj zamówieniami</a><div class='dropdown-divider'></div>";
            echo "<a class='dropdown-item text-danger' href='logout'><span class='bi bi-door-closed-fill'></span> Wyloguj się</a></div></div>";
        }
        else
        {
            echo "<a href='registration' id='registerButton'><span class='bi bi-person-fill-add' aria-label='Rejestracja' role='img'></span> <span>Zarejestruj się</span></a>
            <a href='login' id='logInButton'><span class='bi bi-person-circle' aria-label='Logowanie' role='img'></span> <span>Zaloguj się</span></a>";
        }
    ?>   
        </div>
    </nav>           
</header>
<script>
    function search(word)
    {
        if(word.replace(/^\s+|\s+$/g, '').length >= 3)
        {
            var ajax = new XMLHttpRequest();
            var url = "./ajax/searchEngine.php";
            var params = 'search=' + word;
            ajax.open("POST", url, true);
            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ajax.onreadystatechange = function() {
                if(ajax.readyState == 4 && ajax.status == 200)
                {
                    document.querySelector("#searchResults").style.display = "block";
                    document.querySelector("#searchResults").innerHTML = this.responseText;
                }                    
            };
            ajax.send(params);
        } 
    }      
    document.querySelector("#inputSearch").addEventListener("keyup", function(){
        search(this.value);
    }, false);
    document.querySelector("#inputSearch").addEventListener("focus", function(){
        search(this.value);
    }, false);
    document.querySelector("#inputSearch").addEventListener("blur", function(){
        document.querySelector("#searchResults").style.display = "none";
    }, false);
    document.querySelector("header nav > form").addEventListener("submit", function(e){
        if(/^\s*$/.test(document.querySelector("#inputSearch").value))
            e.preventDefault();
    }, false);
    cart.countProducts();
    if (document.createElement("input").placeholder == undefined)
    {
        document.querySelector("#inputSearch").addEventListener("focus", function() {
            if(/^\s*$/.test(this.value) || this.value == "Szukaj...")
            {
                this.value = "";
                this.className = this.className.replace(/\bplaceholder\b/g, '').trim();
            }
        }, false);
        document.querySelector("#inputSearch").addEventListener("blur", function() {
            if(/^\s*$/.test(this.value))
            {
                this.value = "Szukaj...";
                this.className = "placeholder";
            }            
        }, false);
        if(/^\s*$/.test(document.querySelector("#inputSearch").value))
        {
            document.querySelector("#inputSearch").value = "Szukaj...";
            document.querySelector("#inputSearch").className = "placeholder";
        }
    }
</script>