<?php 

$err = "";
$icpe = "";

$con = pg_connect("XXX")
or die("Connexion impossible à la base de données");

if(isset($_POST["submit"])) {
    if($_POST["type"] == "unique") {
        if(!empty($_POST["icpe"])) {
            if(strlen($_POST["icpe"]) == 10) {
                $icpe = $_POST["icpe"];
                $selectVerifAIOT = 'SELECT count(*) from "etablissement" where codes3ic = \''.$icpe.'\';';
                $resVerifAIOT = pg_query($con,$selectVerifAIOT);
                $res = pg_fetch_row($resVerifAIOT);
                if($res[0] == 1) {
                    $icpe = $_POST["icpe"];
                    $type = $_POST["type"];
                    include_once("../structure/header/header.html");
                    include_once("choixAleas.html");
                    include_once("../structure/footer/footer.html");    
                } else {
                    $err = "Cet ICPE n'existe pas !";
                    include_once("unique.php");
                }
            } else {
                $err = "Mauvais format | Exemple : XXXX.XXXXX (Code S3IC)";
                include_once("unique.php");
            }
        } else {
            $err = "Merci de remplir le numero d'ICPE !";
            include_once("unique.php");
        }
    }

    // ===== //

    if($_POST["type"] == "departement") {
        if(!empty($_POST["departement"])) {
            $icpe = $_POST["departement"][0];
            $type = $_POST["type"];
            include_once("../structure/header/header.html");
            include_once("choixAleas.html");
            include_once("../structure/footer/footer.html");
        } else {
            $err = "Merci de selectionner un département !";
            include_once("departement.php");
        }
    }

    // ===== //

    if($_POST["type"] == "service") {
        if(isset($_POST["dreal"])) {
            $icpe = $_POST["dreal"][0];
            $type = $_POST["type"];
            include_once("../structure/header/header.html");
            include_once("choixAleas.html");
            include_once("../structure/footer/footer.html");
        } else {
            $err = "Merci de selectionner un service !";
            include_once("service.php");
        }
    }
}
?>