<?php  //connecting to database

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

        //Inserting data to database
        if(isset($_POST["submit_add"])){ 
            $location = check_input($_POST["location"]);
            $UTCtime = check_input($_POST["time"]);
            if(empty($location) || empty($UTCtime)){ //empty input
                $insertionErr = "*Empty input";
            }else if(!preg_match("/^([+-]?)(?:1[012]|0?[0-9]):[0-5][0-9](:[0-5][0-9])?$/", $UTCtime)){
                $insertionErr = "*Invalid time input";
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
        
        //Delete data from database
        if(isset($_POST["submit_delete"])){ 
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
    <link rel="stylesheet" href ="index.css">
    
    <body>
        
        <div id="header"> Time Zone Converter Database </div>
        
<!--        Insert new timezone-->
        <div id ="db">
            
        <div id="insert">
        <form method ="post"  action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p>Add Time Zone</p>
            <input type="text" name="location" class="input" placeholder="Location" >
            <input type="text" name="time" class="input" placeholder="&plusmn;00:00 (UTC time)" >
            <input type="submit" name="submit_add" class="btn" value ="ADD">
            <span class="error"><?php echo $insertionErr;?></span>
        </form>
        </div>
        
<!--        Delete timezone-->
        <div id="delete">
        <form method = "post"  action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <p>Delete Time Zone</p>
             <label for="timeZone">
                            <select class="list" name ="timeZone">
                             <option selected disabled hidden>- choose time zone -</option>
                                <?php foreach ($timezone as $row): ?>
                                    <option><?=$row["name"]?></option>
                                <?php endforeach ?>

                            </select>
            </label>
            <input type="submit" name="submit_delete" class="btn" value ="DELETE">
        </form>
        </div>
            
        </div>
        
<!--        DISPLAY DATABASE-->
            <table border="1">
                <tr><th>Locations</th><th>UTC time</th></tr>
                <?php
                $q = $pdo->query("SELECT * FROM times");
                while($row = $q->fetch()){
                  echo "<tr><td>".$row["name"]."</td><td>".$row["timeDifference"]."</td></tr>\n";
                }

                ?>
            </table>
        
    </body>
</html>
