<?php
$client = new swoole_client(SWOOLE_UNIX_STREAM, SWOOLE_SOCK_SYNC);
$retConnect = $client->connect('/tmp/daitianlei.sock', 0, 3);
var_dump($retConnect);
$noticeData = json_encode(array('type' => 'ping', 'serverName' => ''));
$retSend = $client->send($noticeData);
var_dump($noticeData, $retSend);
$ret = $client->recv();
var_dump($ret);
$ret = json_decode($ret, true);
$client->close();
