<?php
//PHP script that reads the sell and DCA logs from Profit Trailer and writes them to a mySQL database
//Load Database
//db: PT_DATA
//
//If this was useful to you, feel free to shout me a coffee for the late hours that went into this
//BTC: 13QHePrFtKPY2axwRLVjEM6AjbbRvDSmP6
//ETH: 0x61a11050DC156CBA3ec49B81FC4F368FBd112059

$con = mysqli_connect("<DATABASE_IP>", "<DB USER>", "<PASSWORD>", "PT_DATA");

if (!$con) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
echo "Host information: " . mysqli_get_host_info($con) . PHP_EOL;

//Default value should be 1 but if you are running a pool for the bot, you can adjust the number of shares in the pool here.
//Only used for the ETH per share chart
$botShares = 1;

//Location of PT JSON
//We use the backup file leaving the main file exclusive for PT
$PTdb = '/YOUR/PATH/TO/PROFITTRAILER/ProfitTrailerData.json.backup';

//Date offset - change to suit your timezone
$tz = new DateTimeZone('Australia/Brisbane');
date_default_timezone_set('Australia/Brisbane');

// Read JSON file
$json = file_get_contents($PTdb);

//Decode JSON
$json_data = json_decode($json,true);
var_dump($json_data);

//Grab the latest BTC/USD price
//Returns: {"mid":"10612.5","bid":"10612.0","ask":"10613.0","last_price":"10612.86597108","timestamp":"1519005852.851868018"}
$bfurl = "https://api.bitfinex.com/v1/ticker/btcusd";
$json = json_decode(file_get_contents($bfurl), true);
$BTCprice = $json["last_price"];

//Grab the latest ETH/BTC price
//Returns: {"mid":"0.0872345","bid":"0.087229","ask":"0.08724","last_price":"0.08724","timestamp":"1519005879.14314943"}
$bfurl = "https://api.bitfinex.com/v1/ticker/ethbtc";
$json = json_decode(file_get_contents($bfurl), true);
$ETHprice = $json["last_price"];

//Update the sell log table
foreach($json_data["sellLogData"] as $key => $value)
{
  $timesold = $value["soldDate"]["time"];
  unset($timesold["nano"]);
  $tmpdateSold = strtotime(implode("-",$value["soldDate"]["date"])." ".implode(":",$timesold));
  $dateSold = date('Y-m-d H:i:s',$tmpdateSold);
  //$firstBought
  $timebought = $value["averageCalculator"]["firstBoughtDate"]["time"];
  unset($timebought["nano"]);
  $tmpfirstBought = strtotime(implode("-",$value["averageCalculator"]["firstBoughtDate"]["date"])." ".implode(":",$timebought));
  $firstBought = date('Y-m-d H:i:s',$tmpfirstBought);
  //$newdateformat = date('Y-m-d',$dateSold);
  $profit = $value["profit"];
  $totalCost = $value["averageCalculator"]["totalCost"];
  $totalAmount = $value["averageCalculator"]["totalAmount"];
  $totalAmountWithSold = $value["averageCalculator"]["totalAmountWithSold"];
  $avgPrice = $value["averageCalculator"]["avgPrice"];
  $avgCost = $value["averageCalculator"]["avgCost"];
  $totalWeightedPrice = $value["averageCalculator"]["totalWeightedPrice"];
  $orderNumber = $value["averageCalculator"]["orderNumber"];
  $fee = $value["averageCalculator"]["fee"];
  $currentPrice = $value["currentPrice"];
  $sellStrategy = $value["sellStrategy"];
  $volume = $value["volume"];
  $triggerValue = $value["triggerValue"];
  $soldAmount = $value["soldAmount"];
  $boughtTimes = $value["boughtTimes"];
  $market = $value["market"];
  $percChange = $value["percChange"];


  $p_sql = "INSERT INTO pt_sellLogData(profit, avg_totalCost, avg_totalAmount, avg_totalAmountWithSold, avg_avgPrice, avg_avgCost, avg_firstBoughtDate, avg_totalWeightedPrice, avg_orderNumber, avg_fee, currentPrice, sellStrategy, volume, triggerValue, soldAmount, soldDate, boughtTimes, market,  percChange) ";
  $p_sql = $p_sql."VALUES('$profit','$totalCost','$totalAmount','$totalAmountWithSold','$avgPrice', '$avgCost','$firstBought','$totalWeightedPrice','$orderNumber','$fee','$currentPrice','$sellStrategy','$volume','$triggerValue','$soldAmount','$dateSold','$boughtTimes','$market','$percChange') ON DUPLICATE KEY UPDATE currentPrice = '$currentPrice'";

  //Write the table updates
  if ($con->query($p_sql) === TRUE) {
    echo $market." record updated successfully\n\n";
  } else {
    echo "\nError: " . $p_sql . "\r\n" . $con->error;
  }
}

//Process DCA Log
//Clear the current DCA log table
mysqli_query($con, "TRUNCATE TABLE pt_dcaLogData");

foreach($json_data["dcaLogData"] as $key => $value)
{
  //$firstBought
  $timebought = $value["averageCalculator"]["firstBoughtDate"]["time"];
  unset($timebought["nano"]);
  $tmpfirstBought = strtotime(implode("-",$value["averageCalculator"]["firstBoughtDate"]["date"])." ".implode(":",$timebought));
  $firstBought = date('Y-m-d H:i:s',$tmpfirstBought);
  $today = time();
  $aged = ($today - $tmpfirstBought)/(60 * 60 * 24);
  echo round($aged)."\n";

  $boughtTimes = $value["boughtTimes"];
  $buyProfit = $value["buyProfit"];
  $market = $value["market"];
  $profit = $value["profit"];
  $totalAmount = $value["averageCalculator"]["totalAmount"];
  $totalAmountWithSold = $value["averageCalculator"]["totalAmountWithSold"];
  $avgPrice = $value["averageCalculator"]["avgPrice"];
  $avgCost = $value["averageCalculator"]["avgCost"];
  $fee = $value["averageCalculator"]["fee"];
  $currentPrice = $value["currentPrice"];
  $sellStrategy = $value["sellStrategy"];
  $buyStrategy = $value["buyStrategy"];
  $volume = $value["volume"];
  $triggerValue = $value["triggerValue"];
  $percChange = $value["percChange"];

  //Update the SQL table with the DCA data
  $p_sql = "INSERT INTO pt_dcaLogData(boughtTimes, buyProfit, market, profit, avg_totalAmount, avg_totalAmountWithSold, avg_avgPrice, avg_avgCost, avg_firstBoughtDate, age, avg_fee, currentPrice, sellStrategy, buyStrategy, volume, triggerValue, percChange) ";
  $p_sql = $p_sql."VALUES('$boughtTimes','$buyProfit','$market','$profit','$totalAmount','$totalAmountWithSold','$avgPrice','$avgCost','$firstBought','$aged','$fee','$currentPrice','$sellStrategy','$buyStrategy','$volume','$triggerValue','$percChange')";

  if ($con->query($p_sql) === TRUE) {
    echo $market." DCA record updated successfully\n\n";
  } else {
    echo "\nError: " . $p_sql . "\r\n" . $con->error;
  }
}

//Update the bbBuy buy log table
foreach($json_data["bbBuyLogData"] as $key => $value)
{
  $BBLow = $value["BBLow"];
  $BBTrigger = $value["BBTrigger"];
  if ($value["positive"] = 'false') {
    $positive = 0;
  } else {
    $positive = 1;
  }
  //$positive = $value["positive"];
  $BBHigh = $value["BBHigh"];
  $currentValue = $value["currentValue"];
  $market = $value["market"];
  $profit = $value["profit"];
  $currentPrice = $value["currentPrice"];
  $buyStrategy = $value["buyStrategy"];
  $volume = $value["volume"];
  $percChange = $value["percChange"];

  //Clear the current possible buy log table
  mysqli_query($con, "TRUNCATE TABLE pt_bbBuyLogData");
  $p_sql = "INSERT INTO pt_bbBuyLogData(BBLow, BBTrigger, positive, BBHigh, currentValue, market, profit, currentPrice, buyStrategy, volume, percChange) ";
  $p_sql = $p_sql."VALUES('$BBLow','$BBTrigger','$positive','$BBHigh', '$currentValue','$market','$profit','$currentPrice','$buyStrategy','$volume','$percChange')";

  //Write the table updates
  if ($con->query($p_sql) === TRUE) {
    echo $market." bbBuy record updated successfully\n\n";
  } else {
    echo "\nError: " . $p_sql . "\r\n" . $con->error;
  }
}

//Close Database Connection
$con->close();

?>
