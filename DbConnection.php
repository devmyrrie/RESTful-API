
<?php
class DbConnection

	{
	private $config;
	private $hostname;
	private $username;
	private $password;
	private $dbname;
	private $db;
	public

	function __construct()
		{
			$this->config = parse_ini_file('config.ini', true);
			$this->hostname = $this->config['database']['host'];
			$this->username = $this->config['database']['user'];
			$this->password = $this->config['database']['password'];
			$this->dbname    = $this->config['database']['dbname'];

		}
function close(){
	$this->db= null;	
}

	function connect()
		{
		$this->db = null;
		try
			{
			//charset utf8 es importante para guardar tildes y asi, en la bd	
			$this->db = new PDO("mysql:host=$this->hostname;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}

		catch(PDOException $e)
			{
			echo $e->getMessage();
			}

		return $this->db;
		}
	}

?>


