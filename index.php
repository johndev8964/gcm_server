<?php
echo "OK"; 

require_once('loader.php');

 
// GCM Registration ID got from device
$gcmRegID  = $_REQUEST["regId"];
 
 
/**
 * Registering a user device in database
 * Store reg id in users table
 */
if (isset($gcmRegID)) {
     
    // Store user details in db
    $res = storeUser($gcmRegID);
    echo $res;
}

$category = $_REQUEST["category"];
$title    = $_REQUEST["message"];
$registatoin_ids = getAllUsers();

if(count($registatoin_ids) && isset($category) && isset($title)) {
    $message = array
   (
    'message'  => $title,
    'title'  => 'I Know the Pilot',
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
