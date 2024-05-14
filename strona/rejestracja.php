<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="main.css">
        <title>Rejestracja</title>
    </head>
<body>
<div class="container_1">
    <div class="add_device">
    <form method="POST" action="rejestrator.php">
        <input type="email" name="email" placeholder="E-mail"><br>
        <input type="password" name="password" placeholder="Hasło"><br>
        <input type="submit" name="submit" value="Zatwierdź"><br>
    </form>
    </div>
</div>
<div class="container_2">
    <form action="logowanie.php">
        <input type="submit" name="loguj" value="Wróć do logowania"><br>
    </form>
</div>
</body>
</html>