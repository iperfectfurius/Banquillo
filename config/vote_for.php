<?php
ini_set('display_errors', true);
header("Access-Control-Allow-Methods: POST");

include './client_list.php';
include './vote.php';

$data = json_decode(file_get_contents("php://input"));
$voted_client_san = htmlspecialchars(strip_tags($data->voted_clid));
foreach ($clientList as $voted_client) {
	if ($voted_client['clid'] == $voted_client_san) {
		$client_voted = $voted_client;
	}
}
foreach ($clientList as $client) {
	if ($client['connection_client_ip'] == $_SERVER['REMOTE_ADDR']) {
		$who = $client;
	}
}
$vote = new Vote($db, $who, $client_voted);
$vote->vote();
http_response_code(200);