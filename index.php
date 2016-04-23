<?php
require_once('loader.php');

 
// GCM Registration ID got from device
$gcmRegID  = $_REQUEST["regId"];
$registatoin_ids = array(getAllUsers());  
 
/**
 * Registering a user device in database
 * Store reg id in users table
 */
if (isset($gcmRegID) && !in_array($gcmRegID, $registatoin_ids)) {
     
    // Store user details in db
    $res = storeUser($gcmRegID);
    echo $res;
}

$category = $_REQUEST["category"];
$title    = $_REQUEST["message"];

if(count($registatoin_ids) && isset($category) && isset($title)) {
    $message = array
   (
    'message'  => $title,
    'title'  => $category,
    'subtitle' => $category,
    'tickerText' => 'I Know the Pilot',
    'vibrate' => 1,
    'sound'  => 1,
    'largeIcon' => 'large_icon',
    'smallIcon' => 'small_icon'
   );
   
 
    $result = send_push_notification($registatoin_ids, $message);
 
    echo $result;
}
?>
