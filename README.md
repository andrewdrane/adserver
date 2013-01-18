=== Simple ad server

Delivers ads via. javascript over the course of a campaign. 
Ensures distribution across time.

== Notes

= Database

There are no indexes on the tracking tables. This is done intentionally to ensure the fastest possible writes.
There are also no restrictions on null values. If there is an issue somewhere with some of the tracking data, it will record partial data, and allow administrators to discover if there are problems with tracking.

There is no identification with clients, just ads

== Setup

== To add in next version

- Update.php: if a campaign is over, set it to inactive. Remove the ad_serve table.
