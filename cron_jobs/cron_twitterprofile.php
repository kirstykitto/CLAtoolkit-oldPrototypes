<!DOCTYPE html>
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
    along with this program.  If not, see <http://www.gnu.org/licenses/>.!> -->
<?php
        $username = "root";
        $password = "";
        $hostname = "localhost"; 

        //connection to the database
        $dbhandle = mysql_connect($hostname, $username, $password) 
          or die("Unable to connect to MySQL");
        echo "Connected to MySQL<br>";
        //select a database to work with
        $selected = mysql_select_db("scraper",$dbhandle) 
          or die("Could not select twitter");
        ?>

<?php
        require 'vendor/autoload.php';
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

        $result = mysql_query("SELECT users.*, twitter.profileName, twitter.twitterID,  hashtag.hashtag, hashtag.hashtagID FROM users INNER JOIN twitter ON users.ID=twitter.ID INNER JOIN hashtag ON users.ID=hashtag.ID;");

        $hashtag = "hashtag";
        $lrsUsername = "username";
        $lrsPassword = "password";
        $lrsEndpoint = 'endpoint';
        $profile = 'profileName';
        $usersDBRes = [];
        
        //Get all registered users from db, save results to array
        $uIndex = 0;
        while ($rows = mysql_fetch_assoc($result)){
            //print_r($rows);           
            $usersDBRes[$uIndex] = $rows;  
            $uIndex++; 
        }
        

       
    print '<br><br><br>';
     foreach($usersDBRes as $user) {
    $bearer_token = $connection->getBearerToken();                         
    $params = array(
        'screen_name' => $user[$profile],
        'count' => 3,
        'exclude_replies' => true,
                );
    $lrs = new TinCan\RemoteLRS(
        $user[$lrsEndpoint],
        '1.0.1',
        $user[$lrsUsername],
        $user[$lrsPassword]
        );
               $response = $connection->get('statuses/user_timeline', $params);
                //print_r ($response);
               foreach ($response as $status) {
			print '<b>Name: </b>'.($status->user->name).'<br>';
			print '<b>Comment: </b>'.($status->text).'<br><hr>';
                        

                        
                $actor = new TinCan\Agent(
                    [ 'mbox' => $status->user->name]
                );
                $verb = new TinCan\Verb(
                    [ 'id' => 'http://activitystrea.ms/schema/1.0/created',
                        'display' => [
                          'en-US' => 'Created'  
                            
                     ]
                        ]
                );
                $activity = new TinCan\Activity(
                    [   'id' => 'http://www.localhost.com/twitter',
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
     }
               
       
        ?>