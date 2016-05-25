<?php
require_once("PlaceRestHandler.php");
$view   = ""; //variable passed from .htaccess (view)
$isPost = false;
$isGet  = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $isPost = true;
}
else
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        $isGet = true;
    }
}
//Depending on the "view" we  carry out some different actions
if (isset($_GET["view"]))
{
    $view = $_GET["view"];
}
switch ($view)
{
    case "allPlaces":
        // to handle REST Url /lugares/
        $response = "";
        if ($isGet)
        //Let us obtain all existing places
        {
            $placeRestHandler = new PlaceRestHandler();
            $response         = $placeRestHandler->getAllPlaces();
        }
        else
        {
            if ($isPost)
            //Add a place to the database ( as a suggestion)
            {
                $dataBack         = json_decode(file_get_contents('php://input'), true); //data tiene los datos ya listos!
                $placeRestHandler = new PlaceRestHandler();
                $response         = $placeRestHandler->addPlace($dataBack);
            }
        }
        echo $response;
        break;
    case "singlePlaceInProvince":
        // to handle REST Url /lugares/<provincia>/<id>/
        if ($isGet)
        {
            $placeRestHandler = new placeRestHandler();
            $province         = $_GET["id1"];
            $idLugar          = $_GET["id2"];
            $province         = filter_var(standarize($province), FILTER_SANITIZE_STRING);
            $response         = $placeRestHandler->getSinglePlaceInRegion($province, $idLugar);
            echo $response;
        }
        break;
    case "singlePlace":
        if ($isGet)
        {
            // to handle REST Url /lugares/<id>/
            $placeRestHandler = new placeRestHandler();
            $id               = $_GET["id"];
            $response         = $placeRestHandler->getSinglePlace($id);
            echo $response;
        }
        break;
    case "allPlacesInProvince":
        if ($isGet)
        {
            // to handle REST Url /lugares/<provincia>/
            $placeRestHandler = new placeRestHandler();
            $province         = $_GET["id"];
            $province         = standarize($province);
            $response         = $placeRestHandler->getAllPlacesInRegion($province);
            echo $response;
        }
        break;
    case "":
        // 400 BAD RESQUEST;
        echo "400 BAD REQUEST";
        break;
}
/* Replaces "-" for " " and gets rid of ~ at the beggining of the string */
function standarize($string)
{
    $returnedString = $string;
    $firstString    = $string[0];
    if ($firstString == "~")
    {
        $returnedString = str_replace("~", "", $returnedString, $count);
    }
    $returnedString = str_replace("-", " ", $returnedString); // Replaces - for  space.
    return $returnedString;
}
?> 