<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Rejestracja</title>
    </head>
<body>
<?php
$user_email = $user_password = "";

function chwg($dane) {
    $dane = trim($dane);
    $dane = stripslashes($dane);
    $dane = htmlspecialchars($dane);
    return $dane;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(empty($_POST["email"])){
        $mailErr = "Adres e-mail jest wymagany";
    } else {
        $email = chwg($_POST["email"]);
    }
    if(empty($_POST["password"])){
        $passErr = "Musisz podać hasło";
    } else {
        $password = chwg($_POST["password"]);
    }
}

$servername = "mysql.agh.edu.pl";
$username = "mikozak";
$pass = "5i5SvXSpTr4aBUgP";
$dbname = "mikozak";

$dbconn = mysqli_connect($servername, $username, $pass, $dbname);
$user_email = mysqli_real_escape_string($dbconn, $email);
$user_password = mysqli_real_escape_string($dbconn, $password);

$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

echo "<br>".$mailErr."<br>".$passErr;

if (mysqli_query($dbconn, "INSERT INTO telemed_users(user_email, user_passhash) VALUES ('$user_email', '$user_password_hash')")){
        echo "Rejestracja przebiegła poprawnie<br>";?>
        <form action="logowanie.php">
        <input type="submit" name="loguj" value="Logowanie"><br>
        </form>
    <?php
    } else {
        echo "Wystąpił błąd<br>";
        echo(mysqli_error($dbconn));
    }
?>
</body>
</html>