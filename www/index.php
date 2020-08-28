<?php    //connecting to database

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

<?php // validating input
$time = $ampm = $localTime = $anotherTimeZone = "";
$timeErr = $timeZone1Err = $timeZone2Err = "";
$valid_input = false;
$result = "";
$utc1 = $utc2 = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $ampm = check_input($_POST["ampm"]);
    $localTime = check_input($_POST["localTime"]);
    $anotherTimeZone = check_input($_POST["anotherTimeZone"]);
    $valid_input = true;
    //fix plz u-u
    if(empty($_POST["time"])){
        $timeErr = "Time is required";
        $valid_input = false;
    }else{
        $time = check_input($_POST["time"]);
        if(!preg_match("/^(?:1[012]|0[0-9]):[0-5][0-9](:[0-5][0-9])?$/", $time)){
            $timeErr = "invalid time";
            $valid_input = false;
        }
    }
    if(isset($_POST[ “localTime”])){
        $localTime =  $_POST[“localTime”];
    }
    if(isset($_POST[“anotherTimeZone”])){
        $anotherTimeZone =  $_POST[“anotherTimeZone”];
    }
    if(empty($_POST["localTime"])){
        $timeZone1Err = "Time Zone is required";
        $valid_input =false;
    }
    if(empty($_POST["anotherTimeZone"])){
        $timeZone2Err = "Time Zone is required";
        $valid_input = false;
    }
    
    if(valid_input){ //if all input is valid
        
        $sql_search = "SELECT timeDifference FROM times WHERE name = :key";
        $stmt = $stmt = $pdo->prepare($sql_search);
        $stmt->execute([':key' => $localTime]);
        $utc1 = $stmt->fetch();
        $stmt->execute([':key' => $anotherTimeZone]);
        $utc2 = $stmt->fetch();
        
        $timeAfterConversion = convertTime($utc1['timeDifference'],$utc2['timeDifference'],$ampm,$time);
        
        $result = $time." ".$ampm." in ". $localTime . " (" . $utc1['timeDifference'] . ")"
. "<br>converts to<br>" . $timeAfterConversion ." in ". $anotherTimeZone ." (". $utc2['timeDifference'].")" ;
              
    }     
}

function convertTime($timezone1, $timezone2, $ampm, $cur_time){

    $time =  preg_split("/[\:\+\-\s]+/",$cur_time,-1,PREG_SPLIT_NO_EMPTY);
    //  print_r($time);

    $sign1 = $sign2 = 1;
    if(preg_match("/[\-]/",$timezone1)){
        $sign1 = -1;
    }
    $utc1 = preg_split("/[\:\+\-\s]+/",$timezone1,-1,PREG_SPLIT_NO_EMPTY);
    // print_r($utc1);

    if(preg_match("/[\-]/",$timezone2)){
        $sign2 = -1;
    }
    $utc2 = preg_split("/[\:\+\-\s]+/",$timezone2,-1,PREG_SPLIT_NO_EMPTY);
    // print_r($utc2);

    $timeDiff = ($utc2[0]*$sign2) - ($utc1[0]*$sign1);
//     echo "<br># ".($utc2[0]*$sign2) . " - " .($utc1[0]*$sign1)." = ". $timeDiff;
    $minDiff = ($utc2[1]*$sign2) - ($utc1[1]*$sign1);
    // echo "<br>#" . $minDiff;

    $timeAfterConversion = $time[0] + $timeDiff;
    if($timeAfterConversion<0){
      $timeAfterConversion += 24;
//      echo "- ".$timeAfterConversion;
    }
    if($timeAfterConversion > 12){
      $timeAfterConversion = $timeAfterConversion%12;
      $ampm = ($ampm == "AM"?"PM":"AM");         
    }
//     echo "<br>== " . $timeAfterConversion.":".$time[1]." ".$ampm; 
    return $timeAfterConversion.":".$time[1]." ".$ampm;
}

function check_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function check_time($time){
    $time = trim($time);
    if(preg_match("/^(?:1[012]|0[0-9]):[0-5][0-9]$/", $time) == 1){
        return $time;
    }
}


?>


<!DOCTYPE html>
<html>
    <head>
        <title>TimeZone Converter</title>
        <link rel="stylesheet" href ="index.css">
    </head>
    <style>
  .error {color: #FF0000;}
    </style>
    <body>
   
        <section></section>
       
        <div id = "container">
            <h1 id="header">Time Zone Converter</h1>
           
            <div id = "clock_container">

                <form method="post" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<!--                    TIME INPUT-->
                <div class ="clock" id ="clock1">
                        <p>Time:</p>
                         <input type="text" name="time" class="input" placeholder="00:00" value=<?php echo date("h:i a"); ?>>
                          <span class="error"><?php echo $timeErr;?></span>
                        <div class="rbtn">
                              <input type="radio" name="ampm" <?php if (isset($ampm) && $ampm=="AM") echo "checked";?> value="AM" checked> AM
                              <input type="radio" name="ampm" <?php if (isset($ampm) && $ampm=="PM") echo "checked";?> value="PM"> PM
                        </div>
                   
                </div>
               
<!--               TIME ZONE INPUT-->
                <div class ="clock" id="clock2">
                    <p>From:</p>
                    <label for="timeZone1">
                            <select class="input" name = "localTime">
                              <option selected disabled hidden>- choose time zone -</option>
                                <?php foreach ($timezone as $row): ?>
                                    <option><?=$row["name"]?></option>
                                <?php endforeach ?>
                            </select>
                            <span class="error"><?php echo $timeZone1Err;?></span>
                    </label>

                    <p>To:</p>
                    <label for="timeZone2">
                            <select class="input" name ="anotherTimeZone">
                             <option selected disabled hidden>- choose time zone -</option>
                                <?php foreach ($timezone as $row): ?>
                                    <option><?=$row["name"]?></option>
                                <?php endforeach ?>
                            </select>
                        <span class="error"><?php echo $timeZone2Err;?></span>
                    </label>
                </div>

               
                 <div id="result">
                    <?php
                        if($valid_input){
                            echo $result;
                        }                     
                    ?>
                </div>

                <input type="submit" name="submit" class="btn" value ="Convert Time">
                </form>
            </div>
           
        </div>
    </body>
</html>
