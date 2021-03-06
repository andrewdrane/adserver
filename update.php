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

    if( !check_still_active( $campaign['id'], $campaign['start'], $campaign['duration'] ) ) {
        continue; //no need to do more with this campaign
    }
    
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

/** Check if a campaign should still be active, based on duration and start date
 * 
 * if the campaign is expired, set the campaign 'active' flag to false and the
 * associated ad_serve active flag to false ase well.
 * 
 * @param int $campaign_id - id of the campaign
 * @param string $datetime_start - datetime string
 * @param int $duration - campaign duration in days
 * @return type boolean - whether the campaign is still active
 */
function check_still_active( $campaign_id, $datetime_start, $duration ) {
    $campaign_start = new DateTime( $datetime_start );
    $now = new DateTime('now');
    
    //If the campaign has gone past it's duration, set it to inactive and return false
    //Check current timestamp with timestamp of campaign start plus the duration in seconds
    if (  $now->getTimestamp() > ( $campaign_start->getTimestamp() + $duration * 24 * 60 ) ) {
        //bring $db into scope
        global $db;
        //set the campaign, and the related ad_serve tables to inactive
        $db->query("UPDATE campaigns SET active = 0 WHERE id = {$campaign_id} LIMIT 1;");
        $db->query("UPDATE ad_serve SET active = 0 WHERE campaign_id = {$campaign_id} LIMIT 1;");

        return false;
    }
    
    return true;
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