<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Track an impression. This is called asynchronosly via. exec() command 
 *  by the ad serving page.
 * Also decrements the ad_serve record
 * 
 * Expects 4 parameters to be passed in via exec: ad_serve_id, ad_id, source_url, ip_address
 */

require_once 'includes/database.php';

$db = new DB();


//carefully cleanse the values to insert
$ad_serve_id = ( $_SERVER['argv'][1] != '0' )
    ? (int)$_SERVER['argv'][1] 
    : null;

$ad_id = ( $_SERVER['argv'][2] != '0' )
    ? (int)$_SERVER['argv'][2] 
    : null;

$source_url = ( $_SERVER['argv'][3] != '0' )
    ? $db->mysqli->real_escape_string( $_SERVER['argv'][3] ) 
    : null;

$ip_address = ( $_SERVER['argv'][4] != '0' )
    ? $db->mysqli->real_escape_string( $_SERVER['argv'][4] ) 
    : null;

//count the transaction
$db->query("INSERT INTO impressions (ad_id, source_url, ip_address, created) VALUES ('{$ad_id}','{$source_url}','{$ip_address}',NOW() );");

//decrement the appropriate lookup table. Make sure pending does not drop below 0
if( $ad_serve_id ) {
    $db->query("UPDATE ad_serve SET pending = pending - 1 WHERE id = {$ad_serve_id} AND pending > 0 LIMIT 1;");
}
