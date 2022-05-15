<?php

// ===== ALGORITHME ===== //

$error = "";

if (isset($_POST["submit"]) || !empty($_POST["zip_file"])) {

    $con = pg_connect("XXX") or die("Connexion impossible à la base de données");

    $file = $_FILES["zip_file"];

    $filename = $_FILES["zip_file"]["name"];
    $fileType = $_FILES["zip_file"]["type"];
    $fileTmpName = $_FILES["zip_file"]["tmp_name"];
    $fileErr = $_FILES["zip_file"]["error"];
    $fileSize = $_FILES["zip_file"]["size"];

    $fileExt = explode('.', $filename);
    $fileCurrentExt = strtolower(end($fileExt));

    $allowed = array('sql');

    if (in_array($fileCurrentExt, $allowed)) {
        if($fileErr == 0) {
            if($fileSize < 5000000000) {
                $date = getdate();
                $fileNewName = $date["year"]."-".$date["mon"]."-".$date["mday"]."_".$filename;
                $fileDestination = "uploads/".$fileNewName;
                $fileNewNameWithOutExt = explode(".",$fileNewName);
                if(move_uploaded_file($fileTmpName, $fileDestination)) {
                    sleep(15);
                    if($res = pg_query($con,file_get_contents($fileDestination))) {
                        include_once("importFile.php");
                        ?>
                            <script>alert("Le fichier à bien été importé !");</script>
                        <?php
                    }else {
                        $error = "Erreur critique le fichier n'a pas pu etre importé dans la base!";
                        include_once("importFile.php");
                    }
                } else {
                    $error = "Erreur critique le fichier n'a pas pu etre téléchargé sur le serveur !";
                    include_once("importFile.php");
                }
            } else {
                $error = "Le fichier est trop volumineux";
                include_once("importFile.php");
            }
        } else {
            $error = "erreur lors du telechargement";
            include_once("importFile.php");
        }
    } else {
        $error = "Aucun fichier ou mauvais type de fichier";
        include_once("importFile.php");
    }
}

?>