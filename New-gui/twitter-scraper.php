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
    <!-- JQuery & Bootstrap JS imports -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/components.js"></script>
    <script type="text/javascript" src="js/jquery.bootstrap.wizard.min.js"></script>




  </head>
  <body>
  <!-- Import Universal Header -->
  <div id="header"></div>
    <script>
      $("#header").load("claheader.html"); 
    </script>

    <div class="container">
    <h3>Twitter Scraper</h3>

      <?php
      require 'vendor/autoload.php';
      $lrs = new TinCan\RemoteLRS(
        'http://54.206.43.109/data/xAPI/',
        '1.0.1',
        'baae59cd41d1e07376d5038f859b3a7bb174ea63',
        '5fc0d4cc87d091408bac3bffc8fe33a07df8bdd4'
        );



      $hashtag_form = <<<HTML

      <form class="form-horizontal" method="post">
        <div class="panel panel-custom">
          <h4>1. Enter your LRS Details</h4>
          <div class="form-group">
            <label for="LRSEndpoint" class="col-sm-4 control-label">Enter Main LRS Endpoint</label>
            <div class="col-sm-8">
              <input type="text" name="LRSEndpoint" id="LRSEndpoint" style="width:400px" >
            </div>
          </div>
          <div class="form-group">
            <label for="LRSUser" class="col-sm-4 control-label">Enter Main LRS Username</label>
            <div class="col-sm-8">
              <input type="text" name="LRSUser" id="LRSUser" style="width:400px" >
            </div>
            </div>
            <div class="form-group">
              <label for="LRSPass" class="col-sm-4 control-label">Enter Main LRS Endpoint</label>
              <div class="col-sm-8">
                <input type="text" name="LRSPass" id="LRSPass" style="width:400px" >
              </div>
            </div>
          </div>

        <div class="panel panel-custom">
          <h4>2. Enter the Twitter ID to Scrape</h4>
          <label for="userID" class="col-sm-4 control-label">Twitter ID</label>
          <input name="userID" type="text" style="width:400px;"/>
          <input class="btn btn-primary" name="submit" type="submit" value="Scrape!"/>

        </div>

      </form>
HTML;

          if(!isset($_POST['userID'])){
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
            'consumer_key'       => '', // API key
            'consumer_secret'    => '', // API secret
            'oauth_token'        => '', // not needed for app only
            'oauth_token_secret' => '',
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
$userName = ($_POST['userID']);
$params = array(
  'screen_name' => $userName,
  'count' => 1000,
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
  [ 'id' => 'http://activitystrea.ms/schema/1.0/created' ]
  );
$activity = new TinCan\Activity(
  [ 'id' => 'http://adlnet.gov/exapi/activities/media' ]
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
<div class="modal fade" id="twitter-help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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