<?php

require('database.php');

$q = "SELECT * from golfschedule";
if($result = $db_link->query($q)) {
	while($row = $result->fetch_assoc()) {
		if(time() < strtotime($row['start_date'])) {
			echo($row['name']);
		}
	}
}
?>