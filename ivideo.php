<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]))
	return exit;

class FFMPEG {
	private $executable;

	private function setExecutable($exe)
	{
		$this->executable = $exe;
	}
	public function __construct($exe = "ffmpeg")
	{
		$this->setExecutable($exe);
	}
	public function getSeconds()
	{
		$seconds = 0;
		$this->run(array(
			
		));
	}
	public function run($args = array(), $name = "")
	{
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
	private $raw;
	private $format;
	private $extension;
	
	public function setFormat($fmt) {
		$this->format = $fmt;
	}

	public function split($count = 5)
	{
		$ext = $this->extension;
		$vids = array();
		for($i=0; $i < $count; $i++)
		{
			$seg = sprintf($this->format, sha1($this->video), $i);
			if (file_exists("$seg.$ext"))
			{
				array_push($vids, "$seg.$ext");
				continue;
			}
			$this->run(array(
				'ss'		=> $i * 30,
				't'			=> 1,
				'vcodec'	=> 'copy',
				'an'		=> '',
				'i'			=> $this->video
			), "$seg.$ext");

			if (file_exists("$seg.$ext"))
				array_push($vids, "$seg.$ext");
		}
		
		if ($this->raw == true)
		{
			foreach($vids as $key=>$value)
			{
				$vids[$key] = file_get_contents($value);
				unlink($value);
			}
		}
		return $vids;
	}
	public function __construct($video = "", $executable = "ffmpeg", $raw = false)
	{
		$this->video = $video;
		$this->raw = $raw;
		$this->extension = pathinfo($video, PATHINFO_EXTENSION);
		$this->format = "%s_%d";
		parent::__construct($executable);
	}
};