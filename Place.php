<?php
/*
A domain Class to  RESTful web services
esSugerencia, si es 0 NO ES SUGERENCIA, si es 1 ES SUGERENCIA
*/
require_once('DbConnection.php');
Class Place
{
    private $dbConnection;
    private $db;
    private $provinciasArray;
    public function __construct()
    {
        $this->dbConnection    = new DbConnection();
        $this->db              = $this->dbConnection->connect();
        $this->provinciasArray = array(
            "San José" => 1,
            "Alajuela" => 2,
            "Cartago" => 3,
            "Heredia" => 4,
            "Limón" => 5,
            "Guanacaste" => 6,
            "Puntarenas" => 7
        );
    }
    function isJson($array)
    {
        json_encode($array);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    function validFormat($array)
    {
        //validate JSON format    
    }
    /** El formato JSON para  POST es el siguiente:
    {
    "Lugares": [
    {
    "nombreLugar": "La Canela",
    "descripcionLugar": "Lugar de reposterÃ­a",
    "nombreProvincia": "San_Jose",
    "ubicacionLugar": "Al frende de RadioU en la UCR",
    "link": "http://2.bp.blogspot.com/",
    "telefonoLugar": null
    }
    ]
    }
    */
    public function addPlace($info)
    {
        //filter_var($string, FILTER_SANITIZE_STRING);
        $place = "";
        if ($this->isJson($info))
        {
            $nombreLugar      = isset($info["Lugares"]["nombreLugar"]) ? $info["Lugares"]["nombreLugar"] : "";
            $descripcionLugar = isset($info["Lugares"]["descripcionLugar"]) ? $info["Lugares"]["descripcionLugar"] : "";
            $nombreProvincia  = isset($info["Lugares"]["nombreProvincia"]) ? $info["Lugares"]["nombreProvincia"] : "";
            $idProvincia      = ($nombreProvincia != "") ? $this->provinciasArray[$nombreProvincia] : -1;
            $ubicacionLugar   = isset($info["Lugares"]["ubicacionLugar"]) ? $info["Lugares"]["ubicacionLugar"] : "";
            $link             = isset($info["Lugares"]["link"]) ? $info["Lugares"]["link"] : null;
            $telefonoLugar    = isset($info["Lugares"]["telefonoLugar"]) ? $info["Lugares"]["telefonoLugar"] : "";
            $primerInsert     = true;
            $lastID           = null;
            if ($nombreLugar != "" && $descripcionLugar != "" && $nombreProvincia != "" && $idProvincia != -1 && $ubicacionLugar != "" && $telefonoLugar != "")
            {
                if ($nombreProvincia == "San José")
                {
                    $place = "http://192.168.0.104/PHP/lugares/San-Jose*";
                }
                else
                {
                    if ($nombreProvincia == "Limón")
                    {
                        $place = "http://192.168.0.104/PHP/lugares/Limon*";
                    }
                    else
                    {
                        $place = "http://192.168.0.104/PHP/lugares/" . $nombreProvincia;
                    }
                }
                if ($this->db != null)
                {
                    try
                    {
                        $this->db->beginTransaction();
                        $stm = $this->db->prepare("INSERT INTO lugar(nombreLugar,descripcionLugar,telefonoLugar) VALUES(:nombreLugar,:descripcionLugar,:telefonoLugar)");
                        $stm->bindValue(':nombreLugar', $nombreLugar);
                        $stm->bindValue(':descripcionLugar', $descripcionLugar);
                        $stm->bindValue(':telefonoLugar', $telefonoLugar);
                        $stm->execute();
                        //Antes de llamar a commit debemos llamar a lastindex sino es un bug.            
                        $lastID = $this->db->lastInsertId();
                        $stm    = $this->db->prepare("INSERT INTO selocaliza(idLugar,idProvincia,link,ubicacionLugar,esSugerencia) VALUES (:idLugar,:idProvincia,:link,:ubicacionLugar,:esSugerencia)");
                        $stm->bindValue(":idLugar", $lastID);
                        $stm->bindValue(":idProvincia", $idProvincia);
                        $stm->bindValue(":link", $link);
                        $stm->bindValue(":ubicacionLugar", $ubicacionLugar);
                        $stm->bindValue(":esSugerencia", 1);
                        $stm->execute();
                        $this->db->commit();
                    }
                    catch (PDOException $e)
                    {
                        $this->db->rollBack();
                        $primerInsert = false;
                        echo $e->getMessage();
                        $place = "";
                    }
                    if ($this->db != null)
                    {
                        $this->dbConnection->close();
                    }
                }
            }
        }
        return $place;
    }
    public function getAllPlaces()
    {
        $resultArray = array();
        if ($this->db != null)
        {
            $sql = "SELECT lg.nombreLugar,lg.descripcionLugar, pro.nombreProvincia,sl.ubicacionLugar,sl.link,lg.telefonoLugar
                from provincia pro,    lugar lg, selocaliza sl
                WHERE pro.idProvincia=sl.idProvincia and lg.idlugar = sl.idlugar";
            try
            {
                foreach ($this->db->query($sql) as $row)
                {
                    $resultArray["Lugares"][] = $row;
                }
            }
            catch (PDOException $e)
            {
                throw $e;
            }
        }
        else
        {
            $error = array(
                'error' => 'No place  found!'
            );
            echo $error;
        }
        return $resultArray;
    }
    public function getSinglePlace($nombreLugar) //Esta funcion me devuelve  el lugar determinado por el id ( todos los lugares es una lista, devuelve el de la posicion id) con respecto tabla LUGAREs
    {
        $resultArray = array();
        if ($this->db != null)
        {
            $sql = "SELECT lg.nombreLugar,lg.descripcionLugar, pro.nombreProvincia,sl.ubicacionLugar,sl.link,lg.telefonoLugar 
             from provincia pro, lugar lg, selocaliza sl
             WHERE pro.idProvincia=sl.idProvincia and lg.idlugar = sl.idlugar  and  lg.nombreLugar = " . "'" . $nombreLugar . "'";
            foreach ($this->db->query($sql) as $row)
            {
                $resultArray["Lugares"][] = $row;
            }
        }
        else
        {
            $error = array(
                'error' => 'No place  found!'
            );
            echo $error;
        }
        return $resultArray;
    }
    public function getAllPlacesInRegion($nombreProvincia)
    {
        $resultArray = array();
        if ($this->db != null)
        {
            $sql = "SELECT lg.nombreLugar,lg.descripcionLugar, pro.nombreProvincia,sl.ubicacionLugar,sl.link,lg.telefonoLugar 
             from provincia pro, lugar lg, selocaliza sl
             WHERE pro.idProvincia=sl.idProvincia and lg.idlugar = sl.idlugar and sl.idProvincia = 
             (SELECT idProvincia from provincia WHERE nombreProvincia= " . "'" . $nombreProvincia . "' )";
            foreach ($this->db->query($sql) as $row)
            {
                $resultArray["Lugares"][] = $row;
            }
        }
        else
        {
            $error = array(
                'error' => 'No place  found!'
            );
            echo $error;
        }
        return $resultArray;
    }
    public function getSinglePlaceInRegion($nombreProvincia, $nombreLugar)
    {
        $resultArray = array();
        if ($this->db != null)
        {
            $sql = "SELECT lg.nombreLugar,lg.descripcionLugar, pro.nombreProvincia,sl.ubicacionLugar,sl.link,lg.telefonoLugar
                       from provincia pro, lugar lg, selocaliza sl 
                       WHERE pro.idProvincia=sl.idProvincia and lg.idlugar = sl.idlugar and sl.idProvincia = 
                       (SELECT idProvincia from provincia WHERE nombreProvincia=" . "'" . $nombreProvincia . "'" . ") and lg.nombreLugar=" . "'" . $nombreLugar . "'";
            foreach ($this->db->query($sql) as $row)
            {
                $resultArray["Lugares"][] = $row;
            }
        }
        else
        {
            $error = array(
                'error' => 'No place  found!'
            );
            echo $error;
        }
        return $resultArray;
    }
    public function close()
    {
        $this->db = null;
    }
}
?>