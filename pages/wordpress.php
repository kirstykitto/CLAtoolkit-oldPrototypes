<!DOCTYPE html>
<!--
    Wordpress Scraper - Scrapes data from wordpress rss feed
    Copyright (C) 2014  Sebastian Cross
    Copyright (C) 2014  Zak Waters

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
	if(!isset($_POST['input'])){
		print <<<HTML
		<p>Enter the feed you wish to scrape: </p>
		<form name='form' method='post'>
			<input name='input' type='text'  style="width:400px;"/>
			<input name='submit' type='submit' value='Tracking data'/>
		</form>
            <br>
HTML;
		exit;
        }
                
                
$url = trim($_POST['input']); 
$feedurl = ("$url/?feed=rss2");
$xml = new SimpleXMLElement($feedurl, NULL, TRUE);
$commentsurl = ("$url/?feed=comments-rss2");
$xmlcomments = new SimpleXMLElement($commentsurl, NULL, TRUE);
$itemscomments = $xmlcomments->channel->item;

$items = $xml->channel->item; 
foreach ($items as $item) { 
    echo "<b> Title: </b>" .$item->title . "<br>"; 
    echo "<b> Comments: </b>" . $item->description . "<br>"; 
    echo "<b> Comment Link: </b>" . $item->comments. "<hr>";
}
      echo '<h1> Comments </h1>';
  foreach ($itemscomments as $itemcomment) { 
    echo "<b> Title: </b>" .$itemcomment->title . "<br>"; 
    echo "<b> Comments: </b>" . $itemcomment->description . "<br>"; 
    echo "<b> Comment Link: </b>" . $itemcomment->link. "<hr>";  
            
  }

        ?>
    </body>
</html>
