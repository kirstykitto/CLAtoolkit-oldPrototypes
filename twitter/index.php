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
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
	<head>
		<meta charset="UTF-8">
		<title>Twitter Scraping tool V1.0</title>
	</head>
	<body>
		<?php
		$hashtag_form = <<<HTML
				<p>Enter the hashtag you wish to scrape: </p>
				<form name="form" method="post">
					<input name="input" type="text" style="width:400px;"/>
					<input name="submit" type="submit" value="Tracking data"/>
				</form>
				<br>
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
		$q = ($_POST['input']);
		$paramstags = array(
			'q' => $q, // name of the current hashtag being scraped
			'count' => 20   ,// detirmin the amount of posts to be craped.
			'exclude_replies' => true
		);
		$tags = $connection->get('search/tweets', $paramstags);

		//echo '<pre>'; print_r($tags); echo '</pre>'; >>>>>>>> Use this to display all information that is being scraped from the hashtag.

		foreach ($tags->statuses as $tagreplier) {
			print '<b>Name: </b>'.($tagreplier->user->name).'<br>';
			print '<b>Comment: </b>'.($tagreplier->user->description).'<br><hr>';
		}
		?>
	</body>
</html>
