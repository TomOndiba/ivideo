<?php
require_once("ivideo.php");

$video = new IVideo("video.mp4");
$splitted = $video->split();

foreach($splitted as $key=>$value)
	echo "<video src='$value' autoplay controls/></video>";