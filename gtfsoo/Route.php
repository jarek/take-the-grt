<?php

class Route 
{
	private $id;
	public $short_name;
	public $long_name;
	
	public $type = 3; // all GRT routes are bus routes at this time
	
	function __construct($short_name=false, $long_name=false, $id=false)
	{
		if ($id !== false && $short_name !== false && $long_name !== false)
		{
			// creating route from definition

			$this->id = $id;
			$this->short_name = $short_name;
			$this->long_name = $long_name;
		}
		else if ($short_name !== false && is_numeric($short_name) === true)
		{
			$this->short_name = $short_name;
			self::ReadFromDatabase();
		}
		else if ($short_name !== false)
		{
			self::ReadFromSQLResult($short_name);
		}
		else if ($id !== false)
		{
			$this->id = $id;
			self::ReadFromDatabase();
		}
		else
		{
			throw new Exception('Creating empty routes not allowed');
		}
	}

	function ReadFromDatabase()
	{
		global $Database;
	
		if (!isset($this->short_name) && !isset($this->id))
		{
			throw new Exception('Must specify route number or ID for read from database');
		}

		if (isset($this->short_name))
		{
			$result = $Database->Select('SELECT * FROM Route WHERE short_name = ?', $this->short_name);
		}
		else if (isset($this->id))
		{
			$result = $Database->Select('SELECT * FROM Route WHERE id = ?', $this->id);
		}

		if (count($result) < 1)
		{
			throw new Exception('Route ' . $this->short_name . ' not found in database');
		}
		else if (count($result) > 1)
		{
			throw new Exception('More than one route named ' . $this->short_name . ' found in database');
		}

		self::ReadFromSQLResult($result[0]);
	}

	function ReadFromSQLResult($result)
	{
		$this->id = $result['id'];
		$this->short_name = $result['short_name'];
		$this->long_name = $result['long_name'];
	}

	function WriteToDatabase()
	{
		global $Database;
		$query = 'INSERT INTO Route (id, short_name, long_name) VALUES (?,?,?) ON DUPLICATE KEY UPDATE id = VALUES(id), short_name = VALUES(short_name), long_name = VALUES(long_name)';
		$success = $Database->Alter($query, $this->GetID(), $this->short_name, $this->long_name);

		if (success === false)
		{
			throw new Exception('Unable to write to database');
		}
	}

	static function WriteGTFSHeader()
	{
		return "route_id,route_short_name,route_long_name,route_type\n";
	}

	function WriteToGTFS()
	{
		$fields = array($this->GetID(), $this->short_name, $this->long_name, $this->type);
		return implode(',', $fields) . "\n";
	}

	function ToString()
	{
		return 'Route ' . $this->short_name . ': ' . $this->long_name . ' (internal ID ' . $this->id . ')';
	}

	function GetID()
	{
		return $this->id;
	}

	function GetDirections()
	{
		global $Database;
		$res = $Database->Select('SELECT * FROM RouteDirection WHERE route_id = ?', $this->id);
		
		foreach ($res as $dir)
		{
			$directions[] = new Direction($dir['route_id'], $dir['direction'], $dir['description'], $dir['id']);
		}
		
		return $directions;
	}

	function GetTrips($direction=false)
	{
		global $Database;

		if ($direction === false)
		{
			$res = $Database->Select('SELECT * FROM Trip WHERE route_id = ?', $this->id);
		}
		else 
		{
			$res = $Database->Select('SELECT * FROM Trip WHERE route_id = ? AND direction = ?', $this->id, $direction);
		}

		if (count($res) > 0)
		{
			foreach ($res as $trip)
			{
				$trips[] = new Trip($trip);
			}
		}	

		return $trips;
	}

	function GetTripTimes($direction)
	{
		global $Database;

		if (($direction !== 0) || ($direction !== 1))
		{
			throw new Exception('Invalid direction value specified');
		}

		$query = 'SELECT * FROM Trip INNER JOIN StopTime ON (trip_id = Trip.id) WHERE route_id = ? AND direction = ?';
		$query .= ' ORDER BY time';

		$res = $Database->Select($query, $this->id, $direction);

		if (count($res) > 0)
		{
			foreach ($res as $info)
			{
				$triptimes[$info['trip_id']][$info['stop_id']] = $info['time'];
			}
		}

		return $triptimes;
	}

	static function GetAllRouteShortNames()
	{
		global $Database;
		$routes = $Database->Select('SELECT short_name FROM Route');

		foreach ($routes as $route_info)
		{
			$result[] = $route_info['short_name'];
		}
		
		return $result;
	}

	static function GetAllRoutes()
	{
		global $Database;
		$routes_info = $Database->Select('SELECT * FROM Route');
		foreach ($routes_info as $route_info)
		{
			$routes[] = new Route($route_info['short_name'], $route_info['long_name'], $route_info['id']);
		}
	
		return $routes;
	}

}

?>
