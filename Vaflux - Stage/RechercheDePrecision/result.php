<?php 

// 0053.07371

$storeRes = array();

$icpeLat = "";
$icpeLng = "";
$wms;

$cavState = false;
$cavMsg = "Non évalué !";
$cavColor = "";

$ziState = false;
$ziMsg = "Non évalué !";
$ziColor = "";

$pprnState = false;
$pprnMsg = "Non évalué !";
$pprnColor = "";

$con = pg_connect("XXX")
or die("Connexion impossible à la base de données");

// $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'14%\' and codes3ic is not null;';
// $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
// while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
//     array_push($storeAIOT,$resultSelectCodeAIOT[0]);
// }

// $selectTest = 'SELECT * from "etablissement";';
// $resSelectTest = pg_query($con,$selectTest);
// $test = pg_fetch_array($resSelectTest);
//     echo "<pre>";
//     print_r($test);
//     echo "</pre>";


if(isset($_POST["submit"])) {
    if($_POST["type"] == "unique") {
        $icpe = $_POST["icpe"];
        $type = $_POST["type"];
        $selectICPE = 'SELECT nomusuel,adresses3ic,communes3ic,departements3ic from "etablissement" where codes3ic = \''.$icpe.'\';';
        $resICPE = pg_query($con,$selectICPE);
        $row = pg_fetch_array($resICPE);
        $icpeAdr = $row[0]."<br>".$row[1].", ".$row[2].", ".$row[3];
        $aleas = "";
        if(!empty($_POST["inondation"])) {
            if(in_array("debordement",$_POST["inondation"])) {
                $aleas = "debordement";
                rechercheZIUni($con,$icpe);
                if($ziState == false) {
                    $ziMsg = "Rien à signaler !";
                    $ziColor = "green";
                } elseif($ziState == true) {
                    $ziMsg = "Concerné !";
                    $ziColor = "red";
                } elseif (empty($ziState)) {
                    $ziMsg = "Non évalué";
                }
            }
            if(in_array("submersion",$_POST["inondation"])) {
                echo "test2";
            }
            if(in_array("nappe",$_POST["inondation"])) {
                echo "test3";
            }
        }
        if(!empty($_POST["chuteDeBlocs"])) {
            echo  "slt2";
        }
        if(!empty($_POST["cavites"])) {
            $aleas = $aleas."/cavité";
            rechercheCavUni($con,$icpe);
            if($cavState == false) {
                $cavMsg = "Rien à signaler !";
                $cavColor = "green";
            } elseif($cavState == true) {
                $cavMsg = "Concerné !";
                $cavColor = "red";
            } elseif (empty($cavState)) {
                $cavMsg = "Non évalué";
            }
        }
        if(!empty($_POST["pprn"])) {
            $aleas = $aleas."/pprn";
            recherchePPRNUni($con,$icpe);
            if($pprnState == false) {
                $pprnMsg = "Rien à signaler !";
                $pprnColor = "green";
            } elseif($pprnState == true) {
                $pprnMsg = "Concerné !";
                $pprnColor = "red";
            } elseif (empty($pprnState)) {
                $pprnMsg = "Non évalué";
            }
        }
        include_once("../structure/header/header.html");
        include_once("result.html");
        include_once("../structure/footer/footer.html");
    }elseif($_POST["type"] == "departement") {
        $icpe = $_POST["icpe"];
        $type = $_POST["type"];
        if(!empty($_POST["inondation"])) {
            echo  "slt1";
            if(in_array("debordement",$inondation)) {
                echo "test1";
            }
            if(in_array("submersion",$inondation)) {
                echo "test2";
            }
            if(in_array("nappe",$inondation)) {
                echo "test3";
            }
        }
        if(!empty($_POST["chuteDeBlocs"])) {
            echo  "slt2";
        }
        if(!empty($_POST["cavites"])) {
            echo  "slt3";
        }
        if(!empty($_POST["pprn"])) {
            recherchePPRNDep($con,$icpe);
        }
        include_once("../structure/header/header.html");
        include_once("resultDep.html");
        include_once("../structure/footer/footer.html");
    }elseif($_POST["type"] == "service") {
        $icpe = $_POST["icpe"];
        $type = $_POST ["type"];
        echo $type;
        echo $icpe;
        include_once("../structure/header/header.html");
        //include_once("choixAleas.html");
        include_once("../structure/footer/footer.html");
    } else {
        include_once("../structure/header/header.html");
        include_once("choixAleas.html");
        include_once("../structure/footer/footer.html");
    }
}

// ===== FUNCTION ===== //

function recherchePPRNUni($con,$icpe) {
    global $icpeLng,$icpeLat,$polygon,$wms,$pprnState;
    $select = 'SELECT count(*) from "Element_83";';
    $resSelect = pg_query($con,$select);
    $resRow = pg_fetch_row($resSelect);
    $selectST = 'SELECT Distinct st_dwithin((SELECT geom from "etablissement" where codes3ic = \''.$icpe.'\'),geom2154,200) as isIn from "Element_83";';
    $resSelectST = pg_query($con, $selectST);
    $selectLngLat = 'SELECT st_x(st_centroid(st_transform(geom,4326))) as lng,st_y(st_centroid(st_transform(geom,4326))) as lat from "etablissement" where codes3ic = \''.$icpe.'\';';
    $resSelectLngLat = pg_query($con,$selectLngLat);
    while($result = pg_fetch_row($resSelectST)) {
        if($result[0] == "t") {
            $pprnState = true;
        } else {
            $pprnState = false;
        }
    }
    $result2 = pg_fetch_row($resSelectLngLat);
    $icpeLng = $result2[0];
    $icpeLat = $result2[1];
}

function rechercheZIUni($con,$icpe) {
    global $icpeLng,$icpeLat,$polygon,$wms,$ziState;
    $select = 'SELECT count(*) from "zi_bn";';
    $resSelect = pg_query($con,$select);
    $resRow = pg_fetch_row($resSelect);
    $selectST = 'SELECT Distinct st_dwithin((SELECT geom from "etablissement" where codes3ic = \''.$icpe.'\'),geom,200) as isIn from "zi_bn";';
    $resSelectST = pg_query($con, $selectST);
    $selectST76 = 'SELECT Distinct st_dwithin((SELECT geom from "etablissement" where codes3ic = \''.$icpe.'\'),geom,200) as isIn from "eaip_76";';
    $resSelectST76 = pg_query($con, $selectST76);
    $selectLngLat = 'SELECT st_x(st_centroid(st_transform(geom,4326))) as lng,st_y(st_centroid(st_transform(geom,4326))) as lat from "etablissement" where codes3ic = \''.$icpe.'\';';
    $resSelectLngLat = pg_query($con,$selectLngLat);
    while($result = pg_fetch_row($resSelectST)) {
        if($result[0] == "t") {
            $ziState = true;
        } else {
            $ziState = false;
        }
    }
    if($ziState != true) {
        while($result76 = pg_fetch_row($resSelectST76)) {
            if($result76[0] == "t") {
                $ziState = true;
            } else {
                $ziState = false;
            }
        }
    }
    $result2 = pg_fetch_row($resSelectLngLat);
    $icpeLng = $result2[0];
    $icpeLat = $result2[1];
}

function rechercheCavUni($con,$icpe) {
    global $icpeLng,$icpeLat,$polygon,$wms,$cavState;
    $select = 'SELECT count(*) from "Element_95";';
    $resSelect = pg_query($con,$select);
    $resRow = pg_fetch_row($resSelect);
    $selectST = 'SELECT Distinct st_dwithin((SELECT geom from "etablissement" where codes3ic = \''.$icpe.'\'),geom2154,200) as isIn from "Element_95";';
    $resSelectST = pg_query($con, $selectST);
    $selectLngLat = 'SELECT st_x(st_centroid(st_transform(geom,4326))) as lng,st_y(st_centroid(st_transform(geom,4326))) as lat from "etablissement" where codes3ic = \''.$icpe.'\';';
    $resSelectLngLat = pg_query($con,$selectLngLat);
    while($result = pg_fetch_row($resSelectST)) {
        if($result[0] == "t") {
            $cavState = true;
        } else {
            $cavState = false;
        }
    }
    $result2 = pg_fetch_row($resSelectLngLat);
    $icpeLng = $result2[0];
    $icpeLat = $result2[1];
}

function recherchePPRNDep($con,$icpe) {
    global $storeRes;
    if($icpe == "Calvados") {
        $id = 0;
        $storeAIOT = array();
        $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'14%\' and codes3ic is not null;';
        $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
        while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
            array_push($storeAIOT,$resultSelectCodeAIOT[0]);
        }
        foreach($storeAIOT as $value) {
            $selectST14 = 'SELECT st_dwithin((SELECT geom2154 from "Element_96" where codes3ic = \''.$value.'\'),geom2154,2000) as isIn from "Element_83";';
            $resSelectST14 = pg_query($con, $selectST14);
            $resultSelectST14 = pg_fetch_row($resSelectST14);
            if($resultSelectST14[0] == "f") {
                array_push($storeRes,$storeAIOT[$id]."/Rien à signaler !");
            } elseif ($resultSelectST14[0] == "t") {
                array_push($storeRes,$storeAIOT[$id]."/Concerné !");
            }
            $id++;
        }
    } elseif($icpe == "Eure") {
        $id = 0;
        $storeAIOT = array();
        $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'27%\' and codes3ic is not null order by codes3ic;';
        $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
        while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
            array_push($storeAIOT,$resultSelectCodeAIOT[0]);
        }
        foreach($storeAIOT as $value) {
            $selectST14 = 'SELECT st_dwithin((SELECT geom2154 from "Element_96" where codes3ic = \''.$value.'\'),geom2154,500) as isIn from "Element_83";';
            $resSelectST14 = pg_query($con, $selectST14);
            $resultSelectST14 = pg_fetch_row($resSelectST14);
            if($resultSelectST14[0] == "f") {
                array_push($storeRes,$storeAIOT[$id]."/Rien à signaler !");
            } elseif ($resultSelectST14[0] == "t") {
                array_push($storeRes,$storeAIOT[$id]."/Concerné !");
            }
            $id++;
        }
    } elseif($icpe == "Manche") {
        $id = 0;
        $storeAIOT = array();
        $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'50%\' and codes3ic is not null order by codes3ic;';
        $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
        while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
            array_push($storeAIOT,$resultSelectCodeAIOT[0]);
        }
        foreach($storeAIOT as $value) {
            $selectST14 = 'SELECT st_dwithin((SELECT geom2154 from "Element_96" where codes3ic = \''.$value.'\'),geom2154,500) as isIn from "Element_83";';
            $resSelectST14 = pg_query($con, $selectST14);
            $resultSelectST14 = pg_fetch_row($resSelectST14);
            if($resultSelectST14[0] == "f") {
                array_push($storeRes,$storeAIOT[$id]."/Rien à signaler !");
            } elseif ($resultSelectST14[0] == "t") {
                array_push($storeRes,$storeAIOT[$id]."/Concerné !");
            }
            $id++;
        }
    } elseif($icpe == "Orne") {
        $id = 0;
        $storeAIOT = array();
        $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'61%\' and codes3ic is not null order by codes3ic;';
        $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
        while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
            array_push($storeAIOT,$resultSelectCodeAIOT[0]);
        }
        foreach($storeAIOT as $value) {
            $selectST14 = 'SELECT st_dwithin((SELECT geom2154 from "Element_96" where codes3ic = \''.$value.'\'),geom2154,500) as isIn from "Element_83";';
            $resSelectST14 = pg_query($con, $selectST14);
            $resultSelectST14 = pg_fetch_row($resSelectST14);
            if($resultSelectST14[0] == "f") {
                array_push($storeRes,$storeAIOT[$id]."/Rien à signaler !");
            } elseif ($resultSelectST14[0] == "t") {
                array_push($storeRes,$storeAIOT[$id]."/Concerné !");
            }
            $id++;
        }
    } elseif($icpe == "Seine-Maritime") {
        $id = 0;
        $storeAIOT = array();
        $selectCodeAIOT = 'SELECT DISTINCT codes3ic from "Element_96" where code_postal like \'72%\' and codes3ic is not null order by codes3ic;';
        $resSelectCodeAIOT = pg_query($con,$selectCodeAIOT);
        while($resultSelectCodeAIOT = pg_fetch_row($resSelectCodeAIOT)) { 
            array_push($storeAIOT,$resultSelectCodeAIOT[0]);
        }
        foreach($storeAIOT as $value) {
            $selectST14 = 'SELECT st_dwithin((SELECT geom2154 from "Element_96" where codes3ic = \''.$value.'\'),geom2154,200) as isIn from "Element_83";';
            $resSelectST14 = pg_query($con, $selectST14);
            $resultSelectST14 = pg_fetch_row($resSelectST14);
            if($resultSelectST14[0] == "f") {
                array_push($storeRes,$storeAIOT[$id]."/Rien à signaler !");
            } elseif ($resultSelectST14[0] == "t") {
                array_push($storeRes,$storeAIOT[$id]."/Concerné !");
            }
            $id++;
        }
    }
    $resSelectST = pg_query($con, $selectST);
    while($result = pg_fetch_row($resSelectST)) {
            echo "<pre>";
            print_r($result);
            echo "</pre>";
    }
}

?>