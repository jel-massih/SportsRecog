<?php
require('database.php');

$xml = simplexml_load_file('http://api.sportsdatallc.org/golf-t1/schedule/pga/2014/tournaments/schedule.xml?api_key=446bzeb39exeh5qkx9s7javf');

foreach($xml->season->tournament as $tournament) {
	addNewTourny($tournament['name'], $tournament['purse'], $tournament['points'], $tournament['start_date'], $tournament->venue['name'], $tournament->venue['city'], $tournament->venue['state']);
}

function addNewTourny($name, $purse, $points, $start_date, $venue_name, $city, $state) {
	global $db_link;
    $name = mysql_escape_string($name);
    $q = "INSERT INTO `playerwall`.`golfschedule` (`name`, `purse`, `points`, `start_date`, `venue_name`, `city`, `state`) VALUES ('$name', '$purse', '$points', '$start_date', '$venue_name', '$city', '$state')";
    $db_link->query($q);
    echo($db_link->error);
}
?>