<?php
require_once("ivideo.php");

$video = new IVideo("video.mp4");
$splitted = $video->split();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Thumbnail Example</title>
		<script type="text/javascript">
			thumbs = {
				data: <?php echo json_encode($splitted); ?>,
				setup: function(element) {
					var self = this;
					var video = document.createElement("video");
					element.appendChild(video);

					video.setAttribute("stage", 0);
					video.src = this.data[video.getAttribute("stage")];

					video.width = element.offsetWidth;
					video.height = element.offsetHeight;
					video.addEventListener("ended", function() {
						video.setAttribute("stage", (parseInt(video.getAttribute("stage")) + 1) % self.data.length);
						video.src = self.data[video.getAttribute("stage")];
						video.play();
					});
					element.addEventListener("mouseover", function() { video.play(); });
					element.addEventListener("mouseout", function() { video.pause(); });
				},
				initialise: function() {
					this.canvas = document.getElementsByClassName("thumb")[0];
					this.setup(this.canvas);
				}
			};
			window.addEventListener("load", function() {
				thumbs.initialise();
			});
		</script>
		<style type="text/css">
			.thumb {
				width:278px;
				height:185px;
				background-color:#000;
			}
		</style>
	</head>
	
	<body>
		<div class="thumb"></div>
	</body>
</html>