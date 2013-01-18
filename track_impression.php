<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Track an impression. This is called asynchronosly via. exec() command 
 *  by the ad serving page.
 * 
 * Expects 3 parameters to be passed in via exec: ad_id, source_url, ip_address
 */

require_once 'includes/database.php';

$d = new DB();


//carefully cleanse the values to insert
$ad_id = ( $_SERVER['argv'][1] != '0' )
    ? (int)$_SERVER['argv'][1] 
    : 'NULL';

$source_url = ( $_SERVER['argv'][2] != '0' )
    ? mysqli_escape_string( $_SERVER['argv'][2] ) 
    : 'NULL';

$ip_address = ( $_SERVER['argv'][2] != '0' )
    ? mysqli_escape_string( $_SERVER['argv'][3] ) 
    : 'NULL';

$d->query("INSERT INTO impressions (ad_id, source_url, ip_address, created) VALUES ('{$ad_id}','{$source_url}','{$ip_address}',NOW() );");

