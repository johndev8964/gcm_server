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
if (isset($gcmRegID) && !in_array($gcmRegID, $registatoin_ids) && isset($type)) {
     
    // Store user details in db
    $res = storeUser($type ,$gcmRegID);
    echo $res;
}

/**
* Add Categories of users.
* 
* @var mixed
*/
$my_categories = $_REQUEST["my_categories"];
if (isset($gcmRegID) && isset($my_categories)) {
    
    //Store or Update user categories
    $res = updateCategories($my_categories, $gcmRegID);
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
   
   $result = send_push_notification_android(getAndroidUsers($category), $message);
   echo $result;
   
   $iOSUsers = getIOSUsers($category);
   foreach($iOSUsers as $deviceToken) {
       if(isset($deviceToken)) {
           send_push_notification_ios($category, $title, $deviceToken);
       }
   }
}
?>
