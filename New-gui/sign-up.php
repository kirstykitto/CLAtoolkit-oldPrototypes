<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sign up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/global.css" rel="stylesheet">
    <!--<link href="../../dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../dist/css/bootstrap.min.css" rel="stylesheet">-->
       <script type="text/javascript" src="js/acountlinking.js"></script>
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
          <a class="navbar-brand" href="#">Reporting Dashboard</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="reports.html">Reporting</a></li>
            <li><a href="sign-up.html">Sign Up</a></li>
            <li><a href="http://54.206.43.109/">Your Learning Locker</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->

    <div class="container">
      <form class="form-wrapper cf">
          <div class="panel panel-custom">
          <p>Link your social media with your QUT Connect account.</p>
          <p>Linking up your social media accounts are essential to getting a better understand of your learning. </p> 
          <p>For both Facebook and google+ they require authentication and approval of user. This is why you will need to sign-in to each before linking or removing.</p>
          <p>Twitter requires you to enter your username (@yourname) then enter a hashtag to be associated with. You can add more hashtags at any time, but you must always enter your username.</p>      
            <div class="panel panel-custom">
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-4 control-label">Link your Facebook account</label>
                <div class="col-sm-8">
                  <div class="col-sm-6">
                  <fb:login-button autologoutlink="true" scope="user_groups,email"> </fb:login-button>
                  </div>
                  <div class="col-sm-6">
                  <button class="btn btn-primary"  onclick="setFacebookUserInfo()" type="button">Join</button>
                  <button class="btn btn-primary" type="button">remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-custom">
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-4 control-label">Link your Twitter account</label>
                <div class="col-sm-8">
                  <div class="col-sm-6">
                    <input type="text" id="username"placeholder="user name">
                    <input type="text" id="hashtag"placeholder="hashtag">
                  </div>
                  <div class="col-sm-6">
                    <button class="btn btn-primary" type="button">Join</button>
                    <button class="btn btn-primary" type="button">remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-custom">
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-4 control-label">Link your Google+ account</label>
                <div class="col-sm-8">
                  <div class="col-sm-6">
                    <p>Google sign in here</p>
                  </div>
                  <div class="col-sm-6">
                    <button class="btn btn-primary" type="button">Join</button>
                    <button class="btn btn-primary" type="button">remove</button>
                  </div>
                </div>
              </div>
            </div>
          </div>  
            </form>
        <?php

        $facebookId = $_GET["uid"];
        //Connecting to sql db.
        $connect = mysqli_connect("my host","my user","my passwrod","my db");
        //Sending facebook to sql db.
        mysqli_query($link, "INSERT INTO  `facebook` (`UID`,`ID`) VALUES ( '".$facebookId."' ,'')");

        //Sending google+ to sql db.
        mysqli_query($link, "INSERT INTO  `twitter` (`UID`,`ID` ,`name`) VALUES ( '".$uid."' ,'','".$name."')");
        //Sending twitter to sql db.
        mysqli_query($link, "INSERT INTO  `google` (`UID`,`ID` ,`name`) VALUES ( '".$uid."' ,'','".$name."')");
        
        mysqli_close($conn);
        ?>

    
    </div><!--/container -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/components.js"></script>
    <script type="text/javascript" src="../js/jquery.bootstrap.wizard.min.js"></script>
    
    
</body>
</html>