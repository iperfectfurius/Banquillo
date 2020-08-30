<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
include './client_list.php';
ob_end_clean();
global $db;
$query = "SELECT name, count(name) as bans from bans group by name";
$stmt = $db->prepare($query);
$stmt->execute();
$list = [];
while ($result = $stmt->fetch()) {
	$list[] = [
		'user' => $result['name'],
		'bans' => $result['bans']
	];
}
echo json_encode($list);
