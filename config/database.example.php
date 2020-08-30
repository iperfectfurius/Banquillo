<?php 
// This tool use MYSQL to verify and ban clients.
// Fill all values(*)

/* Database Example:

-----Tables-----
* bans
* votes

----Bans----
! Mandatory columns

* name->varchar
* votes->int

! Opcional columns (just for log info)

* voted_at->timestamp

----Votes----
! Mandatory columns

* name->varchar
* ip->varchar
* voted_client_name->vachar
* voted_ip->varchar
* valid_vote->tinyint

! Opcional columns (just for log info)

* id->int
* voted_at->timestamp

*/
 class Database{

	private $host = "127.0.0.1";
	private $db_name = "XXX*";
	private $username = "XXX*";
	private $password = "XXX*";
	public $conn;

	//Works with mysql, if needs other BBDD you probably go to check all other config files.
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