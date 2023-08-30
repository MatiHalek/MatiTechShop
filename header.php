<header>
    <nav>
        <a href="index.php" id="logo" title="MatiTechShop - wspaniałe oferty!" data-original-title="<b>MatiTechShop</b> - wspaniałe oferty!" data-toggle="tooltip" data-html="true">
            <img src="/sklep/img/logo_full.png" alt="Logo sklepu MatiTechShop" height="70" width="130">
            <img src="/sklep/img/logo_small.png" alt="Logo sklepu MatiTechShop" height="70" width="70">
        </a>       
        <label>
            <span class="bi bi-search"></span>
            <input type="search" placeholder="Szukaj..." maxlength="200" id="inputSearch" title="Wyszukaj w sklepie" data-toggle="tooltip" data-trigger="hover"> 
            <div id="searchResults"></div>
        </label>
        <a href="cart" id="goToCart" title="Zobacz koszyk" data-toggle="tooltip"><span class='bi bi-basket-fill'></span>
            <div id="cartItemCounter">-</div>
        </a>                                    
    <?php
        error_reporting(0);
        if(session_status() === PHP_SESSION_NONE)
            session_start();                 
        if(isset($_SESSION["logged"]) && $_SESSION["logged"] == true)
        {
            echo "<div class='btn-group'><button type='button' class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' id='logInButton'><span class='bi bi-person-circle'></span><span>";
            echo $_SESSION["username"];
            if((int)($_SESSION["user_data"]["position"]) == 2)
                echo "&nbsp;<img src='img/verified.png' alt='zweryfikowane' width='18' height='18' data-toggle='tooltip' data-placement='bottom' data-html='true' title='<img src=\"img/verified.png\" alt=\"zweryfikowane\" width=\"50\" height=\"50\"><br><h5><b>Zweryfikowano</b></h5>To konto ma status zweryfikowanego, ponieważ należy do moderatora tego serwisu.' id='verified'>";
            if((int)($_SESSION["user_data"]["position"]) == 3)
                echo "&nbsp;<img src='img/verifiedOwner.png' alt='zweryfikowane' width='18' height='18' data-toggle='tooltip' data-placement='bottom' data-html='true' title='<img src=\"img/verifiedOwner.png\" alt=\"zweryfikowane\" width=\"50\" height=\"50\"><br><h5><b>Zweryfikowano</b></h5>To konto ma status zweryfikowanego, ponieważ należy do właściciela tego serwisu.' id='verified'>";
            echo "</span></button><div class='dropdown-menu dropdown-menu-right'>";
            if((int)($_SESSION["user_data"]["position"]) > 1)
                echo "<a class='dropdown-item' href='ordermanage'>Zarządzaj zamówieniami</a><div class='dropdown-divider'></div>";
            echo "<a class='dropdown-item' href='logout'>Wyloguj się</a></div></div>";
        }
        else
        {
            echo "<a href='registration' id='registerButton'><span class='bi bi-person-fill-add'></span> <span>Zarejestruj się</span></a>
            <a href='login' id='logInButton'><span class='bi bi-person-circle'></span> <span>Zaloguj się</span></a>";
        }
    ?>   
    </nav>           
</header>
<script>
    function search(word)
    {
        if(word.length >= 3)
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
    cart.countProducts();
</script>