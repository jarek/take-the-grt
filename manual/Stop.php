<?php

class Stop
{
	public $id;
	public $code;
	public $name;
	public $description;
	
	public $longitude;
	public $latitude;

	public $timepoint;

	public $type = 0;


	function __construct($id=false, $lat=false, $lon=false, $name=false, $description=false, $timepoint=false)
	{
		if ($id !== false && $lat !== false && $lon !== false && $name !== false && $description !== false && $timepoint !== false)
		{
			$this->id = $id;
			$this->code = $id;

			$this->name = $name;
			$this->description = $description;

			$this->latitude = $lat;
			$this->longitude = $lon;

			$this->timepoint = $timepoint;
		}
		else
		{
			throw new Exception('No other constructors created yet');
		}
	}
}

?>
