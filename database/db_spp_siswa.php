<?php
    $dbkeys = array('nisn','status','terbayarkan','hiraubayar');
    require_once "../sys/dbconnect.php";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        foreach ($dbkeys as $key) {
            $$key = isset($_POST[$key]) ? test_input($_POST[$key]) : "";
        } $_nisn = test_input($_POST["_nisn"]);
        
        switch ($_POST["act"]) {
            case 'CHECK': check_student(array('nisn' => $nisn));
                break;
            case 'CREATE': $create = array();
                foreach ($dbkeys as $key) { $create[] = $$key; }
                insert_data("spp_siswa", $create);
                break;
            case 'UPDATE': $update = array();
                foreach ($dbkeys as $key) { $update["$key"] = $$key; }
                update_data("spp_siswa", $update, array('nisn' => $_nisn));
                break;
            case 'DELETE': delete_data("spp_siswa", array('nisn' => $_nisn));
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
    <li>nisn: <input type="text" name="nisn" oninput="checkStudent()"></li>
    <li> status:
        <select name="status">
            <option value="BELUM">BELUM</option>
            <option value="LUNAS">LUNAS</option>
        </select>
    </li>
    <li>terbayarkan: <input type="text" name="terbayarkan"></li>
    <li>hiraubayar: <input type="text" name="hiraubayar"></li>
    <li>Terdeteksi: <span id="deteksi" style="color: red">TIDAK TERDETEKSI</span></li>
    <li>Nama: <input type="text" name="nama" disabled></li>
</ul>
    
<input type="submit" value="CREATE" onclick="setData();">
<input type="submit" value="UPDATE" onclick="updateData();">
<input type="submit" value="DELETE" onclick="deleteData();">

<hr>

<table>
    <?php
        show_table("spp_siswa", "writeForm");
    ?>
</table>

<script>
    let selected = null;
    let url = "db_spp_siswa.php"

    <?php foreach ($dbkeys as $k) {
            echo "const $k = document.getElementsByName(\"$k\")[0]; ";
        } 
    ?>

    const nama = document.getElementsByName("nama")[0];
    const deteksi = document.getElementById("deteksi");

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

        checkStudent();
    }

    function checkStudent() {
        nama.value = "";

        console.log(nisn.value.length);
        
        if (nisn.value.length === 10) {
            if (nisn.value !== "" || nisn.value !== null) {
                const xhr = new XMLHttpRequest();
                const par = `nisn=${nisn.value}&act=CHECK`;
                xhr.open('POST', "../sys/action.php", true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200 && this.responseText !== "") {
                        const studentData = JSON.parse(this.responseText);

                        nama.value = studentData.nama;
                        deteksi.innerHTML = "TERDETEKSI";
                        deteksi.style.color = "blue";

                    } else {
                        deteksi.innerHTML = "TIDAK TERDETEKSI";
                        deteksi.style.color = "red";
                    }
                };
                xhr.send(par);
            }    
        } else {
            deteksi.innerHTML = "TIDAK TERDETEKSI";
            deteksi.style.color = "red";
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
</script>