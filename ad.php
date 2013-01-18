<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Serve an ad to a requesting page.
 *  Get the highest priority ad from ad_serve table in database.
 *  If no ad is available, or connection fails, the fallback is hard coded house ad
 *  Asynchronously track the ad impression
 * 
 * Simplified code to minimize execution time.
 */

//$ad['html'];

$ad_html = fallback_ad();

// data to track
// $_GET['source_url'];
// $_SERVER['REMOTE_ADDR'];

header("Content-type: text/javascript");

echo 'document.write("' . addslashes( $ad_html ) . '");';


/** Return the HTML for the house ad. 
 * Hard coded to maximize chance ad will be served
 */
function fallback_ad() {
    return '<a href="http://adserver.local/click.php?ad_id=fallback"><img src="http://adserver.local/creative/728_90_house_ad.gif" /></a>';
}