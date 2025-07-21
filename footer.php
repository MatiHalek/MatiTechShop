<footer>
    <?php
        error_reporting(0);
        $version = array(
            "major" => 1,
            "minor" => 1,
            "patch" => 0,
            "otherInfo" => "",
            "releaseDate" => new DateTime("2025-07-10")
        );
        echo "<div id='footerInfo'><div>&copy; 2022 - ".$version["releaseDate"]->format("Y")." MH Corporation. Wszelkie prawa zastrzeżone.</div></div>";
        echo "<div id='versionInfo'><a href='https://github.com/MatiHalek/MatiTechShop/releases/tag/v{$version['major']}.{$version['minor']}.{$version['patch']}' target='_blank' title='GitHub' data-toggle='tooltip' class='bi bi-github' aria-label='GitHub'></a><span title='Data wydania: ".$version["releaseDate"]->format("d.m.Y")."' data-toggle='tooltip'>v{$version['major']}.{$version['minor']}.{$version['patch']}".(empty($version["otherInfo"]) ? "" : " ".$version["otherInfo"])."</span></div>";
    ?>      
</footer>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>