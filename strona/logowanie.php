<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="main.css">
        <title>Logowanie</title>
    </head>
<body>
<?php
session_start();
if($_POST["wyloguj"] == "Wyloguj"){
session_unset();
session_destroy();
}

session_start();
?>
    <div class="container_1">
        <div class="add_device">
        <form method="POST" action="main.php">
            <input type="email" name="email" placeholder="E-mail"><br>
            <input type="password" name="password" placeholder="Hasło"><br>
            <input type="submit" name="submit" value="Zaloguj"><br>
        </form>
        </div>
    </div>

    <div class="container_2">
    <form action="rejestracja.php">
        <input type="submit" name="rejestruj" value="Zarejestruj się"><br>
    </form>
    </div>
</body>
</html>