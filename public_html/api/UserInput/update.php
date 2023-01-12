<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//Creating Array for JSON response
$response = array();

function from_R_pi(){
    $response = array();
    
    if (isset($_GET['Feed'])){
    $feed = $_GET['Feed'];
    
    $filepath = realpath (dirname(__FILE__));
	require_once($filepath."/db_connect.php");
	$db = new DB_CONNECT();
	$result = mysql_query("UPDATE UserInput SET Feed= '$feed' WHERE id=1");
	
	if ($result){
	    $response["success"] = 1;
        $response["message"] = "UserInput Data successfully updated.";
        echo json_encode($response);
	}
    } else {
        // If required parameter is missing
        $response["success"] = 0;
        $response["message"] = "Parameter(s) are missing. Please check the request";
     
        // Show JSON response
        echo json_encode($response);
    }
    
}

function from_web()
{
    $response = array();
        // Check if we got the field from the user
    if (isset($_GET['Feeder']) && isset($_GET['Temp'])) {
        
        $time = $_GET['Feeder'];
        $temp = $_GET['Temp'];
        
     
        // Include data base connect class
    	$filepath = realpath (dirname(__FILE__));
    	require_once($filepath."/db_connect.php");
    
    	// Connecting to database
        $db = new DB_CONNECT();
     
    	// Fire SQL query to update weather data by id
        $result = mysql_query("UPDATE UserInput SET Temp= '$temp', Feeder= '$time' WHERE id=1");
     
        // Check for succesfull execution of query and no results found
        if ($result) {
            // successfully updation of temp (temperature)
            $response["success"] = 1;
            $response["message"] = "UserInput Data successfully updated.";
     
            // Show JSON response
            echo json_encode($response);
        } else {
     
        }
    } else {
        // If required parameter is missing
        $response["success"] = 0;
        $response["message"] = "Parameter(s) are missing. Please check the request";
     
        // Show JSON response
        echo json_encode($response);
    }
    
}

if (isset($_GET['Feeder']) && isset($_GET['Temp'])){
    from_web();
}

if (isset($_GET['Feed'])){
    from_R_pi();
}
 
?>