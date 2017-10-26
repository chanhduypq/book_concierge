<?php
$con = mysql_connect('localhost', 'i12reader', 'Y1>03$5&,}5b7/G');
$db = mysql_select_db('bkdata_486');
if ($con && $db) {
    // echo"connected database........";
} else {
    echo "Problem In connection " . die(mysql_error());
}
?>

