window.fbAsyncInit = function() {
    FB.init({
        appId: '1451457058459658',
        xfbml: true,
        version: 'v2.0'
    });
};

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Var to hold user mapping information (Email - Facebook ID)
var userMapJSON;
// Time of last scrape retrieved from the LRS - to prevent duplicate
// scrapes
var res = ADL.XAPIWrapper.getStatements();

var lastScrapeDate = new Date();

// Variables for doughnut graph
var postsScraped = 0;
var likesScraped = 0;
var commentsScraped = 0;


var FunctionOne = function () {
    $('.loading-image').show();
  // create a deferred object
  var r = $.Deferred();
      // There must be a better way of doing this...
    fetchUserAuthTable();
    retrieveLastSubmittedStatements();

    if (!res.statements) {
        setTimeout(prepareScrape, 50);
        return;
    }

    retrieveLastScrape();

  // do whatever you want (e.g. ajax/animations other asyc tasks)

  setTimeout(function () {
    // and call `resolve` on the deferred object, once you're done
    r.resolve();
  }, 2500);

  // return the deferred object
  return r;
};

// define FunctionTwo as needed
var FunctionTwo = function () {
  console.log('FunctionTwo');
      var groupID = document.getElementById("groupID").value;

    FB.api("/" + groupID + "/feed", function(response) {
        if (response && !response.error) {
            console.log(response);

            // Send Posts and their likes
            sendPosts(response);


        }
    });

};
/*function prepareScrape() {
    
    
    // There must be a better way of doing this...
    fetchUserAuthTable();
    retrieveLastSubmittedStatements();
    if (!res.statements) {
        setTimeout(prepareScrape, 50);
        return;
    }
    retrieveLastScrape();
        $('.loading-image').hide();
    
}
function scrapeGroupFeed() {
    var groupID = document.getElementById("groupID").value;
    FB.api("/" + groupID + "/feed", function(response) {
        if (response && !response.error) {
            console.log(response);
            // Send Posts and their likes
            sendPosts(response);
        }
    });
}*/



function sendPosts(response) {

    
    //Set LRS Endpoint
    var conf = {
              "endpoint" : document.getElementById("LRSEndpoint").value
            };
            ADL.XAPIWrapper.changeConfig(conf);
    
    /* Send Posts} */
    for (postID = 0; postID < 10000; postID++) {
        // If no more post elements, break loop
        if (!response.data.hasOwnProperty(postID))
            break;

        // If node is of a type other than post, skip it. -TODO handle
        // questions/polls
        if (!response.data[postID].hasOwnProperty('message'))
            postID++;

        // Compare timestamp of post against last scrape date - scrape
        // if newer.
        // Ignore duplicates if checkbox ticked
        var postTimeStamp = new Date(response.data[postID].created_time);
        if (postTimeStamp > lastScrapeDate || document.getElementById("ignoreDup").checked) {
            console.log('Post:' + postID);

            var mappingInfo = getUserMapping(response.data[postID].from.id);

            var stmt = {
                "actor": {
                    "mbox": "mailto:" + mappingInfo[0],
                    "name": response.data[postID].from.name
                },
                "verb": {
                    "id": "http://activitystrea.ms/schema/1.0/create",
                    "display": {
                        "en-US": "created"
                    }
                },
                "object": {
                    "id": "http://adlnet.gov/exapi/activities/media",
                    "definition": {
                        "name": {
                            "en-US": "media"
                        }
                    }

                },
                "result" : {
                    "response" : 
                        response.data[postID].message
                    
                },  
                "timestamp": response.data[postID].created_time
                

            };

            console.log(stmt);

            // Send to central LRS
            var conf = {
                "auth": "Basic " + toBase64(document.getElementById("LRSUser").value + ':' + document.getElementById("LRSPass").value),
            };
            ADL.XAPIWrapper.changeConfig(conf);

            var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
            ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

            // Change to users' LRS & Send
            var conf = {
                "auth": "Basic " + toBase64(mappingInfo[1] + ':' + mappingInfo[2]),
            };
            ADL.XAPIWrapper.changeConfig(conf);

            var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
            ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

            // Increment post counter for graph
            postsScraped++;

            // Check for likes and possibly send them - only check if a
            // post has not been previously scraped
            sendLikes(response, postID);

        }
        // Check for comments and possible send them
        sendComments(response, postID);


    }

    // Post notification of scrape.
    if (document.getElementById("postNotif").checked) {
        notifyScrape();
    }

    writeScrapeInfo();

}

function sendComments(response, postID) {
        // Check to see if post has comments before entering loop to send
        // comments

        for (commentID = 0; commentID < 10000; commentID++) {
            if (!response.data[postID].hasOwnProperty('comments') || !response.data[postID].comments.data
                .hasOwnProperty(commentID))
                break;

            var commentTimeStamp = new Date(
                response.data[postID].comments.data[commentID].created_time);
            if (commentTimeStamp > lastScrapeDate || document.getElementById("ignoreDup").checked) {
                console.log('Comment on Post ' + postID + ':');

                var mappingInfo = getUserMapping(response.data[postID].comments.data[commentID].from.id);

                var stmt = {
                        "actor": {
                            "mbox": "mailto:" + mappingInfo[0],
                            "name": response.data[postID].comments.data[commentID].from.name
                        },
                        "verb": {
                            "id": "http://adlnet.gov/expapi/verbs/commented",
                            "display": {
                                "en-US": "commented on"
                            }
                        },
                        "object": {
                            "id": "http://adlnet.gov/exapi/activities/media",
                            "definition": {
                                "name": {
                                    "en-US": "media"
                                }
                            }

                        },
                        "result" : {
                            "response" : 
                                response.data[postID].comments.data[commentID].message
                            
                        },  
                        "timestamp": response.data[postID].comments.data[commentID].created_time

                    }
                    // Send to central LRS
                var conf = {
                        "auth": "Basic " + toBase64(document.getElementById("LRSUser").value + ':' + document.getElementById("LRSPass").value),
                    };
                ADL.XAPIWrapper.changeConfig(conf);

                var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
                ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

                // Change to users' LRS & Send
                var conf = {
                    "auth": "Basic " + toBase64(mappingInfo[1] + ':' + mappingInfo[2]),
                };
                ADL.XAPIWrapper.changeConfig(conf);

                var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
                ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

                // Increment comment counter for graph
                commentsScraped++;

            }

        }

    }
    // TODO Complete
    // Send likes - takes response API array, post ID, and optionally,
    // comment ID.
function sendLikes(response, postID, commentNum) {

    // If the like is on a comment take the comment ID
    if (commentNum != null) {
        for (likeID = 0; likeID < 10000; likeID++) {
            if (!response.data[postID].likes.data
                .hasOwnProperty(likeID))
                break;
            console.log("Comment like: " + response.data[postID].likes.data[likeID].name);
        }
    } else {
        for (likeID = 0; likeID < 10000; likeID++) {
            if (!response.data[postID].hasOwnProperty('likes') || !response.data[postID].likes.data
                .hasOwnProperty(likeID))
                break;
            /*
             * console.log("Post " + postID + " like: " +
             * response.data[postID].likes.data[post].name);
             */

            var mappingInfo = getUserMapping(response.data[postID].likes.data[likeID].id);

            var stmt = {
                "actor": {
                    "mbox": "mailto:" + mappingInfo[0],
                    "name": response.data[postID].likes.data[likeID].name
                },
                "verb": {
                    "id": "http://activitystrea.ms/schema/1.0/like",
                    "display": {
                        "en-US": "liked"
                    }
                },
                "object": {
                    "id": "http://adlnet.gov/exapi/activities/media",
                    "definition": {
                        "name": {
                            "en-US": "media"
                        }
                    }

                }


            }

            // Send to central LRS
            var conf = {
                    "auth": "Basic " + toBase64(document.getElementById("LRSUser").value + ':' + document.getElementById("LRSPass").value),
                };
            ADL.XAPIWrapper.changeConfig(conf);

            var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
            ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

            // Change to users' LRS & Send
            var conf = {
                "auth": "Basic " + toBase64(mappingInfo[1] + ':' + mappingInfo[2]),
            };
            ADL.XAPIWrapper.changeConfig(conf);

            var resp_obj = ADL.XAPIWrapper.sendStatement(stmt);
            ADL.XAPIWrapper.log("[" + resp_obj.id + "]: " + resp_obj.xhr.status + " - " + resp_obj.xhr.statusText);

            // Increment comment counter for graph
            likesScraped++;

        }

    }

}

function printGroupID() {
    FB.api("/285873894923511/members", function(response) {
        if (response && !response.error) {
            console.log(response);

        }
    });
}

function fetchUserAuthTable() {
    var ref = new Firebase("https://learninganalytics.firebaseio.com/");

    // Get a reference to our posts
    var postsRef = new Firebase(
        "https://learninganalytics.firebaseio.com/users");

    var authClient = new FirebaseSimpleLogin(ref,
        function(error, user) {
            if (error !== null) {
                console.log("Login error:", error);
            } else if (user !== null) {
                /*
                 * console.log("User authenticated with Firebase:",
                 * user);
                 */
            } else {
                console.log("User is logged out");
                alert('User is logged out');
            }
        });

    // Log user in anonymously
    authClient.login("anonymous");

    var jsonObject;

    // Attach an asynchronous callback to read the data at our posts
    // reference
    postsRef.on('value', function(snapshot) {
        jsonObject = snapshot.val();
        console.log("Successfully fetched user mapping:");
        console.log(snapshot.val()); // - Debug REMOVE
        userMapJSON = jsonObject; // TODO
    }, function(errorObject) {
        console.log('The read failed: ' + errorObject.code);
    });

}

// Determine auth details for users LRS
function getUserMapping(userFacebookID) {

    for (var key in userMapJSON) {
        if (userMapJSON[key].fbID == userFacebookID) {
            return [userMapJSON[key].email,
                userMapJSON[key].LRSHTTPUsername,
                userMapJSON[key].LRSHTTPPassword
            ];
        }
    }

}

// Determine the time and date of the last scrape
// Used for duplicate prevention in LRS
function retrieveLastSubmittedStatements() {
    res = ADL.XAPIWrapper.getStatements();
    ADL.XAPIWrapper.log(res.statements);

}

function retrieveLastScrape() {
    // Set last timestamp as date
    lastScrapeDate = new Date(1900, 1, 1);

    for (var i = 0; i < res.statements.length; i++) {
        var node = res.statements[i];
        var currentStatementScrapeDate = new Date(node.timestamp);

        if (currentStatementScrapeDate > lastScrapeDate) {
            lastScrapeDate = currentStatementScrapeDate;
        }
    }

    console.log("Last scrape date/time: " + lastScrapeDate);
}

// Dynamic facebook set up
function setFacebookUserInfo() {
    // sets end point in current json
    var ref = new Firebase("https://learninganalytics.firebaseio.com");
    // sets endpoint level in json
    var usersRef = ref.child("users");
    // gets email value from text input field
    var emailValue = $('#email-text-input').val();

    FB.api("/me", function(response) {
        // send json object to firebase
        usersRef.push({
            LRSHTTPPassword: $('#individualLRSUser').val(),
            LRSHTTPUsername: $('#individualLRSPass').val(),
            email: emailValue,
            fbID: response.id,
            name: response.name
        });
        alert('Registration complete.');
    });
}

function notifyScrape() {
    // posts to group as loggin user
    FB.api(
        "/" + document.getElementById("groupID").value + "/feed",
        "POST", {
            message: "The scraper has run. ####Edit Message####",
            link: 'http://jwpilkington.me/capstone/james/gui/images/scrape-notify.png'

        },
        function(response) {
            if (response && !response.error) {
                alert('Posting completed.');
            } else {
                alert('Error while posting.');
            }

        });
}

function writeScrapeInfo() {
    $('.loading-image').hide()
    $("div#scrapped-stats span").append('<strong> Scraping tool has collected:</strong><br/>' + postsScraped + 'Posts<br/>' + likesScraped + 'Likes<br/>' + commentsScraped + 'Comments <br/><br/> Exported to Learning Locker for INB302 - Capstone Project Group.');



    var doughnutData = [{
            value: commentsScraped,
            color: "#F7464A",
            highlight: "#FF5A5E",
            label: "Comments"
        }, {
            value: postsScraped,
            color: "#46BFBD",
            highlight: "#5AD3D1",
            label: "Posts"
        }, {
            value: likesScraped,
            color: "#FDB45C",
            highlight: "#FFC870",
            label: "Likes"
        }

    ];

    var ctx = document.getElementById("chart-area").getContext("2d");
    window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {
        responsive: true
    });


}