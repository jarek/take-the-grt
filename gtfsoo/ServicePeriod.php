<?php

include_once '../common/headers.php';

// TODO: exclusions, holidays, etc

class ServicePeriod
{
	private $id;

	public $name;
	
	public $startdate;
	public $enddate;

	public $weekday;
	public $saturday;
	public $sunday;

	static $currentSP = 0;
	static $currentSPtimestamp = 0;
	

	function __construct($id=false, $name=false, $startdate=false, $enddate=false, $weekday=false, $saturday=false, $sunday=false)
	{
		if ($name === false)
		{
			if (is_numeric($id) !== false)
			{
				$this->id = $id;
				self::ReadFromDatabase();
			}
			else
			{
				self::ReadFromSQLResult($id);
			}
		}
		else
		{
			$this->id = $id;
		
			$this->name = $name;
			$this->startdate = $startdate;
			$this->enddate = $enddate;

			$this->weekday = $weekday;
			$this->saturday = $saturday;
			$this->sunday = $sunday;
		}
	}

	function ReadFromDatabase()
	{
		global $Database;
	
		if (isset($this->id) == false)
		{
			throw new Exception('Must specify trip ID for read from database');
		}

		if (isset($this->id))
		{
			$result = $Database->Select('SELECT * FROM Service WHERE id = ?', $this->id);
		}

		if (count($result) < 1)
		{
			throw new Exception('ServicePeriod ID ' . $this->id . ' not found in database');
		}
		else if (count($result) > 1)
		{
			throw new Exception('More than one service period with ID ' . $this->id . ' found in database (this breaks referential integrity)');
		}

		self::ReadFromSQLResult($result[0]);
	}

	function ReadFromSQLResult($result)
	{
		$this->id = $result['id'];
	
		$this->name = $result['name'];
		$this->startdate = $result['start'];
		$this->enddate = $result['stop'];

		$this->weekday = $result['weekday'];
		$this->saturday = $result['saturday'];
		$this->sunday = $result['sunday'];
	}

	public function ToString()
	{
		$res = $this->name;
		$res .= ' (' . ($this->IsActive() ? 'Active' : 'Inactive') . ')';
		return $res;
	}

	public function GetID()
	{
		return $this->id;
	}

	public function Next()
	{
		throw new Exception('ServicePeriod->Next() is not yet implement. Check NextID(), which might do the trick');

		// TODO: implement this, in the smartest, least query-intensive way possible
	}

	public function NextID()
	{
		if ($this->id < 4)
		{
			return $this->id + 1;
		}
		else
		{
			return 2;
		}

		// TODO: these are details specific to our database. this is needless to say ugly. Think of a better way to do this
		// will probably require a database call, but try to be as thrifty as possible.
	}

	public function IsActive($date=false)
	{
		// specify $date == false to look if a service period is active right now
	
		if ($date !== false && is_int($date) === false)
		{
			throw new InvalidArgumentException('$date must be false or a valid UNIX integer timestamp, value provided: ' . $date);
		}
	
		$now = self::GetDateInFormat($date);

		if ((strcmp($now, $this->startdate) > 0) && (strcmp($now, $this->enddate) < 0))
		{
			// check for validity within the period
			if ($date === false)
			{
				$weekday = date('N');
				$hour = date('H');
			}
			else
			{
				$weekday = date('N', $date);
				$hour = date('H', $date);
			}

			// TODO: this piece of code has caused me debugging pain twice already.
			// possibly rewrite in a less bug-inducing way. ~qviri 2009.02.23
			if (($weekday < 6 && !($weekday == 1 && $hour < 5)) || ($weekday == 6 && $hour < 5))
			{
				if ($this->weekday)
				{
					return true;
				}
			}
			else if ($weekday == 6 || (($weekday == 7) && ($hour < 5)))
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
		$results = $Database->Select('SELECT * FROM Service ORDER BY sort ASC');

		foreach ($results as $result)
		{
			$sp[] = new ServicePeriod($result['id'], $result['name'], $result['start'], $result['stop'], $result['weekday'], $result['saturday'], $result['sunday']);
		}

		return $sp;
	}

	public static function GetServicePeriod($date=false)
	{
		// specify $date == false to get current service period
	
		if ($date !== false && is_int($date) === false)
		{
			throw new InvalidArgumentException('$date must be false or a valid UNIX integer timestamp, value provided: ' . $date);
		}

		if (($date === false || ($date - 10*60) <= self::$currentSPtimestamp) && self::$currentSPtimestamp <= time() - 10*60)
		{
			// early return without querying to save time
			return self::$currentSP;
		}

		$servicePeriods = self::GetAllServicePeriods();

		foreach ($servicePeriods as $sp)
		{
			if ($sp->IsActive($date) === true)
			{
				if ($date === false || $date >= time() - 10*60)
				{
					// if we were looking for the *current* ServicePeriod,
					// save it for future references within the same page load
					self::$currentSP = $sp;
					self::$currentSPtimestamp = time();
				}

				return $sp;
			}
		}

		return false; // no valid service period for requested/current timestamp was found
	}
}
