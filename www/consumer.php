<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbit', 5672, 'user', 'password');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$channel->queue_purge('hello');

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$logFileName = 'log.txt';
file_put_contents($logFileName, "");

$callback = function ($msg) use ($logFileName) {
    $logfile = fopen($logFileName, "a");
    $body = json_decode($msg->body);
    if (count($body) === 2) {
        fwrite($logfile, sprintf('User id: %s. Event id: %s', $body[0], $body[1]) . "\n");
        sleep(1);
    }
    fclose($logfile);
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}