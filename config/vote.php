<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
include 'ban.php';
class Vote
{
	private $conn;
	private $table_name = "votes";
	public $name;
	public $ip;
	public $voted_client_name;
	public $voted_ip;

	public function __construct($db, $client, $voted_client)
	{
		$this->conn = $db;
		$this->name = $client['client_nickname'];
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->voted_client_name = $voted_client['client_nickname'];
		$this->voted_ip = $voted_client['connection_client_ip'];
	}
	function vote()
	{
		$this->conn->exec('UPDATE votes SET valid_vote = 0 WHERE ip = ' . $this->conn->quote($this->ip));
		$query = "INSERT INTO $this->table_name (`name`,ip,voted_client_name,voted_ip) VALUES (?,?,?,?)";
		$stmt = $this->conn->prepare($query);
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->voted_client_name = htmlspecialchars(strip_tags($this->voted_client_name));
		$stmt->bindParam(1, $this->name);
		$stmt->bindParam(2, $this->ip);
		$stmt->bindParam(3, $this->voted_client_name);
		$stmt->bindParam(4, $this->voted_ip);
		$stmt->execute();
		echo 'antes de ban';
		checkBan();
	}
	//SELECT voted_ip, count(id) as votes FROM votes WHERE valid_vote = 1 GROUP BY voted_ip ORDER BY votes DESC LIMIT 1
}
