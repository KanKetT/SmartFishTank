<?php

$filepath = realpath (dirname(__FILE__));
require_once($filepath."/db_connect.php");

$db = new DB_CONNECT();
// From Tank table
$result = mysql_query("SELECT MAX(id) as id,Temp,TDS FROM Tank");
$result = mysql_fetch_array($result);

$last = mysql_query("SELECT * FROM Tank WHERE id='$result[id]'");
$last = mysql_fetch_array($last);

$tank = array();
$tank["id"] = $last["id"];
$tank["Temp"] = $last["Temp"];
$tank["TDS"] = $last["TDS"];

//$tempnow = $last['Temp'];

echo json_encode($tank);
?>