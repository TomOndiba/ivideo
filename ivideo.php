<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]))
	return exit;

class FFMPEG {
	private $executable;

	private function setExecutable($exe)
	{
		$this->executable = $exe;
	}
	public function __construct()
	{
		$this->setExecutable("ffmpeg");
	}
	public function getSeconds()
	{
		$seconds = 0;
		$this->run(array(
			
		));
	}
	public function run($args = array(), $name = "")
	{
		print_r($args);
		$strarg = $this->executable;
		foreach($args as $key=>$value)
			$strarg .= " -$key $value";
		$strarg .= " $name";

		exec($strarg, $output);
		return join($output, "\n");
	}
};

class IVideo extends FFMPEG {
	private $video;
	
	public function split($count = 5)
	{
		$vids = array();
		for($i=0; $i < $count; $i++)
		{
			if (file_exists("video_$i.mp4"))
				unlink("video_$i.mp4");

			print $this->run(array(
				'i'			=> 'video.mp4',
				'ss'		 => $i * 2,
				't'			=> 2,
				'vcodec'	=> 'copy',
				'an'		=> ''
			), "video_$i.mp4");
			array_push($vids, "video_$i.mp4");
		}
		return $vids;
	}
	public function __construct($video = "")
	{
		$this->video = $video;
		parent::__construct();
	}
}