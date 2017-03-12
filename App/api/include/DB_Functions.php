<?php
require 'DB_Connect.php';
$response=array();
class DB_Functions {
    //put your code here
    // constructor
    function __construct() {

        // connecting to database
        
      $this->db = new DB_Connect();
        $this->db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }

    public function getEvents(){
        $getEvents=mysqli_query($GLOBALS['con'],"SELECT * FROM sports WHERE deleted='0' AND is_event='1'");
        $rows= mysqli_num_rows($getEvents);
        if ($getEvents) {
            if($rows>0){
            // return user data
                $i=0;
                $response['success']= 1;
                while($result=mysqli_fetch_array($getEvents)){
                $response['data'][$i]['uid']=$result['uid'];
                $response['data'][$i]['name']=$result['sport_name'];
                $response['data'][$i]['captain_name']=$result['captain1_name'];
                $response['data'][$i]['contact']=$result['captain1_contact'];
               // $response['data'][$i]['facebookUrl']=$result['facebookUrl'];
                 $i++;
                }
                return json_encode($response);
                }
                else{
                    $response['success']= '1';
                    $response['data']='';
                    return json_encode($response);
                }
            }
            else {
            $response['success']= '0';
            $response['error_msg']= "Could not retrieve details";
            return json_encode($response);
        }
        
    mysqli_close($GLOBALS['con']);
    }

        public function getSchedule($updated_at){
        
        $getSchedule=mysqli_query($GLOBALS['con'],"SELECT * FROM schedule as s JOIN sports WHERE CAST(s.updated_at AS UNSIGNED) > $updated_at AND s.deleted='0'");
        $rows= mysqli_num_rows($getSchedule);
        if ($getSchedule) {
            if($rows>0){
            // return user data
                    $response=array();
                    $i=0;
                    $response['success']= "1";
                    while($result=mysqli_fetch_array($getSchedule)){
                    $response['data'][$i]['uid']=$result['uid'];
                   $response['data'][$i]['time']=$result['time(oid)'];
                   $response['data'][$i]['updated_at']=$result['updated_at'];
                   //$response['data'][$i]['event_venue']=$result['event_venue'];
                   $response['data'][$i]['sport_name']=$result['sport_name'];
                   $response['data'][$i]['event_date']=$result['date'];
                   //$response['data'][$i]['deleted']=$result['deleted'];
                   //$response['data'][$i]['id']=$result['id'];
                     $i++;

                    }
                    return json_encode($response);
                }
                else{
                    $response['success']= '1';
                    $response['data']='';
                    return json_encode($response);
                }

        } else {
            $response['success']= '0';
            $response['error_msg']= "Could not retrieve Schedule";
            return json_encode($response);
        }
    mysqli_close($GLOBALS['con']);
    }



    public function regUser($name,$email,$phone,$college,$events){
        $time = Date('U');
        $emailquery = mysqli_query($GLOBALS['con'],"SELECT * FROM  registrations WHERE email = '$email'");
        $rows = mysqli_num_rows($emailquery);
        if($rows == 0){
            $atmosCount=mysqli_query($GLOBALS['con'],"SELECT count(*) FROM registration_stats");
    $rows=mysqli_fetch_array($atmosCount);
    $count= $rows['count(*)']+201;
    $atmosId= 'ARNH'.$count;
    $date = Date('d-m-Y H:i:s');

             $regUser= mysqli_query($GLOBALS['con'],"INSERT INTO registration_stats(id,name,email,college,phone,events,timestamp) VALUES('$atmosId','$name','$email','$college_name','$phone','$events','$date')");  
            if ($regUser) {
                $url ='http://bits-atmos.org/mail/registered.php';
      $ch = curl_init();  
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST, 1);
      curl_setopt($ch,CURLOPT_POSTFIELDS, array('email' =>$email,'id' => $atmosId,'college' => $college_name,'phone' => $phone,'name' => $name,'events' => $events));   
      $output=curl_exec($ch);
      curl_close($ch);


                // return user data
                $IDquery = mysqli_query($GLOBALS['con'],"SELECT * FROM registration_stats WHERE email = '$email'");
                $result=mysqli_fetch_array($IDquery);
                $response['error']= FALSE;
                $response['name']=$name;
                $response['college']=$college;
                $response['phone']=$phone;
                $response['email']=$email;
                $response['id']=$result['id'];
                $response['events']=$events;
                $response['registration']=1;
                return json_encode($response);
            } else {
                $response['error']= TRUE;
                $response['error_msg']= "Entry to database Failed";
                return json_encode($response);
            }
        }
        else{
            $result = mysqli_fetch_array($emailquery);
            $atmID = $result['id'];
            $eventsOld = $result['events'];
            if($events == ''|| $events == NULL)
                $eventsNew = $events;
            else
                $eventsNew = $eventsOld.','.$events;

            $ypdateQuery = mysqli_query($GLOBALS['con'],"UPDATE registration_stats SET events = '$eventsNew' WHERE id= '$id' ");  
            $response['error']= FALSE;
                $response['name']=$name;
                $response['college']=$college;
                $response['phone']=$phone;
                $response['email']=$email;
                $response['id']=$result['id'];
                $response['events']=$eventsNew;
                $response['registration']=1;
            return json_encode($response);
        }
    mysqli_close($GLOBALS['con']);
    }


    public function getDetails($updated_at){
        
        $getDetails=mysqli_query($GLOBALS['con'],"SELECT * FROM event_data NATURAL JOIN atmos_events WHERE CAST(updated_at AS UNSIGNED) > $updated_at AND deleted =0");
        $rows= mysqli_num_rows($getDetails);
        if ($getDetails) {
            if($rows>0){
            // return user data
                $response=array();
                $i=0;
                $response['error']= FALSE;
                while($result=mysqli_fetch_array($getDetails)){
                $response['data'][$i]['event_id']=$result['event_id'];
               $response['data'][$i]['prize']=$result['prize'];
               $response['data'][$i]['problem_statement']=$result['problem_statement'];
               $response['data'][$i]['description']=$result['description'];
               $response['data'][$i]['image_link']=$result['image_link'];
               $response['data'][$i]['fb_url']=$result['fb_url'];
               $response['data'][$i]['pdf_link']=$result['pdf_link'];
               $response['data'][$i]['updated_at']=$result['updated_at'];
               $response['data'][$i]['type']=$result['type'];
               $response['data'][$i]['tab']=$result['tab'];
               $response['data'][$i]['event_name']=$result['event_name'];
               $response['data'][$i]['contacts'][0]['name']=$result['contact1_name'];
               $response['data'][$i]['contacts'][0]['number']=$result['contact1_number'];
               $response['data'][$i]['contacts'][1]['name']=$result['contact2_name'];
               $response['data'][$i]['contacts'][1]['number']=$result['contact2_number'];
                 $i++;

                }
                return json_encode($response);
            }
            else{
                $response['error']= FALSE;
                        $response['data']='';
                        return json_encode($response);
            }
        } else {
            $response['error']= TRUE;
            $response['error_msg']= "Could not retrieve Schedule";
            return json_encode($response);
        }
    mysqli_close($GLOBALS['con']);
    }

    public function addToken($token){

    $query=mysqli_query($GLOBALS['con'],"SELECT * FROM user_token WHERE token='$token'");
    $rows=mysqli_num_rows($query);
    if($rows==0){
     $result = mysqli_query($GLOBALS['con'],"INSERT INTO user_token(token) VALUES('$token')");
            // check for successful store
        if ($result) {
           $response['error']= FALSE;
            $response['error_msg']= "Token added Successfully";
            return json_encode($response);
        } else {
            //token not added
            $response['error']= FALSE;
            $response['error_msg']= "Token could not be added";
            return json_encode($response);
            }
        }
        else{
            $response['error']= TRUE;
            $response['error_msg']= "Token already exists";
            return json_encode($response);
        }
        mysql_close( $GLOBALS['con']);
    }


    public function getFeed($updated_at){
        $getFeed=mysqli_query($GLOBALS['con'],"SELECT * FROM notifications  WHERE (CAST(updated_at AS UNSIGNED) > $updated_at)  AND is_feed =1 ORDER BY id DESC");
        if ($getFeed) {
            // return user data
            $rows= mysqli_num_rows($getFeed);
            $response=array();
            $i=0;
            if($rows){
                $response['error']= FALSE;
                while($result=mysqli_fetch_array($getFeed)){
                $response['data'][$i]['title']=$result['title'];
                $response['data'][$i]['message']=$result['message'];
                $response['data'][$i]['id']=$result['id'];
                $response['data'][$i]['updated_at']=$result['updated_at'];
                 $i++;
                }
                echo json_encode($response);
                return true;
            }
            else{
                $response['error']= FALSE;
                $response['data']= array();
                echo json_encode($response);
                return true;
            }
        } else {
                 $response['error']= TRUE;
                $response['error_msg']= "Could not get Feeds";
                echo json_encode($response);
                return true;
        }
    mysqli_close($GLOBALS['con']);
    }

    public function gcmCall($title,$message,$arraynew,$type,$event_id,$updated_at){
     include_once 'GCMPushMessage.php';
    $apikey="AIzaSyAU-OosfuL1L5P7nxmK8RUPaIPkant63CQ";

    $gcm = new GCMPushMessage($apikey);
    $registration_ids = $arraynew;
    $message = array("message"=>$message,"title"=>$title,"type"=>$type,"id" => $event_id,"updated_at"=>$updated_at);
    $gcm->setDevices($registration_ids);
    $result = $gcm->send($message); 
       
    $result= json_decode($result, true);
    var_dump($result);
    if($result['success']>0){
        //delete entry          
     }
     else {
         //chat not added to GCM
            return false;
        }     
    mysqli_close( $GLOBALS['con']);
    }

    public function pushNotification($title,$message,$type,$event_id,$updated_at){  
    $type=1;  
        $query=mysqli_query($GLOBALS['con'],"SELECT * FROM user_token");
        $count=0;
        while($array=mysqli_fetch_array($query)){
          $tokenArray[$count]=$array['token'];
          $count++;
        }
        $tokenArray=array_chunk($tokenArray,50);
        $countArrays=count($tokenArray);
        $act=0;
        while($act<$countArrays){
        $arrayChunk=$tokenArray[$act];
        $this->gcmCall($title,$message,$arrayChunk,$type,$event_id,$updated_at);
        $act++;
        }
    }

    public function updateNotification($message,$title,$feed){
        $time=Date('U');
        $addNotification=mysqli_query($GLOBALS['con'],"INSERT INTO notifications(title,message,updated_at,is_feed) VALUES('$title','$message','$time','$feed')");
        if ($addNotification) {
            // return user data
            return true;
        } else {
            return false;
        }
    mysqli_close($GLOBALS['con']);
    }

    // public function pushTypeNotification($title,$message,$type,$updatedTime){
    //     $query=mysqli_query($GLOBALS['con'],"SELECT * FROM user_token");
    //     $count=0;
    //     while($array=mysqli_fetch_array($query)){
    //       $tokenArray[$count]=$array['token'];
    //       $count++;
    //     }
    //     $tokenArray=array_chunk($tokenArray,50);
    //     $countArrays=count($tokenArray);
    //     $act=0;
    //     while($act<$countArrays){
    //     $arrayChunk=$tokenArray[$act];
    //     $this->gcmCall($title,$message,$arrayChunk,$type);
    //     $act++;
    //     }
    // }

}
?>