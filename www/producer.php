<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbit', 5672, 'user', 'password');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$i = 0;
while ($i < 10000) {
    $userId = rand(0, 999);

    $chunkSize = rand(0, 9);
    $chunkI = 0;
    while ($chunkI < $chunkSize) {
        $body = json_encode([$userId, $i]);
        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, '', 'hello');
        $chunkI++;
        $i++;
    }

}

$channel->close();
$connection->close();