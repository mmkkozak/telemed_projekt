<?php
header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename='.$_POST['data_type'].'.csv');

$fp = fopen('php://output', 'w');
$json = $_POST['data'];
$array = json_decode($json, true);

foreach ($array as $line) {
    fputcsv($fp, $line, ";");
}

fclose($fp);
?>