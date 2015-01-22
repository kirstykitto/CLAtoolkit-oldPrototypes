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

<html>
	<head>
		<title>Google+ JavaScript Quickstart</title>
		<link href="css/main.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="build/tincan.js"></script>
		<script type="text/javascript">
                           /** var tincan = new TinCan (
                                {
                                    recordStores: [
                                        {
                                            endpoint: "",
                                            username: "",
                                            password: "",
                                            allowFail: false
                                        }
                                    ]
                                }
                            );
    */
			(function() {
				var po = document.createElement('script');
				po.type = 'text/javascript';
				po.async = true;
				po.src = 'https://plus.google.com/js/client:plusone.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(po, s);
			})();
		</script>
		<!-- JavaScript specific to this application that is not related to API calls -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" ></script>
	</head>
	<body>
		<div id="gConnect">
			<button class="g-signin"
				data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me"
				data-requestvisibleactions="http://schemas.google.com/AddActivity"
				data-clientId=""
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
			<h2>&nbsp;Posts&nbsp;</h2>
			<div id="resp"></div>
		</div>
		<div id="loaderror">
			This section will be hidden by JQuery. If you can see this message, you
			may be viewing the file rather than running a web server.<br />
			The sample must be run from http or https. See instructions at
			<a href="https://developers.google.com/+/quickstart/javascript">
			https://developers.google.com/+/quickstart/javascript</a>.
		</div>
	</body>
	<script type="text/javascript">
            
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

				/**
				* Gets and renders the list of people visible to this app.
				*/
				people: function() {
					var request = gapi.client.plus.people.list({
						'userId': 'me',
						'collection': 'visible'
					});
					request.execute(function(people) {
						$('#visiblePeople').empty();
						$('#visiblePeople').append('Number of people visible to this app: ' +
						people.totalItems + '<br/>');
						for (var personIndex in people.items) {
							person = people.items[personIndex];
							$('#visiblePeople').append('<img src="' + person.image.url + '">');
						}
					});
				},

				/**
				* Gets and renders the currently signed in user's profile data.
				*/
				profile: function(){
					var request = gapi.client.plus.people.get( {'userId' : 'me'} );
					request.execute( function(profile) {
						$('#profile').empty();
						if (profile.error) {
							$('#profile').append(profile.error);
							return;
						}
						$('#profile').append($('<p><img src=\"' + profile.image.url + '\"></p>'));
						$('#profile').append($('<p>Hello ' + profile.displayName + '</p>'));
						if (profile.cover && profile.coverPhoto) {
						$('#profile').append($('<p><img src=\"' + profile.cover.coverPhoto.url + '\"></p>'));
						}
					});
				},
				// Uses the page name to grab all the posts related to it
				resp: function(id) {
					console.log(id);

					var request = gapi.client.plus.activities.list({
						'userId' : id,
						'collection' : 'public',
						'maxResults' : '20'
					});
					request.execute(function(resp) {
						var numItems = resp.items.length;
						var id;
						$('#resp').empty();
						for (var i = 0; i < numItems; i++) {

							id = resp.items[i].id;
;
							// Grabs all the comments from each post
							var req = gapi.client.plus.comments.list({
								'activityId' : id,
								'maxResults' : '5'
							});


							$('#resp').append('<p><b>ID: ' + resp.items[i].id + '</b></p><p>Content: '
								+ resp.items[i].object.content + '</p><p>Date: '
								+ resp.items[i].updated + '</p><p>'
								+ resp.items[i].actor.url + '</p>');
							req.execute(function(respcomment) {
								var numItems = respcomment.items.length;

								if(numItems) {
									for (var j = 0; j < numItems; j++){

										$('#resp').append('<p><b>Comment:</b> ' + respcomment.items[j].object.content +
										"</p><p><b>Comment ID:</b> " + respcomment.items[j].id + '</p><p><b>Name:</b>'
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
							tincan.sendStatement({
								actor: {
									mbox: id
								},
								verb: {
									id: "http://adlnet.gov/expapi/verbs/attempted"
								},
								target: {
									id: "http://rusticisoftware.github.com/TinCanJS"
								}
							});
						}
					});
				},
				commentExists: function() {

				}
			};
		}) ();
	/**
	 * jQuery initialization
	 */
	$(document).ready(function() {
		$('#disconnect').click(helper.disconnect);
		$('#collect').click(helper.resp);
		$('#g').click(function(){
			var id = $('#gID').val();
			console.log(id);
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
	}
	</script>
</html>
