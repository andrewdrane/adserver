<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Serve an ad to a requesting page.
 *  Get the highest priority ad from ad_serve table in database.
 *  If no ad is available, or connection fails, the fallback is hard coded house ad
 *  Asynchronously track the ad impression
 * 
 * Keep code as simple as possible to minimize execution time.
 */

//$ad['html'];

$ad_html = fallback_ad();

$ad_id = '0'; //house ad
$source_url = isset( $_GET['source_url'] ) ? $_GET['source_url'] : '0';
//just in case we don't have one
$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0';

//do an asynchronous tracking. Escape the command to be safe
exec( escapeshellcmd( "php track_impression.php {$ad_id} {$source_url} {$ip_address} ") . "> /dev/null 2>/dev/null &" );

header("Content-type: text/javascript");

echo 'document.write("' . addslashes( $ad_html ) . '");';


//HELPER FUNCTIONS

/** Return the HTML for the house ad. 
 * Hard coded to maximize chance ad will be served
 */
function fallback_ad() {
    return '<a href="http://adserver.local/click.php?ad_id=fallback"><img src="http://adserver.local/creative/728_90_house_ad.gif" /></a>';
}