<body>
	<?php
		// //enable global variables
		// session_start();

		include_once('../Portfolio.class.php');
		include_once('../Account.class.php');
		include_once('../Stock.class.php');
		include_once('../TrackedStock.class.php');
		include_once('../OwnedStock.class.php');
		include_once('YahooFinance.php');

	//enable global variables
		session_start();

	 	//Allow for errors to be displayed
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	
		//Initialize parse and the classes it needs
		require '../../vendor/autoload.php';
		use Parse\ParseUser;
		use Parse\ParseClient;
		use Parse\ParseException;
		ParseClient::initialize('YtTIOIVkgKimi9f3KgvmhAm9be09KaFPD0lK1r21', 'Bxf6gl3FUT0goWvvx3DIger9bcOjwY1LflXr6MIO', 'r86cSKPWagMCavzJXVF4OFnte5yPpNY74GhY9UxS');

	function pullPortfolioDataFromYahoo(){
		$objYahooStock = new YahooStock;
		$objYahooStock->addFormat("snl1d1c1p2"); 

		$stocks = array_keys($_SESSION['account'] -> getPortfolio() -> getOwnedStock());
		$stockObjects = $_SESSION['account'] -> getPortfolio() ->getOwnedStock();
		
		foreach($stocks as $code=>$stock){
			$objYahooStock->addStock((string)$stock);
		}

    	foreach( $objYahooStock->getQuotes() as $code => $stock){
	      ?>
	      <!doctype html>

			<html>

			<link rel="stylesheet" type="text/css" href="style.css">
			
			<div class="stockmodule" id=<?php echo $stock[0]; ?> >

	      Ticker: <?php echo $stock[0]; ?> <br />
	      Company Name: <?php echo $stock[1]; ?> <br />
	      Quantity: <?php echo $stockObjects[ $stock[0] ] -> getNumberOwned(); ?> <br />
	      Last Trade Price: </b> <span class="money"><?php echo'$ '.$stock[2]; ?></span> <br />
	      Last Trade Date: <?php echo $stock[3]; ?> <br />
	      Dollar Change: </b>  <span class="money"><?php echo '$ '. $stock[4]; ?></span> <br />
	      Percent Change: </b> <span class="money"><?php echo $stock[5]; ?></span> <br />

	      </div>

			</html>

	      <?php


	    }
		
	}
	
	pullPortfolioDataFromYahoo();

?>
