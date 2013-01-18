# Simple ad server

Delivers ads via. javascript over the course of a campaign. 
Ensures even distribution across time.

## Program Flow

The program flows from two ends - the consumer end, which is the consumer viewing ads on a publisher's website (in this case, represented by nytimes.html) and the administrative end, accessed by cron job or manually, by loading update.php

When pinged, the <strong>update.php</strong> file will iterate through all active campaigns, and will either add a record to the ad_serve table, or  update an existing one.
If a campaign has gone past it's desired time span, the ad_serve table and the campaigns table are set to inactive.

The ad_serve table contains the allocated impressions count for each active ad. This allocation is increased over time. Each time update is pinged, it will increase the allocation to the appropriate amount relative to the lifespan of the campaign. 
Update will calculate the total number of impressions that should have been served from the start of the campaign to the end, compare it to the number of impressions already allocated, and add the difference.
This will prevent multiple or irregular hits to the update function from throwing off the campaign.

If a campaign has exceeded it's run (measured in days), then it is set to inactive.

From the consumer end, a javascript snipit retrieves an ad in javascript form 
from the <strong>ad.php</strong> file, and ideally sends along the source_url as a get parameter. 
This ad is determined by looking up the ad_serve record with the most pending ads.
The ad request should come with the URL of the page being viewed, using document.location.href

Ad.php makes an asynchronous call via. exec() to the track_impression and decrement the ad_serve table. 
This is done in a non-blocking fashion, so that the ad goes out quickly. 

Clicking on an ad brings thbe user to click.php, which will retrieve the ad info from the database.
If the info cannot be retrieved, the user is redirected back to referrer if possiboe, or an error is displayed.
The click is tracked.


## Assumptions

* Responsiveness is critical. Any failures should result in a 'house ad'
* Tracking is essential, but should not slow down the ads being served

## Notes

This setup assumes a base URL of http://adserver.local. This exists in the database, cron file and PHP files, so do a complete lookup prior to db import if change is desired.

### Database

* There are no indexes on the tracking tables. This is done intentionally to ensure the fastest possible writes.
* There are also no restrictions on null values. If there is an issue somewhere with some of the tracking data, it will record partial data, and allow administrators to discover if there are problems with tracking.
* There is a 'client_id' column, but currently no clients table. 

### Setup

* Create a database called adserver. Load the db_setup.sql into that database.
* Setup a local server with the url adserver.local. Point it to the adserver directory.
* Copy config.php.default to config.php. Fill in the required database connection info.
* Either install the crontab function, or just ping adserver.local/update.php to get things started.
* Visit the url adserver.local/nytimes.html to see the adserver doing its thing.

### To add in next version

* Error and exception reporting. Create an exceptions log or table to track problems.