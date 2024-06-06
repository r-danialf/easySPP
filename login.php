<?php
    session_start();

    require "./sys/dbconnect.php";
?> 

<?php
    $username = $password = "";

    if (isset($_COOKIE["usercode"])) {
        list($username, $password) = explode(":", $_COOKIE["usercode"]);

        if ( check_user_tu($username, $password) ) {
            header("Location: ./dashboard/spp.php");
        }
    }

    $_SESSION["username"] = "";
    $_SESSION["password"] = "";
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>easySPP</title>
</head>
<body>
    <h2>SPP CONTROL PANEL TEST PAGE</h2> <hr>
    <b>TEST TU LOGIN</b>

    <form action="/easySPP/dashboard/spp.php" method="post">
        USERNAME: <input type="text" name="username" value="<?php echo $_SESSION["username"]; ?>"> <br>
        PASSWORD: <input type="text" name="password" value="<?php echo $_SESSION["password"]; ?>"> <br>
        <input type="submit" name="submit" value="Log-in">
    </form>
</body>
</html>