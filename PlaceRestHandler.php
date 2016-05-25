<?php
require_once("SimpleRestInfo.php");
require_once("Place.php");
class PlaceRestHandler extends SimpleRestInfo
{
    function getAllPlaces()
    {
		$response="";
        $place      = new Place();
        $rawData    = $place->getAllPlaces();
        $statusCode = "";
        if (empty($rawData))
        {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No places  found!'
            );
        }
        else
        {
            $statusCode = 200;
        }
        $requestContentType = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        $this->setClientHttpHeaders($requestContentType, $statusCode);
        // Asumimos que se pide con JSON
        if (strpos($requestContentType, 'application/json') !== false)
        {
            $response = $this->encodeJson($rawData);
        }
		return $response;
    }
    function addPlace($info)
    {
		$response="";
        $place      = new Place();
        $location   = $place->addPlace($info);
        $statusCode = "";
		$rawData="";
        if ($location == "")
        {
            $statusCode = 400;
            $rawData    = array(
                'error' => 'Not created,Bad Request!'
            );
        }
        else
        {
            $statusCode = 201;
            $rawData    = array(
                'ok' => 'Succesfully created!'
            );
        }
        $this->setServerHttpHeaders($location, $statusCode);
        $response = $this->encodeJson($rawData);
        return $response;
        // Asumimos que se pide con JSON
    }
    function getAllPlacesInRegion($provincia)
    {
		$response="";
        $place      = new Place();
        $rawData    = $place->getAllPlacesInRegion($provincia);
        $statusCode = "";
        if (empty($rawData))
        {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No place  found!'
            );
        }
        else
        {
            $statusCode = 200;
        }
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setClientHttpHeaders($requestContentType, $statusCode);
        // Asumimos que se pide con JSON
        if (strpos($requestContentType, 'application/json') !== false)
        {
            $response = $this->encodeJson($rawData);
        }
		return $response;
    }
    function getSinglePlaceInRegion($id1, $id2)
    {
		$response="";
        $place      = new Place();
        $rawData    = $place->getSinglePlaceInRegion($id1, $id2);
        $statusCode = "";
		
        if (empty($rawData))
        {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No place  found!'
            );
        }
        else
        {
            $statusCode = 200;
        }
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setClientHttpHeaders($requestContentType, $statusCode);
        // Asumimos que se pide con JSON
        if (strpos($requestContentType, 'application/json') !== false)
        {
            $response = $this->encodeJson($rawData);
        }
		return $response;
    }
    function getSinglePlace($nombreLugar)
    {
		$response="";
        $place      = new Place();
        $rawData    = $place->getSinglePlace($nombreLugar);
        $statusCode = "";
        if (empty($rawData))
        {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No place  found!'
            );
        }
        else
        {
            $statusCode = 200;
        }
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setClientHttpHeaders($requestContentType, $statusCode);
        // Asumimos que se pide con JSON
        if (strpos($requestContentType, 'application/json') !== false)
        {
            $response = $this->encodeJson($rawData);
        }
		return $response;
    }
    public function encodeJson($responseData)
    {
        $jsonResponse = json_encode($responseData);
        return $jsonResponse;
    }
}
?>