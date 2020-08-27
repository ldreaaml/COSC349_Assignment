<?php

$db_host   = '192.168.2.22';
$db_name   = 'timezone';
$db_user   = 'clouduser';
$db_passwd = 'insecure_db_pw';

$pdo_dsn = "mysql:host=$db_host;dbname=$db_name";
$pdo = new PDO($pdo_dsn, $db_user, $db_passwd);

$sql_q = $pdo->prepare("SELECT name FROM  times");
$sql_q->execute();
$timezone = $sql_q->fetchAll();
?>

<?php

    $location = $del_location = $UTCtime = "";
    $locationErr = $UTCtimeErr = $insertionErr = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(isset($_POST["submit_add"])){ //INSERT
            $location = check_input($_POST["location"]);
            $UTCtime = check_input($_POST["time"]);
//            echo "ADD " . $location . " " . $UTCtime . "<br><br>";
            if(empty($location) || empty($UTCtime)){ //empty input
                $insertionErr = "invalid/empty input";
            }else{

                $sql_insert = "INSERT INTO times VALUES (:country , :utc)";
                $stmt = $pdo->prepare($sql_insert);
                $stmt->bindValue(":country",$location);
                $stmt->bindValue(":utc",$UTCtime);

                $inserted = $stmt->execute();

                if(!$inserted){
                    $insertionErr = "insertion failed";
                }
            }
        }
        if(isset($_POST["submit_delete"])){ //DELETE
            $del_location = check_input($_POST["timeZone"]);
            $sql_delete = "DELETE FROM times WHERE name = :key";
            $stmt = $pdo->prepare($sql_delete);
            $deleted = $stmt->execute([':key' => $del_location]);
        }
    }

function check_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>



<!DOCTYPE html>
<html>
    <head>
        <title>TimeZone Converter</title>
    </head>
    <style>
        .error {color: #FF0000;}
    </style>
    <body>
        
        <p> doing stuff with database</p>
        
        <p> ============================= </p>
        
        <form method ="post"  action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p> add new time zone </p>
            <input type="text" name="location" class="input" placeholder="location" value="test">
            <input type="text" name="time" class="input" placeholder="&plusmn;00:00 (UTC time)" value="+12:34">
            <input type="submit" name="submit_add" class="btn" value ="add time zone">
            <span class="error"><?php echo $insertionErr;?></span>
        </form>
        
        <form method = "post"  action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p> delete time zone </p>
             <label for="timeZone">
                            <select class="input" name ="timeZone">
                             <option selected disabled hidden>- choose time zone -</option>
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
                $q = $pdo->query("SELECT * FROM times");
                while($row = $q->fetch()){
                  echo "<tr><td>".$row["name"]."</td><td>".$row["timeDifference"]."</td></tr>\n";
                }

                ?>
            </table>
        
    </body>
</html>
