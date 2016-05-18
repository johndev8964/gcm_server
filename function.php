<?php
 
   //Storing new user and returns user details
    
   function storeUser($type, $gcm_regid) {
        
        // insert user into database
        $result = mysql_query(
                      "INSERT INTO gcm_users
                            (type, gcm_regid, created_at) 
                            VALUES
                            ($type,
                             '$gcm_regid',
                             NOW())");
         
        // check for successful store
        if ($result) {
             
            // get user details
            $id = mysql_insert_id(); // last inserted id
            $result = mysql_query(
                               "SELECT * 
                                     FROM gcm_users 
                                     WHERE id = $id") or die(mysql_error());
            // return user details 
            if (mysql_num_rows($result) > 0) { 
                return true;
            } else {
                return false;
            }
             
        } else {
            return false;
        }
  }
  
  //Updating user categories
  
  function updateCategories($categories, $gcm_regid) {
      // update categories of user
      $result = mysql_query("UPDATE gcm_users set categories = '$categories' where gcm_regid = '$gcm_regid'");
      
      //check for successfull store
      if ($result) {
          return true;
      }
      else {
          return false;
      }
  }
 
 
    // Getting all registered users
  function getAllUsers() {
        $result = mysql_query("select * FROM gcm_users");
        $regids = array();
        $i = 0;
        while ($row = mysql_fetch_array($result)){
            $regids[$i] = $row["gcm_regid"];
            $i ++;
        }
        return $regids;
  }
 
    // Getting all registered Android users
  function getAndroidUsers($category) {
        $result = mysql_query("select * FROM gcm_users");
        $regids = array();
        $i = 0;
        while ($row = mysql_fetch_array($result)){
            $my_categories = str_replace($row["categories"], "", " ");
            $my_category = str_replace($category, "", "+");
            if($row["type"] == 1 && strpos($my_categories, $my_category) !== false) {
                $regids[$i] = $row["gcm_regid"];
                $i ++;
            }
        }
        return $regids;
  }
  
   // Getting all registered Android users
  function getIOSUsers($category) {
        $result = mysql_query("select * FROM gcm_users");
        $regids = array();
        $i = 0;
        while ($row = mysql_fetch_array($result)){
            $my_categories = str_replace($row["categories"], "", " ");
            $my_category = str_replace($category, "", "+");
            
            if($row["type"] == 2 && strpos($my_categories, $my_category) !== false) {
                $regids[$i] = $row["gcm_regid"];
                $i ++;
            }
        }
        return $regids;
  }
     
    //Sending Push Notification
   function send_push_notification_android($registatoin_ids, $message) {
         
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
 
        //$headers = array(
//            'Authorization: key=' . GOOGLE_API_KEY,
//            'Content-Type: application/json'
//        );
        
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        //print_r($headers);
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        echo $result;
    }
    
    // Sends Push notification for iOS users
    function send_push_notification_ios($category, $title, $devicetoken) {
        $deviceToken = $devicetoken;
        
        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'Pilot_Production.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', 'eksehd602');
        // Open a connection to the APNS server
        //$fp = stream_socket_client(
//            'ssl://gateway.sandbox.push.apple.com:2195', $err,
//            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        $fp = stream_socket_client( 'tls://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $category,
                'body' => $title,
             ),
            'sound' => 'notification_sound.wav'
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        
        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }
?>
