<?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;

$server = '6b490305805f494d9a2adb1b1e1ee8e9.s1.eu.hivemq.cloud';     // change if necessary
$port = 8883;                     // change if necessary
$username = 'PHPclient';                   // set your username
$password = '';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()
$cafile = '...\isrgrootx1.pem'; // HiveMQ Cloud CA

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id, $cafile);

if ($mqtt->connect(true, NULL, $username, $password)) {
	$mqtt->publish('temperature', 'Hello World! at ' . date('r'), 0, false);
} else {
    echo "Time out!\n";
}

$mqtt->debug = true;

$topics['dht_data'] = array('qos' => 0, 'function' => 'procMsg');
$topics['hc_data'] = array('qos' => 0, 'function' => 'procMsg');
$mqtt->subscribe($topics, 0);

while($mqtt->proc()) {
    sleep(2);
}

function procMsg($topic, $msg){
    echo 'Msg Recieved: ' . date('r') . "\n";
    echo "Topic: {$topic}\n\n";
    
    echo "\t$msg\n\n";

    $obj = json_decode($msg, true);

    $device_id = $obj["deviceId"];
    if ($topic == "dht_data"){
        $data1 = $obj["temperature"];
        $data2 = $obj["humidity"];
        $data_type1 = "temperature";
        $data_type2 = "humidity";
    }
    if ($topic == "hc_data"){
        $data1 = $obj["distance"];
        $data_type1 = "distance";
    }
    
    $timestamp_str = $obj["timestamp"];
    $acq_date = substr($timestamp_str, 0, 10);
    $acq_time = substr($timestamp_str, 11);

    $servername = "mysql.agh.edu.pl";
    $username = "mikozak";
    $password = ""; //password for phpmyadmin
    $dbname = "mikozak";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(!$conn){
        die("Connection falied: ".mysqli_connect_error());
    }
    
    $sql_check = "SELECT EXISTS(SELECT 1 FROM telemed_data WHERE 
        (device_ID='$device_id') AND (acq_date='$acq_date') AND (acq_time='$acq_time') AND (data_type='$data_type1')) as check_if";
    $query = mysqli_query($conn, $sql_check);
    if($query){
        echo "Sprawdzono\n";
    } else {
        echo "Błąd: " .$sql_check. "\n".mysqli_error($conn);
    }

    if(mysqli_fetch_assoc($query)["check_if"] == 0){
        if ($topic == "dht_data"){
            $sql1 = "INSERT INTO telemed_data (device_ID, acq_date, acq_time, data_type, data) 
                VALUES ('$device_id', '$acq_date', '$acq_time', '$data_type1', '$data1')";

            $sql2 = "INSERT INTO telemed_data (device_ID, acq_date, acq_time, data_type, data) 
                VALUES ('$device_id', '$acq_date', '$acq_time', '$data_type2', '$data2')";
            
            if(mysqli_query($conn, $sql1)){
                echo "Dopisano ".$data_type1."!\n";
            } else {
                echo "Błąd: " .$sql1. "\n".mysqli_error($conn);
            }
            if(mysqli_query($conn, $sql2)){
                echo "Dopisano ".$data_type2."!\n";
            } else {
                echo "Błąd: " .$sql2. "\n".mysqli_error($conn);
            }
        }

        if ($topic == "hc_data"){
            $sql1 = "INSERT INTO telemed_data (device_ID, acq_date, acq_time, data_type, data) 
                VALUES ('$device_id', '$acq_date', '$acq_time', '$data_type1', '$data1')";
            
            if(mysqli_query($conn, $sql1)){
                echo "Dopisano ".$data_type1."!\n";
            } else {
                echo "Błąd: " .$sql1. "\n".mysqli_error($conn);
            }
        }
    }
    else{
        echo "Rekord dla tych danych już istnieje w bazie.\n";
    }
}

?>
