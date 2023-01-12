<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<meta http-equiv="refresh" content="5">-->
    <title>Input Stepper</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@600&display=swap" rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <script>
        function    subb(){
            document.getElementById("sub").submit();
        }
        
        function    feed(){
            document.getElementById("feed").submit();
        }
        function    table(){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function(){
                const obj = JSON.parse(this.responseText);
                document.getElementById("temp").innerHTML = obj.Temp;
                document.getElementById("TDS").innerHTML = obj.TDS;
            }
            xhttp.open("GET", "temp.php");
            xhttp.send();
        }
        
        setInterval(function(){
            table();
        }, 2000);
    </script>
</head>

<body>
<?php
$filepath = realpath (dirname(__FILE__));
require_once($filepath."/db_connect.php");

$db = new DB_CONNECT();
// From Tank table
$result = mysql_query("SELECT MAX(id) as id,Temp,TDS FROM Tank");
$result = mysql_fetch_array($result);

$last = mysql_query("SELECT * FROM Tank WHERE id='$result[id]'");
$last = mysql_fetch_array($last);

$tempnow = $last['Temp'];
$id = $last['id'];
$TDS = $last['TDS'];

//From UserInput table
$usrTB = mysql_query("SELECT * FROM UserInput WHERE id=1");
$usrTB = mysql_fetch_array($usrTB);

$tempset = $usrTB['Temp'];
$Feeder = $usrTB['Feeder'];
    

?>
    <header class="bg">
        <iframe name="dummyframe" id="dummyframe" style="display: none"></iframe>
        <form id="sub" action="../../api/UserInput/update.php" method='get' target="dummyframe">
            <h2>Fish Tank</h2>
            <h1 id="demo"></h1>
            
            <span class="ph-txt">Temp</span>
            <div class="ph-box"></div>
            <span class="ph-val" id="temp"></span>
            <span class="tds-txt">TDS</span>
            <div class="tds"></div>
            <span class="tds-val" id="TDS"></span>
            <!--<?php echo $TDS?>-->
            <span class="time-txt">Feed Time</span>
            <input type="time" name="Feeder" value="<?php echo $Feeder; ?>" onchange="subb()">
            <span class="time-val"></span>
            <div class="vdo">
                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/0WISDN56HRM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            
            <div class="container">
            <p>Temp controllers</p>
            <input type="range" id="my-slider" name="Temp" min="0" max="40" value="<?php echo $tempset; ?>" oninput="slider()" onmouseup="subb()">
            <div id="slider-value">0</div>
            </div>
        </form>
        <form action="../../api/UserInput/update.php" method="get" target="dummyframe">
            <button type="submit" class="button-49" id="feed" name="Feed" role="button" value="on">FEED NOW</button>
        </form>
        
        <form class="btn-sub">
        <input type="button" value="SUBMIT" class="sub" onclick="save()" />
        </form>
    </header>
    
    <!-- Script -->
    <script src="main.js">
    
    </script>
</body>

</html>