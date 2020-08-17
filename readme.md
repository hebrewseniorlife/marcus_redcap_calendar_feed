# Marcus Institute - Calendar Feed - REDCap External Module
## Purpose
The purpose of the REDCap module is to provide projects with the abilty to query and export calendar schedules for use by third-party tools.  Export formats include CSV and iCal.  The iCal feed may be used to import calendar schedules into popular calendar applications such as Google Calendar, Microsoft Outlook and mobile devices.  

## Concepts
### Calendar Feed
TBD
### Calendar Feed Link
TBD

## Project Caveats
REDCap administrators should consider the following when enabling the module.

* This module works only for longitudinal projects.  Singleton REDCap projects are not supported.
* Public link feeds have *no security contect*.  Althought the details of the query are hidden from the feed results; the query and subsequent return data set are executed without authentication or authorization. Public links should be used causiously and scruitenized appropriately. 
  * PHI should not be included in public links.  If PHI is included then the link should be proxied with an appropriate basic authentication schema.  
  * Public link feeds inherit the TLS/SSL settings of the parent REDCap installation and Web server settings
   
## Requirements
TBD

## Configuration Details
TBD
### Query String Parameters
TBD
#### Examples
TBD
### Twig Details
TBD
#### Examples