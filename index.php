<?php
require_once('loader.php');

 
// GCM Registration ID got from device
$gcmRegID  = $_REQUEST["regId"];
$type      = $_REQUEST["type"];
$registatoin_ids = getAllUsers();  
 
/**
 * Registering a user device in database
 * Store reg id in users table
 */
if (isset($gcmRegID) && !in_array($gcmRegID, $registatoin_ids && isset($type))) {
     
    // Store user details in db
    $res = storeUser($type ,$gcmRegID);
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
   
   $result = send_push_notification_android(getAndroidUsers(), $message);
   echo $result;
   
   $iOSUsers = getIOSUsers();
   foreach($deviceToken as $iOSUsers) {
       if(isset($deviceToken)) {
           send_push_notification_ios($category, $title, $deviceToken);
       }
   }
}
?>
