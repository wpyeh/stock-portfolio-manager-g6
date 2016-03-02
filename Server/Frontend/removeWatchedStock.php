<?php
	//enable global variables
	session_start();

	include_once('../Portfolio.class.php');
	include_once('../Account.class.php');
	include_once('YahooFinance.php');

	//Allow for errors to be displayed
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require '../../vendor/autoload.php';
	use Parse\ParseUser;
	use Parse\ParseClient;
	use Parse\ParseException;
	use Parse\ParseQuery;
	ParseClient::initialize('YtTIOIVkgKimi9f3KgvmhAm9be09KaFPD0lK1r21', 'Bxf6gl3FUT0goWvvx3DIger9bcOjwY1LflXr6MIO', 'r86cSKPWagMCavzJXVF4OFnte5yPpNY74GhY9UxS');

	//this function will update the user's watchlist in parse and the local trackedStock object
	function removeWatchedStock($tickerName){

		readfile("http://localhost/Frontend/dashboard.html");

		//getting the user's portfolio from Parse
		$queryWatchlist = new ParseQuery("Watchlist");

		//////******COMMENT THIS IN WHEN WE HAVE ACTUAL LOCAL OBJECTS****///
		// $queryStock->equalTo("username", username);

		$queryWatchlist->equalTo("username", "rebecca@usc.edu");
		try {
			$watchlist = $queryWatchlist->first();

			// The object was retrieved successfully.
				$stockNames = $watchlist->get("stockNames");

				if (($tickerKey = array_search($tickerName, $stockNames)) !== false) { //if they are tracking the stock, remove it from them
						unset($stockNames[$tickerKey]);
						$watchlist->setArray("stockNames", $stockNames); //push update to parse
						try{ //save this update to parse
					$watchlist->save();
				} 
				catch (ParseException $ex) {  
					echo 'Failed to update stock names when buying stock ' . $ex->getMessage();
				}
			}
			}

		catch (ParseException $ex) {
			// The object was not retrieved successfully.
			// error is a ParseException with an error code and message.
			echo 'error retrieving my portfolio';
		}
	}

	removeWatchedStock($_POST['ticker']);
?>