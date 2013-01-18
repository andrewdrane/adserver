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

//wrap the retrieval in a try/catch. If anything goes wrong, just serve the house ad.
$ad_id = '0'; //house ad is default ad. Keep this
$ad_serve_id = '0'; //by default set this to 0

try {

    require_once 'includes/database.php';

    $db = new DB();


    $ad_serve_result = $db->query("SELECT id, ad_id, html FROM ad_serve WHERE pending > 0 AND active = 1 ORDER BY pending DESC LIMIT 0,1");
    $ad_serve = $ad_serve_result->fetch_assoc();
    
    if ( !empty( $ad_serve['html'] ) ) {
        $ad_serve_id = $ad_serve['id'];
        $ad_id = $ad_serve['ad_id'];

        //Now get the actual ad
        $ad_html = "<a href=\"http://adserver.local/click.php?ad_id={$ad_serve['ad_id']}\">{$ad_serve['html']}</a>";
    } else {
        $ad_html = fallback_ad();
    }

    $source_url = isset( $_GET['source_url'] ) ? $_GET['source_url'] : '0';
    //just in case we don't have one
    $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0';


} catch( Exception $e ) {
    $ad_html = fallback_ad();
}

//do an asynchronous tracking. Escape the command to be safe. ALWAYS do this, even if an exception was thrown. We want to know about this stuff.
exec( escapeshellcmd( "php track_impression.php {$ad_serve_id} {$ad_id} '{$source_url}' '{$ip_address}'") . " > /dev/null 2>/dev/null &" );


//send it back as javascript
header("Content-type: text/javascript");

echo 'document.write("' . addslashes( $ad_html ) . '");';



//HELPER FUNCTIONS


/** Return the HTML for the house ad. 
 * Hard coded to maximize chance ad will be served
 */
function fallback_ad() {
    return '<a href="http://adserver.local/click.php?ad_id=house"><img src="http://adserver.local/creative/728_90_house_ad.gif" /></a>';
}