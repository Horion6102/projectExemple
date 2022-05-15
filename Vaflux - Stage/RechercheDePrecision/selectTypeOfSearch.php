<?php
include_once("../structure/header/header.html");
include_once("selectTypeOfSearch.html");

if(isset($_POST["submit"]) || !empty($_POST["type"])) {
    $type = $_POST["type"];
    if($type == "unique") {
        header("Location : unique.php");
    } elseif ($type == "departement") {
        header("Location : departement.php");
    } elseif ($type == "service") {
        header("Location : service.php");
    }
}

include_once("../structure/footer/footer.html");

// ===== ===== ===== //

function getCategorie() {
    $con = pg_connect("XXX") or die("Connexion impossible à la base de données");
    $select = 'SELECT * from "categories"';
    $result = pg_query($con,$select);
    $row = pg_fetch_row($result);
    foreach($row as $value) {
        echo "<label for=''>".$value."</label><input type='checkbox' name='' id=''>";
    }
}
?>