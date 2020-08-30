<?php
include './connect.php';
$admin = "192.168.1.10";
$who = $_SERVER['REMOTE_ADDR'];

foreach ($clientList as $client) 
	if ($client['connection_client_ip'] == $who) {
		$client['admin'] = $client['connection_client_ip'] == $admin ? true: false;
		die(json_encode($client));
	}

echo json_encode(['error'=> 'Donde vas pringao','client_nickname'=>'Pringao', 'users' => sizeof($clientList)]);
//$clientList
