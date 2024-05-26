<?php
    $dbkeys = array('nisn','nama','kelas','jurusan','bagian','absen','alamat','no_telp');
    $enum = array(
        'kelas' => array('X', 'XI', 'XII', 'XIII'),
        'jurusan' => array('RPL', 'GP', 'PH', 'TB', 'ATR', 'TP'),
        'bagian' => array('1', '2', '3', '4')
    );
    require_once "../sys/dbconnect.php";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        foreach ($dbkeys as $key) {
            $$key = isset($_POST[$key]) ? test_input($_POST[$key]) : "";
        } $_nisn = test_input($_POST["_nisn"]);

        switch ($_POST["act"]) {
            case 'CREATE': $create = array();
                foreach ($dbkeys as $key) { $create[] = $$key; }
                insert_data("siswa", $create);
                break;
            case 'UPDATE': $update = array();
                foreach ($dbkeys as $key) { $update["$key"] = $$key; }
                update_data("siswa", $update, array('nisn' => $_nisn));
                break;
            case 'DELETE': delete_data("siswa", array('nisn' => $_nisn));
                break;
            default: break;
        }
    }
?>

<!DOCTYPE html>

<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #224; padding: 0.8em; text-align: left;}
    ul, li { display: grid; }
    ul { padding: 0; grid-template-columns: 1fr 1fr 1fr;}
    li { padding: 0.2em 0.5em; grid-template-columns: 0.5fr 1fr;}
</style>

<ul>
    <?php 
        foreach ($dbkeys as $k) {
            if (in_array($k, array_keys($enum))) {
                $a = ($k === "jurusan") ? " onchange=\"adjustClass()\" " : "";
                echo "<li>$k: <select name=\"$k\"$a>";
                foreach($enum["$k"] as $v) {
                    echo "<option value=\"$v\">$v</option>";
                } echo "</select></li>";
            } else {
                echo "<li>$k: <input type=\"text\" name=\"$k\"></li>";
            }
            
        }
    ?>
</ul>
    
<input type="submit" value="CREATE" onclick="setData();">
<input type="submit" value="UPDATE" onclick="updateData();">
<input type="submit" value="DELETE" onclick="deleteData();">

<hr>

<table>
    <?php
        show_table("siswa", "writeForm");
    ?>
</table>

<script>
    let selected = null;
    let url = "db_siswa.php"

    <?php foreach ($dbkeys as $k) {
            echo "const $k = document.getElementsByName(\"$k\")[0]; ";
        } 
    ?>

    const table = document.getElementsByTagName("table")[0];

    function writeForm(id) {
        selected = id;

        const allrows = [...table.getElementsByTagName("tr")];

        const row = document.getElementById(id);
        const cells = row.getElementsByTagName("td");

        <?php $i = 0;
            foreach( $dbkeys as $k ) {
                echo "$k.value = cells[$i].innerText; ";
                $i+=1; } 
        ?>
        
        allrows.forEach(e => {e.style.backgroundColor = "#fff";});
        row.style.backgroundColor = "#0aa";
    }

    function adjustClass() {
        const kel = ['X', 'XI', 'XII', 'XIII'];
        const bag = ['1', '2', '3', '4'];

        const prohibition = {
            "RPL": ['XIII'],
            "GP": ['2', '3', '4'],
            "PH": ['3','4','XIII'],
            "TB": ['3','4','XIII'],
            "ATR": ['3','4','XIII'],
            "TP": ['3','4','XIII'],
        };

        bagian.innerHTML = "";
        kelas.innerHTML = "";

        for (const b in bag) {
            if(!(prohibition[jurusan.value].includes(bag[b]))) {
                bagian.innerHTML += `<option value="${bag[b]}">${bag[b]}</option>`;
            }
        }

        for (const k in kel) {
            if(!(prohibition[jurusan.value].includes(kel[k]))) {
                kelas.innerHTML += `<option value="${kel[k]}">${kel[k]}</option>`;
            }
        }
    }

    function setData() {
        const xhr = new XMLHttpRequest();
        <?php
            $query = array();
            foreach ($dbkeys as $k) {
                $query[] = "$k=\${"."$k.value}";
            } $query = implode("&", $query);
            echo "const par = `$query&act=CREATE`;";
        ?>
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(this.responseText);
                location.reload();
            }
        };
        xhr.send(par);
    }

    function updateData() {
        const row = document.getElementById(selected);
        const cells = row.getElementsByTagName("td");

        const xhr = new XMLHttpRequest();
        <?php
            $query = array();
            foreach ($dbkeys as $k) {
                $query[] = "$k=\${"."$k.value}";
            } $query = implode("&", $query);
            echo "const par = `$query&act=UPDATE&_nisn=\${cells[0].innerText}`; ";
        ?>
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(this.responseText);
                location.reload();
            }
        };
        xhr.send(par);
    }

    function deleteData() {
        const row = document.getElementById(selected);
        const cells = row.getElementsByTagName("td");

        const xhr = new XMLHttpRequest();
        const par = `_nisn=${cells[0].innerText}&act=DELETE`;
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(this.responseText);
                location.reload();
            }
        };
        xhr.send(par);
    }

    adjustClass();
</script>
