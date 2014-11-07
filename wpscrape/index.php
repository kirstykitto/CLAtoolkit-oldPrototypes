<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
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
