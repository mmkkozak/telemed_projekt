<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="main.css">
        <title>Strona główna</title>
    </head>
<body>
<?php
$servername = "mysql.agh.edu.pl";
$username = "mikozak";
$password = "5i5SvXSpTr4aBUgP";
$dbname = "mikozak";

$dbconn = mysqli_connect($servername, $username, $password, $dbname);
$user_email = mysqli_real_escape_string($dbconn, $_POST["email"]);
$user_password = mysqli_real_escape_string($dbconn, $_POST["password"]);
$query = mysqli_query($dbconn, "SELECT * FROM telemed_users WHERE user_email = '$user_email'");

if(mysqli_num_rows($query)>0){
    $record = mysqli_fetch_assoc($query);
    $hash = $record["user_passhash"];

    if(password_verify($user_password, $hash)){
        $_SESSION["current_user"] = $record["user_id"];
    }
}

if(isset($_SESSION["current_user"])){
// dostęp po zalogowaniu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$devID = $_POST['device_ID'];
	if (!empty($devID)) {
		$query = mysqli_query($dbconn, "INSERT INTO telemed_devices VALUES ('$devID', '$userID')");
	}
}

$userID = $_SESSION["current_user"];
$query = mysqli_query($dbconn, "SELECT device_ID FROM telemed_devices WHERE user_ID = $userID");

$opcje = [];
while ($row = mysqli_fetch_assoc($query)) {
    $opcje[] = $row["device_ID"];
}

?>

<div class="container_1">
    <div class="pick_device">
        <h3>Wybierz urządzenie</h3><br>
        <form method="POST" action="wykres.php">
            <div class="dtlist">
            <input list="devices" name="devices" placeholder="Wybierz z listy" style="width: 190px;">
            <datalist id="devices">
            <?php foreach($opcje as $opcja){
            echo '<option value="'.$opcja.'">';
            }?>
            </datalist>
            </div>
            <input type="submit" name="choose" value="Wykres">
        </form>
    </div>
</div>

<div class="container_2">
    <div class="add_device">
        <br><h4>Dodaj urządzenie</h4><br>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                <input type="text" name="device_ID" placeholder="ID urządzenia">
                <input type="submit" name="submit" value="Zatwierdź"><br>
        </form>
    </div>
</div>

<br>

<div class="container_3">
    <form method="post" action="logowanie.php">
    <input type="submit" name="wyloguj" value="Wyloguj"><br>
    </form>
</div>
<?php
} else{?>
    <div class="container_1">
    <h4>
    <?php echo "Użytkownik nie jest zalogowany<br><br>";?>
    </h4>
    <form action="logowanie.php">
    <input type="submit" name="loguj" value="Logowanie"><br>
    </form>
    </div>
<?php } ?>

</body>
</html>