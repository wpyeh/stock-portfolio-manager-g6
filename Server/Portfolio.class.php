<html>
<body>  
<?php
          require '../../vendor/autoload.php';
          use Parse\ParseClient;
          use Parse\ParseException;
          use Parse\ParseQuery;
          ParseClient::initialize('YtTIOIVkgKimi9f3KgvmhAm9be09KaFPD0lK1r21', 'Bxf6gl3FUT0goWvvx3DIger9bcOjwY1LflXr6MIO', 'r86cSKPWagMCavzJXVF4OFnte5yPpNY74GhY9UxS');

     class Portfolio{

          private $totalValue;
          private $username;
          private $bankBalance;

          private $trackedStock = array();
          private $ownedStock = array();

          //query from parse for this user's portfolio. if they already have one, create
          //an associative array of ownedStock where the key is the stock ticker and the value is a
          //php OwnedStock object
          public function createOwnedStocks($tickerArray,$ownedStockArray){
            unset($this->ownedStock);
            $this->ownedStock = array();
            for ($x = 0; $x < count($tickerArray); $x++) {
              $this->ownedStock[$tickerArray[$x]] = $ownedStockArray[$x];
            }   
          }

          public function addOwnedStock($tickerSymbol, $newStock){
            $this->ownedStock[$tickerSymbol] = $newStock;
          }

          public function addTrackedStock($tickerSymbol, $newStock){
            $this->trackedStock[$tickerSymbol] = $newStock;
          }

          //query from parse for this user's watchlist. if they already have one, create
          //an associative array of trackedStock where the key is the stock ticker and the value is a
          //php TrackedStock object
          public function createWatchedStocks($tickerArray,$watchedStockArray){
            //example of how to make an associative array
            //$trackedStock["AAPL"] = $TrackedStockObjectHere
            unset($this->trackedStock);
            $this->trackedStock = array();
            for ($x = 0; $x < count($tickerArray); $x++) {
              $this->trackedStock[$tickerArray[$x]] = $watchedStockArray[$x];
            }   
          }

          public function getOwnedStock(){
            return $ownedStock;
          }

          public function getTrackedStock(){
            return $trackedStock;
          }

          public function setUsername($username){
               $this->username = $username;
          }

          public function getUsername(){
               return $username;
          }

          public function setBankBalance($bankBalance){
                $this->bankBalance = $bankBalance;
          }

          public function getBankBalance(){
            return $bankBalance;
          }

          public function buyStock($stockTicker, $numShares, $purchasePrice){

              //retrieve the portfolio from parse
              $queryStock = new ParseQuery("Portfolio");
              $queryStock->equalTo("username", $this->username);
              try {
                $portfolio = $queryStock->first(); 

                // The object was retrieved successfully.
                $stockNames = $portfolio->get("stockNames");
                $stockPurchaseDates = $portfolio->get("purchaseDates");
                $stockPurchasePrices = $portfolio->get("purchasePrices");
                $stockNumberShares = $portfolio->get("numberShares");
                $accountBalance = $portfolio->get("accountBalance");

                //if they try to purchase more stocks than they have the money for
                if($accountBalance < ($numShares * $purchasePrice)){
                  $failedOrder = "Insufficient funds. Transaction failed.";
                  echo "<script type='text/javascript'>alert('$failedOrder');</script>";
                  return;
                }

                //search to see if they already own the stock
                $found = false;
                foreach($stockNames as $code => $stock){
                  if( (string)$stock == (string)$stockTicker){
                    $found = true;
                  }
                }
                
                if($found == true){
                    //if they already own it, just need to update the numbers of shares they own of this stock
                  $prevNumber = $stockNumberShares[$stockTicker];
                  $stockNumberShares[$stockTicker] = $prevNumber + $numShares;
                  ksort($stockNumberShares);

                  $portfolio->setAssociativeArray("numberShares",$stockNumberShares);

                  //update local ownedStockList
                  

                }

                else{ //this is a newly owned stock, need to update
                  //updating parse values
                  array_push($stockNames, $stockTicker); //add it to stockNames
                  sort($stockNames);

                  $stockPurchaseDates[$stockTicker]=(string)date("Y/m/d"); //add in purchase date
                  ksort($stockPurchaseDates);

                  $stockPurchasePrices[$stockTicker]=$purchasePrice; //add in purchase price
                  ksort($stockPurchasePrices);

                  $stockNumberShares[$stockTicker]=$numShares; //add in number of shares
                  ksort($stockNumberShares);
                  
                  //update values for Parse object
                  $portfolio->setArray("stockNames", $stockNames);
                  $portfolio->setAssociativeArray("purchaseDates", $stockPurchaseDates);
                  $portfolio->setAssociativeArray("purchasePrices", $stockPurchasePrices);
                  $portfolio->setAssociativeArray("numberShares", $stockNumberShares);

                  //update local ownedStockList
                }

                  //update account balance
                  $newBalance = $accountBalance - ($purchasePrice * $numShares);
                  // echo $numShares; </br></br>
                  $portfolio->set("accountBalance", $newBalance);

                $successfulOrder = "Transaction successful!";
                echo "<script type='text/javascript'>alert('$successfulOrder');</script>";

                try{ //save this update to parse
                  $portfolio->save();
                } catch (ParseException $ex) {  
                  echo 'Failed to update stock names when buying stock ' . $ex->getMessage();
                }
              }

              catch (ParseException $ex) {
                // The object was not retrieved successfully.
                // error is a ParseException with an error code and message.
                echo 'error retrieving my portfolio';
              }
            }

public function sellStock($stockTicker, $numShares){
    $queryStock = new ParseQuery("Portfolio");
    ///////USE GLOBAL OBJECT USERNAME
    $queryStock->equalTo("username", "rebecca@usc.edu");
    try {
        $portfolio = $queryStock->first();
       // The object was retrieved successfully.
        $stockNames = $portfolio->get("stockNames");
        $stockPurchaseDates = $portfolio->get("purchaseDates");
        $stockPurchasePrices = $portfolio->get("purchasePrices");
        $stockNumberShares = $portfolio->get("numberShares");
        $accountBalance = $portfolio->get("accountBalance");

        $found = false;

        //search if they own this stock or not
        foreach($stockNames as $code => $stock){
            if( (string)$stock == (string)$stockTicker){
                $found = true;
            }
        }

        if($found == false){ //if user does not already own shares of this stock, THEY CANNOT SELL THEM!
           $failedSell = "You do not own this stock. Transaction failed.";
           echo "<script type='text/javascript'>alert('$failedSell');</script>";
           return;
        }

        //they do own it
        if($found == true){

            //if they own it, now check if they own at least # shares they want to sell
            if($stockNumberShares[$stockTicker] < $numShares){
                $failedShares = "You do not own enough shares. Transaction failed.";
                 echo "<script type='text/javascript'>alert('$failedShares');</script>";
                return;
            }

            //now, check if they sell all of their shares, if so remove from their owned list 
            ///*****UPDATE LOCAL OBJECT*****************
            if($stockNumberShares[$stockTicker] == $numShares){

                //remove it from list of stocks they own
                if (($nameKey = array_search($stockTicker, $stockNames)) !== false) {
                    unset($stockNames[$nameKey]);
                    $portfolio->setArray("stockNames", $stockNames);
                }

                //remove it from purhcaseDates
                if (($dateKey = array_search($stockTicker, $stockPurchaseDates)) !== false) {
                    unset($stockPurchaseDates[$dateKey]);
                    $portfolio->setAssociativeArray("purchaseDates", $stockPurchaseDates);    
                }

                //remove it from purchasePrices
                if (($priceKey = array_search($stockTicker, $stockPurchasePrices)) !== false) {
                    unset($stockPurchasePrices[$priceKey]);
                    $portfolio->setAssociativeArray("purchasePrices", $stockPurchasePrices);    
                }

                //remove it from numberShares
                if (($shareKey = array_search($stockTicker, $stockNumberShares)) !== false) {
                    unset($stockNumberShares[$shareKey]);
                    $portfolio->setAssociativeArray("numberShares", $stockNumberShares);    
                }     
            }

            else{ //else, they still own at least 1 share of that stock 

                $prevNumberOwned = $stockNumberShares[$stockTicker]; //update the number of shares they own now
                $stockNumberShares[$stockTicker]=$prevNumberOwned - $numShares; //add in number of shares
                ksort($stockNumberShares);

                //update values for Parse object
                $portfolio->setAssociativeArray("numberShares", $stockNumberShares);
            }

            //now update account balance to reflect the sale

            //get the current price of this stock from Yahoo
            $objYahooStock = new YahooStock;
            $objYahooStock->addFormat("snl1d1t1c1p2"); 
            $objYahooStock->addStock($stockTicker);

            $price;
            foreach( $objYahooStock->getQuotes() as $code => $stock){
                $price = floatval($stock[2]); //will only get the price for one stock
            }

            $newBalance = $accountBalance + ($price * $numShares);

            $portfolio->set("accountBalance", $newBalance);

            $successfulOrder = "Transaction successful!";
            echo "<script type='text/javascript'>alert('$successfulOrder');</script>";
        

            try{ //save this update to parse
                $portfolio->save();
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
     }
     ?>

</body>
</html>