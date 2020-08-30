<?php
include './connect.php';
include './database.php';
$who = $_SERVER['REMOTE_ADDR'];
$verified = false;
foreach ($clientList as $client)
	if ($client['connection_client_ip'] == $who){
		$verified = true;
		break;
	}

if(!$verified)
	die(json_encode(['error'=> 'Donde vas pringao']));

$stmt = $db->query('SELECT voted_ip, count(id) as votes FROM votes WHERE valid_vote = 1 GROUP BY voted_ip ORDER BY votes DESC');

$votesByIp = [];
while($client = $stmt->fetch()){
	$votesByIp[$client['voted_ip']] = $client['votes'];
}

$clients = [];
foreach ($clientList as $client)
	$clients[] = [
		'clid' => $client['clid'],
		'client_nickname' => $client['client_nickname'],
		'votes' => (int)($votesByIp[$client['connection_client_ip']] ?? 0)
	];

echo json_encode($clients);

	