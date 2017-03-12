<?php
    require 'include/DB_Functions.php';
    $db = new DB_Functions();
    
if (isset($_REQUEST['tag']) && $_REQUEST['tag'] != ''){
    // get tag
    $tag = $_REQUEST['tag'];
    // // include db handler

 
    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
    $response2 =array();
    $k= array();
    
    if ($tag == 'getEvents') {
        // Get all events
        $user = $db->getEvents();
        echo $user;
    }

    else if ($tag == 'getSchedule') {
        if(array_key_exists("updatedAt", $_REQUEST)){
            $updated_at = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['updatedAt']);
            // Get all events
            $user = $db->getSchedule($updated_at);
            echo $user;
        }
        else{
            $response['success']= '0';
            $response['error_msg']= "Updated at parameter missing";
            echo  json_encode($response);
        }
    }

    else if ($tag == 'registerUser') {

        // Registration of user from DOSH Desk
        $name = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['name']);
        $email = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['email']);
        $college = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['college']);
        $phone = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['phone']);  
        $events = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['events']);               
        // Register  user
            $user = $db->regUser($name,$email,$phone,$college,$events);
            echo $user;
        }

     else if ($tag == 'getEventDetails') {
        if(array_key_exists("updated_at", $_REQUEST)){
            $updated_at = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['updated_at']);
            // Get all events
            $user = $db->getDetails($updated_at);
            echo $user;
        }
        else{
            $response['error']= FALSE;
            $response['error_msg']= "Updated at parameter missing";
            echo  json_encode($response);
        }
    }
      else  if ($tag == 'add_token') {
        if(array_key_exists("token", $_REQUEST)){
            $updated_at = mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['token']);
            // Get all events
            $user = $db->addToken($updated_at);
            echo $user;
        }
        else{
            $response['error']= FALSE;
            $response['error_msg']= "Token  missing";
            echo  json_encode($response);
        }
    }
    else if($tag=='get_feed'){
            // display all feeds 
            $updated_at= mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['updated_at']);
            $user = $db->getFeed($updated_at);
            if($user){

            }
            else{
            $response["error"] = TRUE;
            $response["error_msg"] = "This error is impossible";
             echo json_encode($response);
            }
    }


        
    else{
        $response["error"] = TRUE;
    $response["error_msg"] = "No such tag found!";
    echo json_encode($response);
    }
}
 else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter 'tag' is missing!";
    echo json_encode($response);
}
?>