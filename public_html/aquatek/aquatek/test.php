<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<link rel="stylesheet" href="cs.css">-->
        <script src="java.js"></script>
        <title>AQUATEK</title>
        <link rel = "stylesheet" type = "text/css" href = "dec.css"> 

        <script type="text/javascript">
        function Buttontoggle()
        {
          var t = document.getElementById("Camera");
          if(t.value=="OFF"){
              t.value="ON";
          }
          else if(t.value=="ON"){
              t.value="OFF";}
        }
        
        function bubble(){
            var animateButton = function(e) {
    
            e.preventDefault;
            //reset animation
            e.target.classList.remove('animate');
              
            e.target.classList.add('animate');
            setTimeout(function(){
            e.target.classList.remove('animate');
            },700);
            };
            
            var bubblyButtons = document.getElementsByClassName("bubbly-button");
            
            for (var i = 0; i < bubblyButtons.length; i++) {
              bubblyButtons[i].addEventListener('click', animateButton, false);
            }
        }
        
        </script>
    </head>
    <body>
        <form action="connect.html">
        <button onclick="connect.html" class="button-82-pushable" role="button">
            <span class="button-82-shadow"></span>
            <span class="button-82-edge"></span>
            <span class="button-82-front text">
                START
            </span>
        </button>
    </form>
     <img class="img" src="/pic/Screenshot_2022-10-14_163103_preview_rev_1_1.png" alt="AQUATEK">

    <h1>This is a Heading</h1>
    <p>This is a paragraph.</p>
    <iframe name="dummyframe" id="dummyframe" style="display: none"></iframe>

    <form action="/api/UserInput/update.php" method="get" target="dummyframe">
  Camera:
  <button name="Camera" id="Camera" type="submit" value="on">ON</button>
  <button name="Camera" id="Camera" type="submit" value="off">OFF</button>
</form>

<form action="/api/UserInput/update.php" method="get" target="dummyframe">
    <button class="bubbly-button" onclick="bubble()" id="Feed" name="Feed" value="off">Feed</button>
</form>

<form action="/api/UserInput/update.php" method="get">
    <input type="time" id="Feeder" name="Feeder"/>
    <input type="submit"/>
</form>

<!--<form action="/api/UserInput/update.php" target="dummyframe" method="GET">-->
<!--  <label for="Feeder">Select a time:</label>-->
<!--  <input type="time" id="Feeder" name="Feeder">-->
<!--  <input type="submit">-->
<!--</form>-->



<?php

$response = array();
 
// Include data base connect class
$filepath = realpath (dirname(__FILE__));
require_once($filepath."/db_connect.php");

 // Connecting to database
$db = new DB_CONNECT();

$result = mysql_query("SELECT MAX(id) as id,Temp,TDS FROM Tank");
$result = mysql_fetch_array($result);

$last = mysql_query("SELECT * FROM Tank WHERE id='$result[id]'");
$last = mysql_fetch_array($last);

echo "<br><h1>Temp</h1> =".$last['Temp'];
echo "<br><h1>ID</h1> = ".$last['id'];
echo "<br><h1>tDS</h1> = ".$last['TDS'];

?>

    </body>
</html>