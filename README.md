The Connected Learning Analytics Toolkit
=========
The Connected Learning Analytics (CLA) toolkit uses a Learning Record Store (LRS) as specified by the experience API (xAPI). Any data can be sent to a LRS as long as there is the appropriate scraper to gather the data.

The Scraping tools work with the API’s made available by various social medias, this makes for easy and legal data gathering.  The Scrapers are built using JavaScript and PHP, this is defined by social medias and the methods recommended to interface with the APIs. 

The data statements are sent as JSON objects to the LRS. The objects are stored as actor-verb-object syntax as currently it is very basic but can be expanded to accommodate for more information. Below is an example of the syntax.

{" actor ": {
" mbox ": " mailto : jeff@example . com ",
" name ": " Jeff ",
" objectType ": " Agent "
},
" verb ": {
" id ":" http :// activitystrea . ms /../ create ",
" display ": {" en - US ": " Created "}
},
" object ": {
" objectType ":" Activity ",
" definition ": {
" name ": {" en - US ":" Posted "} , 

The importance of this toolkit is that once data is gathered and stored, the next step is to make sense and draw meaning from the data. This will be done with further reports and analysis…

The GUI
---------
The CLA Toolkit is built on top of Bootstrap, giving a simple HTML, CSS and JavaScript framework that helps standardise the Web based user interface. This will also help with the ongoing development of this toolkit and future implementation.

The toolkit also uses a wizard plugin for bootstrap - https://github.com/VinceG/twitter-bootstrap-wizard 

For further development of the GUI please refer to - http://getbootstrap.com/
