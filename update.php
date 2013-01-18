<?php
/** AdServer
 * 2013 Andrew Drane
 * 
 * Update the lookup tables for all active campaigns
 *  Check if
 * 
 * Ideally called by CRON. This will increase the 'pending' ads according to 
 *  how many ads should be left in the time period (assuming even distribution)
 * Cron can be run every 15 minutes, or more. URL can be updated whenever appropriate;
 */

//get the active campaigns

include_once 'includes/database.php';

$db = new DB();
$active_campaigns = $db->query('SELECT id, ad_id, impressions, allocated, start, duration FROM campaigns WHERE active = 1');

//Loop through result set
while ( $campaign = $active_campaigns->fetch_assoc() ) {

    //Check for an existing ad_serve
    $ad_serve_result = $db->query( "SELECT id, campaign_id, ad_id, pending FROM ad_serve WHERE campaign_id = {$campaign['id']} LIMIT 0,1" );
    $ad_serve = $ad_serve_result->fetch_assoc();
    
    $pending = get_next_allocation($campaign['impressions'], $campaign['allocated'], $campaign['start'], $campaign['duration']);
    
    //if this is a new ad_serve
    if ( empty($ad_serve['id'] ) ) {
        $db->query("INSERT INTO ad_serve (campaign_id, ad_id, pending,html) VALUES ({$campaign['id']},{$campaign['ad_id']},{$pending},(SELECT html FROM advertisements WHERE id = {$campaign['ad_id']} LIMIT 0,1));");
    } else {
        //update existing ad serve
        $db->query("UPDATE ad_serve SET pending = pending + {$pending} WHERE id = {$ad_serve['id']} LIMIT 1;");
    }
    
    //now update the campaigns so we know what's been allocated
    $db->query("UPDATE campaigns SET allocated = allocated + {$pending} WHERE id = {$campaign['id']} LIMIT 1;");
    
}


/** Returns the number of ads to allocate from the pending ads, to the ad_serve table
 *
 * @param type $total_ads
 * @param int $allocated - # of ads already allocated
 * @param string $datetime_start  - datetime string in mysql format. 
 * @param int $duration # of days duration for the campaign
 */
function get_next_allocation( $total_impressions, $allocated, $datetime_start, $duration ) {
    $campaign_start = new DateTime( $datetime_start );
    $now = new DateTime('now');
    
    //campaign elapsed in minutes
    $campaign_elapsed_minutes = ceil( ( $now->getTimestamp() - $campaign_start->getTimestamp() ) / 60 );
    
    //get total minutes of campaign
    $total_campaign_minutes = $duration * 24 * 60;
    
    //get total ad impressions that should be allocated, as a fraction of the elapsed time
    return ceil( $total_impressions * ( $campaign_elapsed_minutes / $total_campaign_minutes ) ) - $allocated;
}