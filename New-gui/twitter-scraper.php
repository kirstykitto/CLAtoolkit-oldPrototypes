<!DOCTYPE html>
<html>
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
<head>
    <meta charset="utf-8">
    <title>Twitter Scraper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/global.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-fixed-top navbar-inverse" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">CLA Toolkit</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.html">Home</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Scrapers<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="facebook-scraper.html">Facebook Scraper</a></li>
                <li><a href="googleplus-scraper.html">Google Plus Scraper</a></li>
                <li><a href="stack-exchange-scraper.html">Stack Exchange Scraper</a></li>
                <li><a href="twitter-scraper.html">Twitter Scraper</a></li>
              </ul>
            </li>
            <li><a href="reports.html">Reports</a></li>
            <li><a href="sign-up.html">Sign Up</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->

    <div class="container">
      <?php
                require 'vendor/autoload.php';
                $lrs = new TinCan\RemoteLRS(
                    'http://54.206.43.109/data/xAPI/',
                    '1.0.1',
                    'baae59cd41d1e07376d5038f859b3a7bb174ea63',
                    '5fc0d4cc87d091408bac3bffc8fe33a07df8bdd4'
                );
    $hashtag_form = <<<HTML
      <form id="commentForm" method="get" name="form" class="form-horizontal">
<div id="rootwizard">
  <ul class="tabs-custom">
    <li><a href="#tab1" data-toggle="tab">LRS Details</a></li>
    <li><a href="#tab2" data-toggle="tab">Hashtag</a></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane" id="tab1">
        <div class="control-group">
           <div class="controls">
            <label>Enter Main LRS Endpoint</label>
            <input type="text" name="LRSEndpoint" id="LRSEndpoint">
          </div>  
          <div class="controls">
            <label>Enter Main LRS Username</label>
            <input type="text" name="LRSUser" id="LRSUser">
          </div>
          <div class="controls">
            <label>Enter Main LRS Password</label>
            <input type="text" name="LRSPass" id="LRSPass">
          </div>  
        </div>
      </div><!-- /tab1 -->

      <div class="tab-pane" id="tab2">
        <div class="control-group">
          
          <div class="controls">
         
        <p>Enter the profile you wish to scrape: </p>
        
          <input name="input" type="text" style="width:400px;"/>
          <input name="submit" type="submit" value="Tracking data"/>
                                      
          </div>  
        </div>
      </div><!-- /tab2 -->



    <ul class="pager wizard">
      <li class="previous first" style="display:none;"><a href="#">First</a></li>
      <li class="previous"><a href="#">Previous</a></li>
      <li class="next last" style="display:none;"><a href="#">Last</a></li>
        <li class="next"><a href="#">Next</a></li>
    </ul>
  </div>  
</div>
</form>
HTML;
    if(!isset($_POST['input'])){
      print $hashtag_form;
      exit;
    }
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
    print $hashtag_form;
    $userName = ($_POST['input']);

                $params = array(
                    'screen_name' => $userName,
                    'count' => 3,
                    'exclude_replies' => true,
                );
                $response = $connection->get('statuses/user_timeline', $params);
               foreach ($response as $status) {
      print '<b>Name: </b>'.($status->user->name).'<br>';
      print '<b>Comment: </b>'.($status->text).'<br><hr>';
                                                
                $actor = new TinCan\Agent(
                    [ 'mbox' => $status->user->name]
                );
                $verb = new TinCan\Verb(
                    [ 'id' => 'http://adlnet.gov/expapi/verbs/experienced' ]
                );
                $activity = new TinCan\Activity(
                    [ 'id' => 'http://rusticisoftware.github.com/TinCanPHP' ]
                );
                $statement = new TinCan\Statement(
                    [
                        'actor' => $actor,
                        'verb'  => $verb,
                        'object' => $activity,
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

    ?>
<!-- Modal -->
    <div class="modal fade" id="facebookgroup-help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
          </div>
          <div class="modal-body">
            ...
          </div>
        </div>
      </div>
    </div><!-- /Modal social-media-help -->

        
    </div><!--/container -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/components.js"></script>
    <script type="text/javascript" src="js/jquery.bootstrap.wizard.min.js"></script>
    <script>
    $(document).ready(function() {
    var $validator = $("#commentForm").validate({
      rules: {
       socialfield: {
          required: true
          
        },
        endpointfield: {
          required: true
          
        }
    
      }
    });
 
      $('#rootwizard').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'onNext': function(tab, navigation, index) {
          var $valid = $("#commentForm").valid();
          if(!$valid) {
            $validator.focusInvalid();
            return false;
          }
        }
      }); 
});
    </script>

    <script type="text/javascript" src="js/xapiwrapper.min.js"></script>
    <script src="../include/Chart.js"></script>
    

</body>
</html>