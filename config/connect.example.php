<?php
// You can rename this file to connect.php and fill strings that contain ('*') at the end for connecting to server

ini_set('display_errors', true);
header('Content-Type: application/json');

$xd = "xd";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false)
	throw new Exception('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));

assert(socket_bind($socket, 0, mt_rand(10220, 10999)) !== false);


if (socket_connect($socket, '127.0.0.1', 10011) !== true)
	throw new Exception('socket_connect() failed: reason: ' . socket_strerror(socket_last_error($socket)));

assert(socket_listen($socket, 8192) !== false);
socket_read($socket, 2048);

function send($msg)
{
	global $socket;
	//echo 'Writing \'' . $msg . '\'... ';
	$msg .= PHP_EOL;
	if (socket_write($socket, $msg, strlen($msg)) !== false) {
		//echo 'Done' . PHP_EOL;
	} else {
		//echo 'Error';
	}
}

// Log in (Need your SSH ts3 server query account https://forum.teamspeak.com/threads/91465-How-to-use-the-Server-Query )
send('login yourAdminAccount* YourPassword*');
socket_read($socket, 2048, PHP_BINARY_READ);

// Use server 1, or 0 (Not always check for what you need if fails)
send('use sid=1');
socket_read($socket, 2048, PHP_BINARY_READ);

// Get user IPs
send('clientlist -ip');
$clientsRaw = socket_read($socket, 1048576, PHP_BINARY_READ);
$clientsRawExploded = substr($clientsRaw, 0, strpos($clientsRaw, "\n"));
$clients = explode('|', $clientsRawExploded);
$clientList = [];

foreach ($clients as $client) {
	$params = explode(' ', $client);

	$clientProperties = [];
	foreach ($params as $param) {
		$equalSignPosition = strpos($param, '=');
		$clientProperties[substr($param, 0, $equalSignPosition)] = str_replace('\\s', ' ', substr($param, $equalSignPosition + 1));
	}
	if (strpos($clientProperties['client_nickname'], 'serveradmin') !== 0)
		$clientList[] = $clientProperties;
}


send('quit');
socket_shutdown($socket);
socket_close($socket);
