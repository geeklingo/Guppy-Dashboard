<?php
//This script uses the Binance API to retrieve current holdings information and writes them to the Database

//If this was useful to you, feel free to shout me a coffee for the late hours that went into this
//BTC: 13QHePrFtKPY2axwRLVjEM6AjbbRvDSmP6
//ETH: 0x61a11050DC156CBA3ec49B81FC4F368FBd112059

//Load Database
//db: PT_DATA
$con = mysqli_connect("<DATABASE_IP>", "<DB USER>", "<PASSWORD>", "PT_DATA");

if (!$con) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
echo "Host information: " . mysqli_get_host_info($con) . PHP_EOL;


//Binance api
require 'vendor/autoload.php';

$BKEY = "<ENTER-YOUR-BINANCE-API>";
$BSEC = "<ENTER-YOUR-BINANCE-SECRET-CODE>";
$api = new Binance\API($BKEY,$BSEC);
$ticker = $api->prices();
$balances = $api->balances($ticker);


//Update current holdings table
//var_dump($balances);
//$btcUsed = $api->btc_value-$balances["BTC"]["available"];
$btcUsed = 0;
foreach ($balances as $p_row => $p_value)
{
  $coin = $p_row;
  $coinQty = $p_value["available"];
  $coinBTCValue = $p_value["btcTotal"];
  $btcUsed = $btcUsed + $coinBTCValue;
  $p_sql = "INSERT INTO binance_holdings(coin, qty, btc_value) VALUES('$coin','$coinQty','$coinBTCValue') ON DUPLICATE KEY UPDATE qty = '$coinQty', btc_value = '$coinBTCValue'";

  if ($con->query($p_sql) === TRUE) {
    echo $coin." record updated successfully with ".$coin." - ".$coinQty." - ".$coinBTCValue."\n";
  } else {
    echo "\nError: " . $p_sql . "\r\n" . $con->error;
  }
}
$btcUsed = $btcUsed -  $balances["BTC"]["available"];

//Update performance table
$ticker = $api->prices();
$btcFree = $balances["BTC"]["available"];
$totalBTC = $btcFree+$btcUsed;
$perShareETH = round(($totalBTC/$ticker['ETHBTC'])/$botShares,5);
$sql = "INSERT INTO portfolio_history(checked, value, freeBTC, usedBTC, ethValue, exchange) VALUES(now(), '$totalBTC','$btcFree','$btcUsed','$perShareETH','Binance')";
if ($con->query($sql) === TRUE) {
    echo "New record created successfully\n";
} else {
    echo "\nError: " . $sql . "\r\n" . $con->error;
}

//Close Database Connection
$con->close();

?>
