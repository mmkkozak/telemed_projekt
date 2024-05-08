<?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;

$server = '6b490305805f494d9a2adb1b1e1ee8e9.s1.eu.hivemq.cloud';     // change if necessary
$port = 8883;                     // change if necessary
$username = 'PHPclient';                   // set your username
$password = 'PHPpassw#3';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()
$cafile = 'C:\Users\kmich\Desktop\studia\podstawy telemedycyny\projekt\github\telemed_projekt\isrgrootx1.pem'; // HiveMQ Cloud CA

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id, $cafile);

if ($mqtt->connect(true, NULL, $username, $password)) {
	$mqtt->publish('temperature', 'Hello World! at ' . date('r'), 0, false);
} else {
    echo "Time out!\n";
}

$mqtt->debug = true;

$topics['temperature'] = array('qos' => 0, 'function' => 'procMsg');
$mqtt->subscribe($topics, 0);

while($mqtt->proc()) {

}

function procMsg($topic, $msg){
		echo 'Msg Recieved: ' . date('r') . "\n";
		echo "Topic: {$topic}\n\n";
		echo "\t$msg\n\n";
}

?>