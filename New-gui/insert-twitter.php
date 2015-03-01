<?php
  // Cleanse input of special characters - SQL Injection Protection
   $twitterProfile = $_POST['twitterProfile'];
   $twitterID = $_POST['twitterID'];
   $twitterEmail = $_POST['twitterEmail'];
   $twitterHashtag = $_POST['twitterHashtag'];

   echo $twitterProfile; 
         echo '</br>';
      echo $twitterID;
            echo '</br>';

     echo  $twitterEmail;
           echo '</br>';
      echo $twitterHashtag;
            echo '</br>';
  

  // Create connection
  $conn = new mysqli("127.0.0.1","root","?gJ73X$4%","scraper");

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  } 


// Match user email against ID
  $sql = "SELECT  `ID` ,  `endpoint` ,  `username` ,  `password` ,  `email` 
          FROM  `users` 
          WHERE  `email` =  '$twitterEmail'
          LIMIT 0 , 30";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    //If email exists then find the corresponding ID
    while($row = $result->fetch_assoc()) {
      $ID = $row["ID"];
    }  
      // Hashtag insertion
    // Match grab hashtag ID for hashtag or create new hashtag ID
    $sql = "SELECT * 
            FROM  `hashtag` 
            WHERE  `hashtag` =  '$twitterHashtag'
            LIMIT 0 , 30";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // If hashtag exists in 'hashtag' table grab its ID
      while($row = $result->fetch_assoc()) {
        $hashtagID = $row["hashtagID"];
      }  
        // Insert user's details and requested hashtag into 'twitter'
    $sql = "INSERT INTO  `scraper`.`twitter` (
                                              `ID` ,
                                              `twitterID` ,
                                              `hashtagID` ,
                                              `profileName`
                                              )
              VALUES (
                      '$ID', 
                      '$twitterID',
                      '$hashtagID',
                      '$twitterHashtag'
                      );";

    } else {
      // If hashtag does not exist, add hashtag to hashtag table and add user details to twitter table
      $sql = "INSERT INTO  `scraper`.`hashtag` (
                                              `hashtagID` ,
                                              `ID` ,
                                              `hashtag` 
                                              )
              VALUES (
                      'hashtagID', 
                      '$ID',
                      '$twitterHashtag'
                      );";
      
    }




    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

  } else {
    //If email not in table notfify user
    echo "No such email in database.";
  }



  $conn->close();
  ?>

