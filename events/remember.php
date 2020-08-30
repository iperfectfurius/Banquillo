<?php
ini_set('display_errors', true);
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

// Log in
send('login serveradmin RU67tGQI');
socket_read($socket, 2048, PHP_BINARY_READ);

// Use server 1
send('use sid=1');
socket_read($socket, 2048, PHP_BINARY_READ);

send('gm msg=[url=http://81.203.8.151/banquillo]Banquillo\sDisponible![/url]');

send('quit');
socket_shutdown($socket);
socket_close($socket);