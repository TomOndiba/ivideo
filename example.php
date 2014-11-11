<?php
require_once("ivideo.php");

$video = new IVideo("video.mp4");
$splitted = $video->split();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Thumbnail Example</title>
	</head>
	
	<body>
		<video src="<?php echo $splitted; ?>" autoplay/>
	</body>
</html>