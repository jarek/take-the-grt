<?php

class Stop
{
	public $id;
	public $code;
	public $name;
	public $friendlyname;
	public $direction;
	
	public $longitude;
	public $latitude;

	public $type = 0;


	function __construct($id=false, $lat=false, $lon=false, $name=false, $friendlyname=false, $direction=false)
	{
		if ($id !== false && $lat !== false && $lon !== false && $name !== false && $friendlyname !== false && $direction !== false)
		{
			$this->id = $id;
			$this->code = $id;

			$this->name = $name;
			$this->friendlyname = $friendlyname;
			$this->direction = $direction;

			$this->latitude = $lat;
			$this->longitude = $lon;
		}
		else if ($id !== false && is_numeric($id))
		{
			// query database
			$this->id = $id;
			$this->code = $id;

			self::ReadFromDatabase();
		}
		else if ($id !== false)
		{
			self::ReadFromSQLResult($id);
		}
		else
		{
			throw new Exception('No other constructors created yet');
		}
	}

	function ReadFromDatabase()
	{
		global $Database;

		if (isset($this->id) == false)
		{
			throw new Exception('Must specify stop ID for read from database');
		}

		if (isset($this->id))
		{
			$result = $Database->Select('SELECT * FROM Stop WHERE code = ?', $this->id);
		}

		if (count($result) < 1)
		{
			throw new Exception('Stop ID ' . $this->id . ' not found in database');
		}
		else if (count($result) > 1)
		{
			throw new Exception('More than one stop with ID ' . $this->id . ' found in database (this breaks referential integrity)');
		}

		self::ReadFromSQLResult($result[0]);

	}

	function ReadFromSQLResult($result)
	{
		$this->id = $result['code'];
		$this->code = $result['code'];
	
		$this->name = $result['name'];
		$this->friendlyname = $result['friendlyname'];
		$this->direction = $result['direction'];
	
		$this->latitude = $result['latitude'];
		$this->longitude = $result['longitude'];
	}

	function GetID()
	{
		return $this->id;
	}

	function ToString($verbose=true)
	{
		$string = '<small>' . $this->id;

		if (empty($this->name) === false || (empty($this->latitude) === false && empty($this->longitude) === false))
		{
			$string .= ' <a href="' . $this->GoogleMapUrl() . '">';
			if (empty($this->latitude) === false && empty($this->longitude) === false)
			{
				$string .= 'map';
			}
			else
			{
				$string .= 'map?';
			}
			$string .= '</a>';
		}
		$string .= '</small> <br />';

		if (empty($this->friendlyname) === false)
		{
			$string .= $this->friendlyname;
		}
		else
		{
			$string .= $this->name;
		}

		if ($verbose == true && empty($this->direction) === false)
		{
			$string .= ' to ' . $this->direction;
		}

		return $string;
	}

	function GoogleMapUrl()
	{
		if (empty($this->latitude) || empty($this->longitude))
		{
			$address = str_replace('/', 'and', $this->name);
			return 'http://maps.google.ca/?q=' . $address . ', waterloo, on&amp;z=16';
		}
		else
		{
			return 'http://maps.google.ca/?q=' . $this->latitude . ',' . $this->longitude . '&amp;z=18';
		}
	}

	function GetRoutesForStop()
	{
		global $Database;
	
		$query = 'SELECT Route.id,Route.short_name,Route_long_nameshort_name,RouteDirection.description '
			. ' FROM Route INNER JOIN RouteDirection ON (route_id = Route.id) '
			. ' INNER JOIN RouteDirectionStop ON (route_direction_id = RouteDirection.id) '
			. ' INNER JOIN Stop ON (stop_id = Stop.code) '
			. ' WHERE Stop.code = ?';

		$result = $Database->Select($query, $this->id);

		if (count($result) > 0)
		{
			foreach ($result as $stop)
			{
				$routes[] = array(new Route($stop), $stop['description']); 
			}
		}

		return $routes;
	}

	static function CreateStopsFromList($stop_ids)
	{
		global $Database;

		$query = 'SELECT * FROM Stop WHERE code IN (';
		foreach ($stop_ids as $stop)
		{
			$sql_param_placeholders[] = '?';
		}
		$query .= implode(',', $sql_param_placeholders) . ')';

		$result = $Database->Select($query, $stop_ids);

		if (count($result) > 0)
		{
			foreach ($result as $stopdata)
			{
				if (($index = array_search($stopdata['code'], $stop_ids)) !== false)
				{
					// TODO: this method could potentially get slow. try to find a better way to do it
					$list[$index] = new Stop($stopdata);
				}
			}
			ksort($list); // sort by key to get proper stop order
		}

		return $list;
	}
}

?>
