<?php
// You can rename this file to database.php and fill strings that contain ('*') at the end for connecting to server

error_reporting(E_ALL);
date_default_timezone_set('Europe/Madrid');// You can replace if needed
ini_set('display_errors', true);
//Minimum votes
define('MIN_USERS', 3);

$socket2 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

//Exclusive votes only to server clients.(check for ip)
function checkBan()
{
	global $clientList;
	$exclusiveIps = [];
	foreach ($clientList as $client)
		if (!in_array($client['connection_client_ip'], $exclusiveIps))
			$exclusiveIps[] = $client['connection_client_ip'];

	if (count($exclusiveIps) < 0) {
	} else {
		// $voteToBan is just a % of votes needed for process to ban.
		$votesToBan = ceil((count($exclusiveIps) / 100) * 45);
		echo $votesToBan;
		if ($votesToBan >= MIN_USERS) {
			executeBan();
			echo 'lol';
		}
	}
}
function executeBan()
{
	global $db;
	global $clientList;
	$query = "SELECT voted_ip, count(id) as votes FROM votes WHERE valid_vote = 1 GROUP BY voted_ip ORDER BY votes DESC LIMIT 1";
	$stmt = $db->prepare($query);
	$stmt->execute();

	$result = $stmt->fetch();
	$ip = $result['voted_ip'];
	$votes = $result['votes'];
	foreach ($clientList as $client) {
		if ($client['connection_client_ip'] == $ip) {
			$clid = $client['clid'];
			$client_to_ban = $client;
		}
	}

	global $votesToBan;
	if ($votes >= MIN_USERS  && $votes>= $votesToBan) {
		global $socket2;
		global $db;

		if (!canBan())
			die();

		if ($socket2 === false)
			throw new Exception('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));

		assert(socket_bind($socket2, 0, mt_rand(11000, 11999)) !== false);


		if (socket_connect($socket2, '127.0.0.1', 10011) !== true)
			throw new Exception('socket_connect() failed: reason: ' . socket_strerror(socket_last_error($socket2)));

		assert(socket_listen($socket2, 8192) !== false);
		socket_read($socket2, 2048);

		function send2($msg)
		{
			global $socket2;
			//echo 'Writing \'' . $msg . '\'... ';
			$msg .= PHP_EOL;
			if (socket_write($socket2, $msg, strlen($msg)) !== false) {
				//echo 'Done' . PHP_EOL;
			} else {
				//echo 'Error';
			}
		}
		// Log in
		send2('login yourAdminAccount* YourPassword*');
		socket_read($socket2, 2048, PHP_BINARY_READ);

		echo json_encode(['Baneado' => 'Baneado!']);
		//Delete all votes
		$name = $client_to_ban['client_nickname'];
		$query = "update votes set valid_vote = 0 where valid_vote = 1";
		$stmt = $db->prepare($query);
		$stmt->execute();

		// Use server 1, or 0 (Not always check for what you need if fails)
		send2('use sid=1');
		socket_read($socket2, 2048, PHP_BINARY_READ);

		$query = "insert into bans (name,votes) values(?,?)";
		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $name);
		$stmt->bindParam(2, $votes);
		$stmt->execute();
		// Get user IPs
		$send = "banclient clid=$clid time=60 banreason=te_ha_infectado_el_corona:(   ";
		// Delay before ban
		sleep(6);
		send2($send);
		socket_read($socket2, 2048, PHP_BINARY_READ);
		send2('quit');
		socket_shutdown($socket2);
		socket_close($socket2);
	}
}
function canBan()
{
	global $db;

	$query = "SELECT * FROM bans order by voted_at desc limit 1";
	$stmt = $db->prepare($query);
	$stmt->execute();
	$result = $stmt->fetch();

	$startTime = date('Y-m-d H:i:s', strtotime($result['voted_at']));
	$time_to_ban = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($startTime)));

	if ($time_to_ban > date('Y-m-d H:i:s'))
		return false;
	else
		return true;
}
