<?php
$ch = curl_init("https://api.att.com/speech/v3/speechToText");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer icvtjurFRWlMeSXuA1uqeuoKzrHAoAOR', 'Content-type: audio/wav', 'Accept: application/json', 'X-SpeechContext: Generic'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($_POST["RecordingUrl"]));
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result);
$word = "";
foreach($data->Recognition->NBest[0]->Words as $word) {
	$word = strtolower($word);

	$fh = fopen('pimp.log', 'a+');
	if ( $fh )
	{
		fwrite($fh, print_r($word."__", true));
		fclose($fh);
	}
	

	if($word == "seat" || $word == "seats" || $word == "seating") {
		$ch = curl_init("http://playerrecog.nodejitsu.com/callbacks/event/seats?url=".urlencode($_GET['seat']));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_exec($ch);
		curl_close($ch);
		break;
	}

	if($word == "parking" || $word == "garage" || $word == "park" || $word == "target") {
		$ch = curl_init("http://playerrecog.nodejitsu.com/callbacks/event/parking?url=".urlencode($_GET['park']));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_exec($ch);
		curl_close($ch);
		break;
	}
}
?>