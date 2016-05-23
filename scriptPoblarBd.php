
<?php
// Script para cargar a la base de datos desde  archivo
// ----------****--------------------****----------
//ERROR LINEA 58 y 82
require_once('DbConnection.php');
class scriptPoblarBd
{
    private $dbConnection;
    private $db;
    private $provinciasArray;
    public function __construct()
    {
        $this->dbConnection = new DbConnection();
        $this->db           = $this->dbConnection->connect();
    }
    public function correr()
    {
        $listOfPlaces;
        $provinciasArray = array(
            "San José" => 1,
            "Alajuela" => 2,
            "Cartago" => 3,
            "Heredia" => 4,
            "Limón" => 5,
            "Guanacaste" => 6,
            "Puntarenas" => 7
        );
        if ($this->db != null)
        {
            try
            {
                $this->db->beginTransaction();
                $stm        = $this->db->prepare("INSERT INTO lugar(nombreLugar,descripcionLugar,telefonoLugar) VALUES(:nombreLugar,:descripcionLugar,:telefonoLugar)");
                $file       = fopen('prodisInfo.csv', 'r');
                $primeraVez = true;
                while (($line = fgetcsv($file, ',')) !== FALSE)
                {
                    if ($primeraVez)
                    {
                        $primeraVez = false;
                    }
                    else
                    {
                        // $line is an array of the csv elements
                        $stm->execute([':nombreLugar' => $line[0], ':descripcionLugar' => $line[3], ':telefonoLugar' => $line[5] ]);
                        // Guardamos la info en el vector, el indice es el idlugar de cada lugar
                        $idLugar                                    = $this->db->lastInsertId();
                        $listOfPlaces[$idLugar]["nombreLugar"]      = $line[0];
                        $listOfPlaces[$idLugar]["descripcionLugar"] = $line[3];
                        $listOfPlaces[$idLugar]["nombreProvincia"]  = $line[1];
                        $listOfPlaces[$idLugar]["ubicacionLugar"]   = $line[2];
                        $listOfPlaces[$idLugar]["link"]             = $line[4];
                        $listOfPlaces[$idLugar]["telefonoLugar"]    = $line[5];
                        //    $nombreProvincia = utf8_decode($line[1]); ERRO no lo encuentra
                        $nombreProvincia                            = $line[1];
                        $listOfPlaces[$idLugar]["idProvincia"]      = $provinciasArray[$nombreProvincia];
                        $listOfPlaces[$idLugar]["idLugar"]          = $this->db->lastInsertId();
                    }
                }
                fclose($file);
                $stm = $this->db->prepare("INSERT INTO selocaliza(idLugar,idProvincia,link,ubicacionLugar,esSugerencia) VALUES (:idLugar,:idProvincia,:link,:ubicacionLugar,:esSugerencia)");
                foreach ($listOfPlaces as $aPlace)
                {
                    $stm->execute([':idLugar' => $aPlace["idLugar"], ':idProvincia' => $aPlace["idProvincia"], ':link' => $aPlace["link"], ':ubicacionLugar' => $aPlace["ubicacionLugar"], ':esSugerencia' => 0 ]);
                }
                $this->db->commit();
            }
            catch (PDOException $e)
            {
                $this->db->rollBack();
                $primerInsert = false;
                echo $e->getMessage();
            }
        }
    }
}
echo "HOLA";
$script = new scriptPoblarBd();
$script->correr();
?>