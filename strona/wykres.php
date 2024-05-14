<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Wykresy</title>

<?php
session_start();
$servername = "mysql.agh.edu.pl";
$username = "mikozak";
$password = "5i5SvXSpTr4aBUgP";
$dbname = "mikozak";

echo "<br>";

$dbconn = mysqli_connect($servername, $username, $password, $dbname);
$device = mysqli_real_escape_string($dbconn, $_POST["devices"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$date1 = $_POST['from'];
    $date2 = $_POST['till'];
    $data_type = $_POST['type_choice'];
    if ($_POST['device']) $device = $_POST['device'];
	if (!empty($date1) && !empty($date2) && !empty($data_type)) {
        $sql = "SELECT acq_date, acq_time, data  FROM telemed_data 
            WHERE (device_ID='$device') AND (acq_date BETWEEN '$date1' AND '$date2') AND (data_type='$data_type')";
		$query = mysqli_query($dbconn, $sql);

        if($query){
            //echo "Zapytanie wykonane poprawnie\n";
        } else {
            echo "Błąd: " .$sql. "\n".mysqli_error($conn);
        }
	}
    
}

$dataPoints = [];
$dataCSV = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataPoints[] = array("y" => $row["data"], "label" => $row['acq_date']." ".$row['acq_time']);
    $dataCSV[] = array($row['acq_date'], $row['acq_time'], $row['data']);
}

$data_csv = json_encode($dataCSV);
$data_csv = htmlspecialchars($data_csv);
?>

<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	axisX: {
		title: "time"
	},
	axisY: {
		title: "<?php echo $data_type?>"
	},
	data: [{
		type: "line",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>

<body>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <label for="device">Wybrane urządzenie:</label>
    <input type="text" id="device" name="device" value="<?php echo "$device"?>" readonly><br>
    <br>Data od:
    <input type="date" name="from" <?php if(isset($date1)) echo 'value="'.$date1.'"'?>>
    do:
    <input type="date" name="till" <?php if(isset($date2)) echo 'value="'.$date2.'"'?>>
    <br>
    <input type="radio" id="hum" name="type_choice" value="humidity" <?php if($data_type=="humidity") echo "checked"?>>
    <label for="hum">wilgotność</label><br>
    <input type="radio" id="temp" name="type_choice" value="temperature" <?php if($data_type=="temperature") echo "checked"?>>
    <label for="temp">temperatura</label><br>
    <input type="radio" id="dist" name="type_choice" value="distance" <?php if($data_type=="distance") echo "checked"?>>
    <label for="dist">dystans</label><br><br>
    <input type="submit" name="submit" value="Zatwierdź">
</form>

<form action="main.php">
<input type="submit" name="back" value="Powrót"><br><br>
</form>

<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

<form method="POST" action="zapis.php">

    <input type="hidden" name="data" value="<?php echo "$data_csv" ?>">

    <input type="hidden" name="data_type" value="<?php echo $data_type?>">
    <input type="submit" name="save" value="Zapisz do pliku csv"><br>
</form>

</body>
</html>