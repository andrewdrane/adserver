<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Redirect an ad click to the appropritate URL
 * Track the click
 * 
 */

//if we have the ad id, get the user to the correct location.
if ( !empty( $_GET['ad_id'] ) && $_GET['ad_id'] == 'house') {
    $ad['url'] = 'https://github.com/andrewdrane/adserver'; //go to the house site. There is no house_ad in the database
} elseif( !empty( $_GET['ad_id'] ) ) {
    require_once 'includes/database.php';

    $db = new DB();

    $ad_result = $db->query( 'SELECT id, url FROM advertisements WHERE id = ' . (int)$_GET['ad_id'] . ' LIMIT 0,1;' );

    $ad = $ad_result->fetch_assoc();
    
    //track an ad click
    if( !empty( $ad['url'] ) ) {
        $ad_id = $ad['id'];
        //see if we have the referrer to track where the ad came from
        $source_url = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '0';
        //hopefully we will also have the IP address
        $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0';
        
        $db->query("INSERT INTO clicks (ad_id, source_url, ip_address, created) VALUES ('{$ad_id}','{$source_url}','{$ip_address}',NOW() );");
    }
    
} 

//if we have data to redirect, then do so
if( !empty( $ad['url'] ) ) {
    header( 'Location: ' . $ad['url'] );
} elseif( !empty( $_SERVER['HTTP_REFERER'] ) ) {
    //if we don't have an ad id, at least send them back
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    //this would be bad.
    echo "sorry... something went wrong";
}