<?php

$country = $UTCtime = "";

$sql_insert = "INSERT INTO times VALUES (?,?)";
$sql_delete = "";



?>




<!DOCTYPE html>
<html>
    <head>
        <title>TimeZone Converter</title>
    </head>
    <body>
        
        <p> doing stuff with database</p>
        
        <p> ============================= </p>
        
        <form action = "add">
            <p> add new time zone </p>
            <input type="text" name="time" class="input" placeholder="location">
            <input type="text" name="time" class="input" placeholder="&plusmn;00:00 (UTC time)">
            <input type="submit" name="submit_add" class="btn" value ="add time zone">
        </form>
        
        <form action = "delete">
            <p> delete time zone </p>
             <label for="timeZone">
                            <select class="input" name ="timeZone">
                             <option selected disabled hidden>- choose time zone -</option>
<!--
                                <?php foreach ($timezone as $row): ?>
                                    <option><?=$row["name"]?></option>
                                <?php endforeach ?>
-->
                            </select>
            </label>
            <input type="submit" name="submit_delete" class="btn" value ="delete time zone">
        </form>
        
<!--        DISPLAY DATABASE-->
        <p>Time Zone Database</p>
        <table border="1">
            <tr><th>Location</th><th>UTC time</th></tr>

            <?php

            $db_host   = '192.168.2.22';
            $db_name   = 'timezone';
            $db_user   = 'clouduser';
            $db_passwd = 'insecure_db_pw';

            $pdo_dsn = "mysql:host=$db_host;dbname=$db_name";

            $pdo = new PDO($pdo_dsn, $db_user, $db_passwd);

            $q = $pdo->query("SELECT * FROM times");

            while($row = $q->fetch()){
              echo "<tr><td>".$row["name"]."</td><td>".$row["timeDifference"]."</td></tr>\n";
            }

            ?>
        </table>

        
    </body>
</html>