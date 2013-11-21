<?php
include('SimpleImage.php');
require('database.php');
require "Services/Twilio.php";

$fh = fopen('parse.log', 'a+');

if($fh) {
	$_POST['subject'] = strtolower($_POST['subject']);
	$filenames = array();
	$fileoff = 0;
	$image = new SimpleImage();
	foreach($_FILES as $key => $file)
	{
		$guid = getGuid();
		move_uploaded_file($file['tmp_name'], $guid.$file['name']);
		$filename = $guid.$file['name'];
		$image->load($filename);
		$image->resizeToHeight(800);
		$image->save($filename);
		$filenames[$fileoff] = $filename;
		if($_POST['subject'] == "train") {
			$img = "http://jel-massih.com/TestingServer/FaceRecog/".$filename;
			$file = file_get_contents('http://rekognition.com/func/api/?api_key=swy7MrBTqpV7ex0n&api_secret=jCofd8LwvdvR9xwQ&jobs=face_add_[tiger_woods]&urls='.$img.'&name_space=golf&user_id=jason');
			continue;
		}
	}

	

	if($_POST['subject'] == "train") {
		return;
	}

	$img = "http://jel-massih.com/TestingServer/FaceRecog/".$filenames[0];
	$file = file_get_contents('http://rekognition.com/func/api/?api_key=swy7MrBTqpV7ex0n&api_secret=jCofd8LwvdvR9xwQ&jobs=face_recognize&urls='.$img.'&name_space=golf&user_id=jason');
	$thing = json_decode($file);

	$seatingString="";
	$parkingString="";
	$response = "";
	$highname = "";
	$highconf = 0.0;
	foreach($thing->face_detection as $face) {
		foreach($face->matches as $match) {
			$conf = floatval($match->score);
			if($conf > 0.15 && $conf > $highconf) {
				$highname = $match->tag;
				$highconf = $conf;
			}
		}
	}

	if($highname != "") {
		$playername = "";
		switch($highname) {
			case "tiger_woods":
				$playername = "Tiger Woods";
				break;
			case "phil_mickelson":
				$playername = "Phil Mickelson";
				break;
		}
		$response = "You have Scanned: ".$playername."\n\n";
		$scanname = $response;
		$response = $response."Upcoming Events Are: \n";
		$eventstring = "Upcoming Events Are: _";
		$upcoming = "Upcoming Events Are: \n";
		$q = "SELECT * from golfschedule";
		if($result = $db_link->query($q)) {
			$limit = 0;
			while($row = $result->fetch_assoc()) {
				if(time() < strtotime($row['start_date']) && $limit	< 3) {
					$response = $response.$row['name']."\n";
					$eventstring = $eventstring.$row['name']."_";
					$response = $response."Location: ".$row['venue_name'].", ".$row['city'].", ".$row['state']."\n";
					$shortresponse = $shortresponse.$row['name']."\n";
					if($limit == 0) {
						$seatingString = askSeatGeek(urlencode($row['name']));
						$parkingString = askParkWhiz(urlencode($row['venue_name'].", ".$row['city']), strtotime($row['start_date']));
					}
					$response = $response."Ticket Link: ".askSeatGeek(urlencode($row['name']));
					$response = $response."Parking Link: ".askParkWhiz(urlencode($row['venue_name'].", ".$row['city']), strtotime($row['start_date']));
					$response = $response."\n";
					$limit++;
				}
			}
		}
	}

	if($response == "") {$response="No Known Player Detected!";}
	//echo($response);
	fwrite($fh, print_r($thing, true));
	mail($_POST['from'], "Your Request!", $response);
	if(strtolower($_POST['subject']) == "sms") {
		sendSms($_POST['text'], substr($scanname.$upcoming.$shortresponse, 0, 160));//$_POST['text']);
	} else if (strtolower($_POST['subject']) == "call") {
		makeCall($_POST['text'], urlencode($scanname), urlencode($eventstring), urlencode($seatingString), urlencode($parkingString));
		//makeCall('5084791878', urlencode($scanname), urlencode($eventstring), urlencode($seatingString), urlencode($parkingString));
	}
} 	

function getGuid() {
	 mt_srand((double)microtime()*10000);
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = chr(123)// "{"
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        .chr(125);// "}"
    return $uuid;
}

function askSeatGeek($query) {
	
	$seatfile = file_get_contents('http://api.seatgeek.com/2/events?q='.$query);
	$seat = json_decode($seatfile);
	if($seat->events[0]->url == "") {
		return "";
	}
	return $seat->events[0]->url."\n";
}

function askParkWhiz($query, $start) {
	$parkfile = file_get_contents("http://api.parkwhiz.com/search/?destination=".$query."&start=".$start."&key=62d882d8cfe5680004fa849286b6ce20");
	$park = json_decode($parkfile);
	if($park->parkwhiz_url == "") {
		return "";
	}
	return $park->parkwhiz_url."\n";
}

function sendSms($reciever, $message) {
	$AccountSid = "########################";
	$AuthToken = "###########################";
	$client = new Services_Twilio($AccountSid, $AuthToken);
	$sms = $client->account->sms_messages->create("5083156274", $reciever, $message, array());
}

function makeCall($reciever, $scanname, $message, $seaturl, $parkurl) {
	$sid = '######################';
	$token = '#######################';

	$client = new Services_Twilio($sid, $token);
	try {
	$call = $client->account->calls->create(
	'5083156274', 
	$reciever, 
	'http://jel-massih.com/TestingServer/FaceRecog/calltext.php?intro='.$scanname.'&text='.$message."&seaturl=".$seaturl."&parkurl=".$parkurl
	);
	echo 'Started call: ' . $call->sid;
	} catch (Exception $e) {
	echo 'Error: ' . $e->getMessage();
	}
}
?>