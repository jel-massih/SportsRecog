<?php
	$img = "http://jel-massih.com/TestingServer/FaceRecog/{F8FDBE98-606C-D58D-E40F-EEEFDE44AD61}images.jpg";
	$file = file_get_contents('http://rekognition.com/func/api/?api_key=swy7MrBTqpV7ex0n&api_secret=jCofd8LwvdvR9xwQ&jobs=face_recognize&urls='.$img.'&name_space=golf&user_id=jason');
	$thing = json_decode($file);
	var_dump($thing->face_detection[0]->img_index);

	var_dump($file);
?>