
<?php

// El get tiene que tener como header Content-type application-json y ademas ACCept

require_once ("PlaceRestHandler.php");

$view = ""; //variable que se pasa como argumento en el URL del .htaccess
$isPost = false;
$isGet = false;

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

/*
Controla los servicios REST y mapea eso
*/
/** El formato JSON para  POST es el siguiente:
 {
 "Lugares": [
 {
 "nombreLugar": "La Canela",
 "descripcionLugar": "Lugar de repostería",
 "nombreProvincia": "San_Jose",
 "ubicacionLugar": "Al frende de RadioU en la UCR",
 "link": "http://2.bp.blogspot.com/",
 "telefonoLugar": null
 }

 ]
 }

 */

// Vemos que tipo de RESTful URL es

if (isset($_GET["view"]))
{
	$view = $_GET["view"]; 
}

switch ($view)
{
case "allPlaces": //FUNCIONA CON GET FALTA POST

	// to handle REST Url /lugares/

	if ($isGet)
	{ //Obtenemos todos los lugares de los que hay informacion en la bd
		$placeRestHandler = new PlaceRestHandler();
		$placeRestHandler->getAllPlaces();
	}
	else
	{
		if ($isPost)
		{ //Agregamos un lugar a la base de datos, como sugerido.
			$dataBack = json_decode(file_get_contents('php://input') , true); //data tiene los datos ya listos!
			$placeRestHandler = new PlaceRestHandler();
			$placeRestHandler->addPlace($dataBack);
		}
	}

	break;

case "singleInRegion": //FUNCIONA CON GET falta POST

	// to handle REST Url /lugares/<provincia>/<nombreLugar>/

	$placeRestHandler = new placeRestHandler();
	$provincia = $_GET["id1"];
	$nombLugar = $_GET["id2"];
	$provincia =standarize($provincia);
	$nombLugar = standarize($nombLugar);
	$placeRestHandler->getSinglePlaceInRegion($provincia, $nombLugar);
	break;

case "singlePlace": //FUNCIONA con GET falta POST

	// to handle REST Url /lugares/<nombreLugar>/
	$placeRestHandler = new placeRestHandler();
	$id = $_GET["id"];

	$id= standarize($id); 
	$placeRestHandler->getSinglePlace($id);
	break;

case "allInRegion": //FUNCIONA con get falta POST

	// to handle REST Url /lugares/<provincia>/

	$placeRestHandler = new placeRestHandler();
	$provincia = $_GET["id"]; 
	$provincia = standarize($provincia); 
	$placeRestHandler->getAllPlacesInRegion($provincia);
	break;

case "":

	// 404 - not found;

	break;
}
 /*Pone los espacios en blanco en lugar de +, y ademas quita el asterisco final si lo hay*/
 function standarize($string){
	 $returnedString = $string ;
	$lastString = substr($string, -1);
	if($lastString == "*"){
		$returnedString = substr($string, 0, -1);//eliminamos el * final que decia si es provincia
	}
	$returnedString=str_replace("-"," ",$returnedString);// cambiamos - por spacios en blanco
	return $returnedString ;	
	
}

?>