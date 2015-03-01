<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap core CSS -->
  <link href="../css/bootstrap.css" rel="stylesheet">
  <link href="../css/global.css" rel="stylesheet">
  <!-- JQuery -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>

  <script type="text/javascript" src="js/acountlinking.js"></script>
</head>
<body>

  <!-- Import Universal Header -->
  <div id="header"></div>
  <script>
    $("#header").load("claheader.html"); 
  </script>

  <div class="container">

    <form class="form-horizontal" method="post" action="insert-twitter.php">
      <h4>Register for Scraping</h4>
      <p>Link your social media with your QUT Connect account.</p>
      <p>Linking up your social media accounts are essential to getting a better understand of your learning. </p> 
      <p>For both Facebook and google+ they require authentication and approval of user. This is why you will need to sign-in to each before linking or removing.</p>
      <p>Twitter requires you to enter your username (@yourname) then enter a hashtag to be associated with. You can add more hashtags at any time, but you must always enter your username.</p>      

      <h4>Twitter</h4>

      <div class="panel panel-custom">

        <div class="form-group">
          <label for="twitterProfile" class="col-sm-4 control-label">Enter your Twitter Username</label>
          <div class="col-sm-8">
            <input type="text" name="twitterProfile" id="twitterProfile" style="width:400px"W>
          </div>
        </div>

        
        <div class="form-group">
          <label for="twitterID" class="col-sm-4 control-label">Enter your Twitter User ID (Lookup <a href="http://mytwitterid.com/"  target="_blank">here</a>)</label>
          <div class="col-sm-8">
            <input type="text" name="twitterID" id="twitterID" style="width:400px">
          </div>
        </div>

        <div class="form-group">
          <label for="twitterEmail" class="col-sm-4 control-label">Enter your Email</label>
          <div class="col-sm-8">
            <input type="text" name="twitterEmail" id="twitterEmail" style="width:400px">
          </div>
        </div>

        <div class="form-group">
          <label for="twitterHashtag" class="col-sm-4 control-label">Enter Hashtag to Scrape</label>
          <div class="col-sm-8">
            <input type="text" name="twitterHashtag" id="twitterHashtag" style="width:400px">
          </div> 
        </div>

        <div class="form-group">
          <label for="inputPassword3" class="col-sm-4 control-label"></label>
          <div class="col-sm-8">
            <input class="btn btn-primary" name="submit" type="submit" value="Register"/>
          </div>
        </div>
      </div>



    </div>

  </div>

</form>

</div>




    </div><!--/container -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/components.js"></script>
    <script type="text/javascript" src="../js/jquery.bootstrap.wizard.min.js"></script>
    
    
  </body>
  </html>