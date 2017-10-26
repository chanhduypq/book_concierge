<?php

include('config.php');

$sql = "SELECT * FROM bf_currencies";
$query = mysql_query($sql);

$currencies = array();
while ($row = mysql_fetch_assoc($query)) {
    $currencies[] = $row['iso'];
}

if (!empty($currencies)) {
    foreach ($currencies as $from) {
        foreach ($currencies as $to) {
            if ($from == $to) 
                continue;
            
            mysql_query("INSERT IGNORE INTO bf_xrates (`from`, `to`) values ('$from', '$to')") or die(mysql_error());
        }
    }
}

//date_default_timezone_set('Asia/Kolkata');
$from = '';
$to = '';


$arrrate = '';
$arrrtprice = '';
$rate = '';
$rtprice = '';
$curdate = date('Y-m-d');

$sqlcurrency = "select * from bf_xrates where   updated < '" . date('Y-m-d') . "'";
$query = mysql_query($sqlcurrency);
while ($rowcurrency = mysql_fetch_array($query)) {
    $to = $rowcurrency['to'];
    $from = $rowcurrency['from'];
    $url = "https://www.google.com/finance/converter?a=1&from=" . $from . "&to=" . $to . "&meta=ei%3DQHsKVfO8JcHkuATckIGIAQ";

    $arr = file_get_contents($url);
    $arrrate = explode('<div id=currency_converter_result>', $arr);
    $arrrtprice = explode('</span>', $arrrate[1]);
    $rate = substr($arrrtprice[0], strpos($arrrtprice[0] + 3, 'bld>'));
    $rate = explode(' ', $rate);
    $rtprice = str_replace('class=bld>', '', $rate[4]);
    $sqlupdcurrency = "update bf_xrates set xrate= $rtprice, updated='" . date('Y-m-d h:i:s') . "' where `from`='" . $from . "' AND `to`='" . $to . "'";
    $update = mysql_query($sqlupdcurrency);
    
    sleep(rand(3,12));
}
?>