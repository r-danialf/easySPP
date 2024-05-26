<?php
    session_start();
    require "./sys/dbconnect.php";

    define("a_month", 86400*30);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION["username"] = test_input($_POST["username"]);
        $_SESSION["password"] = test_input($_POST["password"]);

        if (check_user_tu($_SESSION["username"], $_SESSION["password"])) {
            setcookie("usercode", $_SESSION["username"].":".$_SESSION["password"], time() + a_month, "/");
        } else {
            header("Location: ./login.php");
        }
    }

    if (isset($_COOKIE["usercode"])) {
        list($_SESSION["username"], $_SESSION["password"]) = explode(":", $_COOKIE["usercode"]);

        if ( !check_user_tu($_SESSION["username"], $_SESSION["password"]) ) {
            setcookie("usercode", "", time() - 3600, "/");
            header("Location: ./login.php");
        }
    } else {
        header("Location: ./login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>easySPP Dashboard</title>

    <style>
        h2 {
            text-align: center;
        }
        .navbar {
            list-style-type: none;
            overflow: hidden;
            
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: 100px repeat(auto-fit, minmax(80px, 1fr));
            grid-gap: 10px;
        }

        .navbar li {
            text-align: center;
            background-color: #fff;
            padding: 10px;
        }.navbar li:hover {
            background-color: #dfd;
            cursor: pointer;
        }

        .navbar .chosen {
            background-color: #0a6;
        }.navbar .chosen:hover {
            background-color: #0a6;
        }

        .margin-nav {
            padding: 10px;
        }

        table { 
            border-collapse: collapse; 
            width: 100%; 
        }

        th, td {
            border: 1px solid #224; 
            padding: 0.8em; 
            text-align: left;
        }

    </style>
</head>
<body>
    <?php
        $spp_jurusan = ['rpl', 'gp', 'ph', 'tb', 'atr', 'tp'];
        $spp_kelas   = ['x', 'xi', 'xii', 'xiii'];
        $spp_bagian  = ['1', '2', '3', '4'];

        $inavailability = [
            'rpl' => ['xiii'],
            'gp'  => ['2', '3', '4'],
            'ph'  => ['3', '4', 'xiii'],
            'tb'  => ['3', '4', 'xiii'],
            'atr' => ['3', '4', 'xiii'],
            'tp'  => ['3', '4', 'xiii']
        ];

        if (!isset($_SESSION['chosen'])) {
            $_SESSION['chosen'] = [
                'jurusan' => 'rpl',
                'kelas'   => 'x',
                'bagian'  => '1'
            ];
        } 
        
        function test_kelas() {
            global $inavailability;
            if (in_array($_SESSION['chosen']['kelas'], $inavailability[$_SESSION['chosen']['jurusan']])) {
                $_SESSION['chosen']['kelas'] = "x";
            }
        }

        function test_bagian() {
            global $inavailability;
            if (in_array($_SESSION['chosen']['bagian'], $inavailability[$_SESSION['chosen']['jurusan']])) {
                $_SESSION['chosen']['bagian'] = "1";
            }
        }
    ?>

    <h2>easySPP DASHBOARD CENTER</h2>
    
    <?php echo "USER: ".$_SESSION["username"]; ?> ||
    <?php echo "PASS: ".$_SESSION["password"]; ?> ||
    <a onclick="dbAccess()">DATABASE</a> || 
    <a href="./logoff.php">LOG OFF</a>

    <hr>
    <ul class="navbar spp_jurusan">
        <?php
            echo "<b class=\"margin-nav\">JURUSAN:</b>";
            foreach ($spp_jurusan as $jurusan) {
                test_kelas(); test_bagian();
                $chosen = ($jurusan === $_SESSION['chosen']['jurusan']);
                $status = $chosen ? 'chosen' : '';
                echo "<li onclick=\"set_chosen('$jurusan','".$_SESSION['chosen']['kelas']."','".$_SESSION['chosen']['bagian']."','$jurusan');\" class=\"$status\">".strtoupper($jurusan)."</li>";
            }
        ?>
    </ul>

    <hr>
    <ul class="navbar spp_kelas">
        <?php
            echo "<b class=\"margin-nav\">KELAS:</b>";
            foreach ($spp_kelas as $kelas) {
                $unavailable = in_array($kelas, $inavailability[$_SESSION['chosen']['jurusan']]);
                $chosen = ($kelas === $_SESSION['chosen']['kelas']);
                $status = $chosen ? 'chosen' : '';
                if (!$unavailable) {
                    echo "<li onclick=\"set_chosen('".$_SESSION['chosen']['jurusan']."','$kelas','".$_SESSION['chosen']['bagian']."')\" class=\"$status\">".strtoupper($kelas)."</li>";
                }
            }
        ?>
    </ul>

    <hr>
    <ul class="navbar spp_bagian">
        <?php
            echo "<b class=\"margin-nav\">BAGIAN:</b>";
            foreach ($spp_bagian as $bagian) {
                $unavailable = in_array($bagian, $inavailability[$_SESSION['chosen']['jurusan']]);
                $chosen = ($bagian === $_SESSION['chosen']['bagian']);
                $status = $chosen ? 'chosen' : '';
                if (!$unavailable) {
                    echo "<li onclick=\"set_chosen('".$_SESSION['chosen']['jurusan']."','".$_SESSION['chosen']['kelas']."','$bagian')\" class=\"$status\">$bagian</li>";
                }
            }
        ?>
    </ul>

    <hr>

    <table>
        <?php
            if (!isset($_SESSION['chosen'])) { set_chosen('RPL', 'X', '1'); }
            list($jurusan, $kelas, $bagian) = array_values($_SESSION['chosen']);
            show_spp_table($jurusan, $kelas, $bagian);
        ?>
    </table>


    <script>
        function dbAccess() {
            const pass = window.prompt("Enter Database Password:");

            if (pass !== null && pass === "<?php echo $server_password; ?>") {
                window.location.href = 'database.php';
            }
        }

        function set_chosen(jurusan, kelas, bagian) {
            const xhr = new XMLHttpRequest();
            const url = 'sys/action.php?action=set_chosen&jurusan='+jurusan+'&kelas='+kelas+'&bagian='+bagian;
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
