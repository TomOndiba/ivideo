<?php
namespace IVideo;

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
	public function run($args = array(), $name = "")
	{
		$strarg = $this->executable;
		foreach($args as $key=>$value)
			$strarg .= " -$key" . (($value == "") ? "" : " $value");
			
		if ($name)
			$strarg .= " $name";

		exec($strarg, $output);
		return join($output, "\n");
	}
};

class Thumbnail extends FFMPEG {
	private $video;
	private $format;
	private $size;
	
	public function setFormat($fmt)
	{
		$this->format = $fmt;
	}
	public function getSeconds()
	{
		/***********************
		 	## reference ##
			ffmpeg -i file.flv 2>&1 | grep "Duration"| cut -d ' ' -f 4 | sed s/,// | sed 's@\..*@@g' | awk '{ split($1, A, ":"); split(A[3], B, "."); print 3600*A[1] + 60*A[2] + B[1] }'
		************************/
		$seconds = 0;
		return (int)($this->run(array(
			'i' => $this->video,
		), '2>&1 | grep "Duration"| cut -d \' \' -f 4 | sed s/,// | sed \'s@\..*@@g\' | awk \'{  split($1, A, ":"); split(A[3], B, "."); print 3600*A[1] + 60*A[2] + B[1] }\''));
	}
	public function split($count = 5, $output = false, $raw = false)
	{
		$seconds = $this->getSeconds();
		$distance = floor($seconds / $count);

		if (!$output)
			$output = "thumb-" . md5($this->video) . ".webm";

		if (file_exists($output))
			unlink($output);

		/* Segment video out */
		$segments = array();
		for($i=0; $i < $count; $i++)
		{
			$name = "/tmp/ivideo$i" . md5($this->video . time()) .".mp4";
			if (file_exists($name))
				unlink($name);

			$this->run(array(
				'i' => $this->video,
				'ss' 	=> ($i * $distance) + 1,
				't' 	=> 1,
				'c:v' 	=> 'libx264',
				'an' 	=> '',
				'vf' 	=> 'scale=' . $this->size[0] . ':' . $this->size[1]
			), $name);
			array_push($segments, $name);
		}

		/* Concat video into suitable format */
		$list = array();
		foreach($segments as $key=>$value)
			array_push($list, "file '$value'");

		file_put_contents("/tmp/ivideo.txt", join($list, "\n"));
		
		$this->run(array(
			'f' => 'concat',
			'i' 	=> '/tmp/ivideo.txt',
			'c:v' 	=> 'libvpx',
			'b:v' 	=> '1M',
			'crf' 	=> 10,
			'an' 	=> '',
			'vf' 	=> 'scale=' . $this->size[0] . ':' . $this->size[1]
		), $output);
		
		/* Cleanup */
		foreach($segments as $key=>$value)
			unlink($value);

		if ($raw == true)
		{
			$data = file_get_contents($output);
			unlink($output);
			$output = $data;
		}
		unlink("/tmp/ivideo.txt");
		return $output;
	}
	public function __construct($video = "", $size = array(300,200), $executable = "ffmpeg")
	{
		$this->video 	= $video;
		$this->size 	= $size;
		$this->format 	= "%s_%d";
		parent::__construct($executable);
	}
};
