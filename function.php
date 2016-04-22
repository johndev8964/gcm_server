<?php
 
   //Storing new user and returns user details
    
   function storeUser($gcm_regid) {
        
        // insert user into database
        $result = mysql_query(
                      "INSERT INTO gcm_users
                            (gcm_regid, created_at) 
                            VALUES
                            ( '$gcm_regid', 
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
 
 
    // Getting all registered users
  function getAllUsers() {
        $result = mysql_query("select gcm_regid FROM gcm_users");
        return $result;
  }
 
     
    //Sending Push Notification
   function send_push_notification($registatoin_ids, $message) {
         
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
 
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
 
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
?>
