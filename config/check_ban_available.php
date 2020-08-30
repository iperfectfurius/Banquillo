<?php
header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set('Europe/Madrid');
define('MIN_TO_VOTE_BAN', 5);

include './database.php';
global $db;

$query = "SELECT * FROM bans order by voted_at desc limit 1";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch();

$startTime = date('Y-m-d H:i:s', strtotime($result['voted_at']));
$time_to_ban = date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($startTime)));

if ($time_to_ban > date('Y-m-d H:i:s')) {
	echo json_encode(['ban' => 'false','lastBan'=>$startTime]);
	$ban_available = false;
} else {
	echo json_encode(['ban' => 'true','lastBan'=>$startTime]);
	$ban_available = true;
}

	//$query = "SELECT name, count(name) as bans from bans group by name";
