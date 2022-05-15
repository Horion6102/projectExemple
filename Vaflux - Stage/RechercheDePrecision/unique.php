<?php
include_once("../structure/header/header.html");
include_once("unique.html");

if(isset($_POST["submit"]) || !empty($_POST["num"])) {
    include_once("choixAleas.php");
}

include_once("../structure/footer/footer.html");
?>