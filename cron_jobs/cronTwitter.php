
<!--
    Twitter Scraper - Scrapes data from Twitter
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
      <?php
      require 'vendor/autoload.php';
      
            $username = "root";
            $password = "";
            $hostname = "localhost"; 

            //connection to the database
            $dbhandle = mysql_connect($hostname, $username, $password) 
              or die("Unable to connect to MySQL");
            //select a database to work with
            $selected = mysql_select_db("twitter",$dbhandle) 
              or die("Could not select twitter");
            
                   
          require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
          require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';
          use TwitterOAuth\TwitterOAuth;
          date_default_timezone_set('UTC');
          
          /**
     * Array with the OAuth tokens provided by Twitter when you create application
     * output_format - Optional - Values: text|json|array|object - Default: object
     */
          $config = array(
            'consumer_key'       => 'g0iHBvpghpaQ1mDq27F2fBtZ8', // API key
            'consumer_secret'    => 'LtSFohUi9Umv2mS69515L0V3B0rzS9a0299Kw6TELHZ4Y7tm0S', // API secret
            'oauth_token'        => '736893980-OJ6cP9rL07Xi2lQMn0uhrfhVtQAGJk9RvGOpJrpr', // not needed for app only
            'oauth_token_secret' => '8gu4qHESbZE8yNFQXxfjXuvKSmrMQDJ9eVwZLK1pSD5k0',
            'output_format'      => 'object'
        );
/**
     * Instantiate TwitterOAuth class with set tokens
     */
$connection = new TwitterOAuth($config);
    // Get an application-only token
    // more info: https://dev.twitter.com/docs/auth/application-only-auth
$bearer_token = $connection->getBearerToken();


$result = mysql_query("SELECT * FROM cron");

// defining post variables for the forms
$userName = 'twittername';
$hashtag = 'hashtag';
$LRSname = 'LRSname';
$LRSEndpoint = 'LRSEndpoint';
$LRSUser = 'LRSUser';
$LRSPass = 'LRSPass';
$timestamp = 'time';
$ID = "ID";
$usersDBRes = [];

//Get all registered users from db, save results to array
$uIndex = 0;
while ($rows = mysql_fetch_assoc($result)){         
$usersDBRes[$uIndex] = $rows;  
$uIndex++; 
        }
        
foreach($usersDBRes as $user) {       
// Twitter search paramaters
$params = array(
  'screen_name' => $user[$userName],
  'count' => 180,
  'exclude_replies' => true,
  );

// Connecting to specific twitter api function
$response = $connection->get('statuses/user_timeline', $params);
$reversed = array_reverse($response); // reverse the array to make sure the database information is updated with the correct data.
// Looping throught each post of twitter account
foreach ($reversed as $status) {
    
    $date = ($status->created_at);
    if ( strtotime($user[$timestamp]) < strtotime($date)){
        if (strpos($status->text, $user[$hashtag]) !== false) { // checking if the posts contain provided hashtag 
            print '<b>Name: </b>'.($status->user->name).'<br>'; // Name of Poster
            print '<b>Date Posted: </b>'.date('d-m-Y H:i:s', strtotime(($date))).'<br>'; // Date of post
            print '<b>Comment: </b>'.($status->text).'<br>'; // Conetent of post
            print '<b>Favourites: </b>'.($status->user->favourites_count).'<br>'; // times favourited
            print '<b>Retweets: </b>'.($status->retweet_count).'<br><hr>'; // times retweeted
                //$result = mysql_query("UPDATE cron SET time='$date' WHERE twittername='$user[$userName]'");// updates the databse timestamp
 // sending posts to LRS
        $lrs = new TinCan\RemoteLRS(
        $user[$LRSEndpoint], // LRS endpoint
        '1.0.1', // version
        $user[$LRSUser], // LRS UserName
        $user[$LRSPass]// LRS Password
        );
               $actor = new TinCan\Agent(
                    [ 'mbox' => $user[$LRSname] ]
                );
                $verb = new TinCan\Verb(
                    [ 'id' => 'http://activitystrea.ms/schema/1.0/created',
                        'display' => [
                          'en-US' => 'Created'  
                            
                     ]
                        ]
                );
                $activity = new TinCan\Activity(
                    [   'id' => 'http://www.localhost.com/CLAtoolkit/New-gui/twitter-scraper.php',
                        'definition' => [
                            'name' => [
                                'en-US' => 'posted',
                            ],
                            'description' => [
                        'en-US' => 'created a tweet',
                    ],
                        ]
                    ]  
                        );
                $res = new TinCan\Result(
                    [
                        'response' => $status->text
                        
                    ]    
                        
                        );
              
                $statement = new TinCan\Statement(
                    [
                        'actor' => $actor,
                        'verb'  => $verb,
                        'object' => $activity,
                        'result' => $res
                    ]
                );

                $response = $lrs->saveStatement($statement);
                if ($response->success) {
                    print "Statement sent successfully!\n";
                }
                else {
                    print "Error statement not sent: " . $response->content . "\n";
                }
  }
  else{
    print  "";
  }
    }
}
}
?>
