
<?php
$ch = curl_init("http://playerrecog.nodejitsu.com/callbacks/event/seats/?url=http://seatgeek.com/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_exec($ch);
curl_close($ch);
?>
