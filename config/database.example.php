<?php 
// This tool use MYSQL to verify and ban clients.
// Fill all values(*)
 class Database{

	private $host = "127.0.0.1";
	private $db_name = "XXX*";
	private $username = "XXX*";
	private $password = "XXX*";
	public $conn;

	public function getConnection(){
		try {
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
		} catch(PDOException $exception) {
			echo "Connection error: " . $exception->getMessage();
			die();
		}
  
		return $this->conn;
	}
 }
 
$database = new Database();
$db = $database->getConnection();
?>