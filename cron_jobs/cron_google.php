<!--

/*
 *
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

-->

<?php require 'vendor/autoload.php'; ?>
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
<html>
<head>
  <title>Google+ JavaScript Quickstart</title>
   <LINK href="css/styles.css" rel="stylesheet" type="text/css">
   <script type="text/javascript" src="build/tincan.js"></script>
  <script type="text/javascript">
      var tincan = new TinCan (
    {
        recordStores: [
            {
                endpoint: "http://54.206.43.109/data/xAPI/",
                username: "110e5456e3787584ae37b24747752f3f68cef32d",
                password: "623203e7d3657ffde0ba2d999f352c1be5898f81",
                allowFail: false
            }
        ]
    }
);

  (function() {
    var po = document.createElement('script');
    po.type = 'text/javascript'; po.async = true;
    po.src = 'https://plus.google.com/js/client:plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
  })();
  </script>
  <!-- JavaScript specific to this application that is not related to API
     calls -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" ></script>
</head>
<body>
  <div id="gConnect">
    <button class="g-signin"
        data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me"
        data-requestvisibleactions="http://schemas.google.com/AddActivity"
        data-clientId="798271829765-n8o3qe26bpj99asqj7dkgkspli2egcfl.apps.googleusercontent.com"
        data-callback="onSignInCallback"
        data-theme="dark"
        data-cookiepolicy="single_host_origin">
    </button>
  </div>
  <div id="authOps" style="display:none">
    <button id="disconnect" >Disconnect your Google account from this app</button>
    
       <p>Enter the  Google+ page you wish to scrape: </p>
		
			<input id ="gID" name='userID' type='text'  style="width:400px;"/>
                        <button id="g" type="submit" >Find User Posts</button>
		
    <h2> Posts </h2>
    <div id="resp"> 
        </div>
           
  </div>
  <div id="loaderror">
    This section will be hidden by JQuery. If you can see this message, you
    may be viewing the file rather than running a web server.<br />
    The sample must be run from http or https. See instructions at
    <a href="https://developers.google.com/+/quickstart/javascript">
    https://developers.google.com/+/quickstart/javascript</a>.
  </div>
  <?php       
    
        $result = mysql_query("SELECT users.*, google.googlePluspage, google.keyword FROM users INNER JOIN google ON users.ID=google.ID;");
        // $googlePluspage = "googlePluspage";
         $lrsEndpoint = 'endpoint';
		 
		$PgNameArray = array();
		$i = 0;
         
        while ($rows = mysql_fetch_assoc($result)){
			
		$PgNameArray[$i] = $rows['googlePluspage'];
		$i++;

         $googlePage = $rows['googlePluspage'];
         $keyword = $rows['keyword'];
        }
		 
		 //Create Javascript Array from PHP array in order to retive the names from the DB
		 
		 echo '<script type="text/javascript">';
		 echo "var names = ["; 
		 $arraySize = count($PgNameArray);
		 $indexTrack = 1;
		 foreach ($PgNameArray as $name) {
			if ($indexTrack < $arraySize) {
				echo "'$name', ";
			} else { echo "'$name'"; }
			$indexTrack++;
		 }
		 echo "];";
         echo '</script>';

		  ?> 
</body>
<script type="text/javascript">
        var page = "<?php echo($googlePage); ?>";
        var helper = (function() {
        var BASE_API_PATH = 'plus/v1/';

          return {
    /**
     * Hides the sign in button and starts the post-authorization operations.
     *
     * @param {Object} authResult An Object which contains the access token and
     *   other authentication information.
     */
    onSignInCallback: function(authResult) {
      gapi.client.load('plus','v1', function(){
        $('#authResult').html('Auth Result:<br/>');
        for (var field in authResult) {
          $('#authResult').append(' ' + field + ': ' +
              authResult[field] + '<br/>');
        }
        if (authResult['access_token']) {
          $('#authOps').show('slow');
          $('#gConnect').hide();
          helper.profile();
          helper.people();
          //helper.resp();
          //helper.respcomment();
        } else if (authResult['error']) {
          // There was an error, which means the user is not signed in.
          // As an example, you can handle by writing to the console:
          console.log('There was an error: ' + authResult['error']);
          $('#authResult').append('Logged out');
          $('#authOps').hide('slow');
          $('#gConnect').show();
        }
        console.log('authResult', authResult);
      });
    },

    /**
     * Calls the OAuth2 endpoint to disconnect the app for the user.
     */
    disconnect: function() {
      // Revoke the access token.
      $.ajax({
        type: 'GET',
        url: 'https://accounts.google.com/o/oauth2/revoke?token=' +
            gapi.auth.getToken().access_token,
        async: false,
        contentType: 'application/json',
        dataType: 'jsonp',
        success: function(result) {
          console.log('revoke response: ' + result);
          $('#authOps').hide();
          $('#profile').empty();
          $('#visiblePeople').empty();
          $('#authResult').empty();
          $('#gConnect').show();
        },
        error: function(e) {
          console.log(e);
        }
      });
    },


   // Uses the page name to grab all the posts related to it 
    resp: function() {
   // Going throw the database array of names in order to scrape for the loop
    for(var i = 0; i < names.length; i++){
        var request = gapi.client.plus.activities.list({
        'userId' : names[i],
        'collection' : 'public',
        'maxResults' : '1'
        });
        request.execute(function(resp) {
            var numItems = resp.items.length;


            for (var k = 0; k < numItems; k++) {
                page = resp.items[k].id;
                // Grabs all the comments from each post
                var req = gapi.client.plus.comments.list({
                    'activityId' : page,
                    'maxResults' : '1'
                });
                
                
                $('#resp').append('<p><b>ID: ' + resp.items[k].id + '</b></p><p>Content: '
                        + resp.items[k].object.content + '</p><p>Date: ' 
                        + resp.items[k].updated + '</p><p>'
                        + resp.items[k].actor.url + '</p><p>'
                        + resp.items[k].actor.displayName + '</p><br><br><hr>');
                
                req.execute(function(respcomment) {
                    
                    var numItems = respcomment.items.length;
                    if(numItems) {
                        for (var j = 0; j < numItems; j++){

                            $('#resp').append('<p><b>Comment:</b> ' 
                                    + respcomment.items[j].object.content +  "</p><p><b>Comment ID:</b> " 
                                    + respcomment.items[j].id + '</p><p><b>Name:</b>'
                                    + respcomment.items[j].actor.displayName + '</p><hr>' );
                tincan.sendStatement(
                    {
                        actor: {
                            mbox: respcomment.items[j].actor.displayName
                        },
                        verb: {
                            id: "http://adlnet.gov/expapi/verbs/attempted"
                        },
                        target: {
                            id: "http://rusticisoftware.github.com/TinCanJS"
                        }
                    }
                );                        
            }    
         }
    });
            tincan.sendStatement(
                {
                    actor: {
                        mbox: resp.items[k].actor.displayName
                    },
                    verb: {
                        id: "http://adlnet.gov/expapi/verbs/attempted"
                    },
                    target: {
                        id: "http://rusticisoftware.github.com/TinCanJS"
                    }
                }
            );
                
              }
        });
    }
}};
    
    
  }) ();
  

/**
 * jQuery initialization
 */
$(document).ready(function() {
  $('#disconnect').click(helper.disconnect);
  $('#collect').click(helper.resp);
  $('#g').click(function(){
      var id = $('#gID').val();
     
      
      helper.resp(id);
  });
  $('#loaderror').hide();
  if ($('[data-clientid="YOUR_CLIENT_ID"]').length > 0) {
    alert('This sample requires your OAuth credentials (client ID) ' +
        'from the Google APIs console:\n' +
        '    https://code.google.com/apis/console/#:access\n\n' +
        'Find and replace YOUR_CLIENT_ID with your client ID.'
    );
  }
});
/**
 * Calls the helper method that handles the authentication flow.
 *
 * @param {Object} authResult An Object which contains the access token and
 *   other authentication information.
 */
function onSignInCallback(authResult) {
  helper.onSignInCallback(authResult);
};
    

</script>
</html>
