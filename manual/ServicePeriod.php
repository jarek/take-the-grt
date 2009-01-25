<?php

include_once 'headers.php';

class ServicePeriod
{
	private $id;

	public $name;
	
	public $startdate;
	public $enddate;

	public $weekday;
	public $saturday;
	public $sunday;

	function __construct($id, $name, $startdate, $enddate, $weekday, $saturday, $sunday)
	{
		$this->id = $id;
	
		$this->name = $name;
		$this->startdate = $startdate;
		$this->enddate = $enddate;

		$this->weekday = $weekday;
		$this->saturday = $saturday;
		$this->sunday = $sunday;
	}

	public function ToString()
	{
		$res = $this->name;
		$res .= ' (' . ($this->IsActiveRightNow() ? 'Active' : 'Inactive') . ')';
		return $res;
	}

	public function GetID()
	{
		return $this->id;
	}

	public function IsActiveRightNow()
	{
		$now = self::GetDateInFormat();

		if ((strcmp($now, $this->startdate) > 0) && (strcmp($now, $this->enddate) < 0))
		{
			$weekday = date('N');
			if ($weekday < 6)
			{
				if ($this->weekday)
				{
					return true;
				}
			}
			else if ($weekday < 7)
			{
				if ($this->saturday) 
				{
					return true;
				}
			}
			else
			{
				if ($this->sunday)
				{
					return true;
				}
			}
		}

		return false;
	}

	public static function GetDateInFormat($date=false)
	{
		if ($date === false)
		{
			return date('Y-m-d H:i:s');
		}
		else
		{
			return date('Y-m-d H:i:s', $date);
		}
	}

	public static function GetAllServicePeriods()
	{
		global $Database;
		$results = $Database->Select('SELECT * FROM Service');

		foreach ($results as $result)
		{
			$sp[] = new ServicePeriod($result['id'], $result['name'], $result['start'], $result['stop'], $result['weekday'], $result['saturday'], $result['sunday']);
		}

		return $sp;
	}
}
