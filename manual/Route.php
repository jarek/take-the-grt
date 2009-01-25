<?php

include_once 'headers.php';

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
		else if ($short_name !== false)
		{
			$this->short_name = $short_name;
			self::ReadFromDatabase();
		}
		else if ($id !== false)
		{
			$this->id = $id;
			self::ReadFromDatabase();
		}
		else
		{
			//print "creating empty route";
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

		$this->id = $result[0]['id'];
		$this->short_name = $result[0]['short_name'];
		$this->long_name = $result[0]['long_name'];
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
