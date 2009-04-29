<?php

include_once '../common/headers.php';

class Direction 
{
	private $id = false;
	public $route_id;
	public $direction;
	public $description;
		
	function __construct($route_id=false, $direction=false, $description=false, $id=false)
	{
		if ($route_id !== false && $direction !== false && $description !== false)
		{
			// creating route from definition

			if (is_numeric($direction) === false || $direction < 0)
			{
				throw new Exception('Direction name must be a zero-based integer; provided value was ' . $direction);
			}
			
			$this->id = $id;
			$this->route_id = $route_id;
			$this->direction = $direction;
			$this->description = $description;
		}
		else if ($id !== false)
		{
			$this->id = $id;
			self::ReadFromDatabase();
		}
		else
		{
			throw new Exception('Cannot create empty direction');
		}
	}

	function ReadFromDatabase()
	{
		global $Database;
	
		if (isset($this->id) == false)
		{
			throw new Exception('Must specify direction ID for read from database');
		}

		if (isset($this->id))
		{
			$result = $Database->Select('SELECT * FROM RouteDirection WHERE id = ?', $this->id);
		}

		if (count($result) < 1)
		{
			throw new Exception('Direction ID ' . $this->id . ' not found in database');
		}
		else if (count($result) > 1)
		{
			throw new Exception('More than one direction with ID ' . $this->id . ' found in database (this breaks referential integrity)');
		}

		$this->id = $result[0]['id'];
		$this->route_id = $result[0]['route_id'];
		$this->direction = $result[0]['direction'];
		$this->description = $result[0]['description'];
	}

	function WriteToDatabase()
	{
		global $Database;
		
		$id = $this->GetID();
		if ($id === false)
		{
			$query = 'INSERT INTO RouteDirection (route_id, direction, description) VALUES (?,?,?)';
			$success = $Database->Alter($query, $this->route_id, $this->direction, $this->description);
			
			if ($success == true)
			{
				$this->id = $Database->GetLastInsertID();
			}
		}
		else
		{
			$query = 'INSERT INTO RouteDirection (id, route_id, direction, description) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE id = VALUES(id), route_id = VALUES(route_id), direction = VALUES(direction), description = VALUES(description)';
			$success = $Database->Alter($query, $id, $this->route_id, $this->direction, $this->description);
		}

		if ($success === false)
		{
			throw new Exception(sprintf('Unable to write direction to database: %s', $Database->Error()));
		}
	}

	function ToString()
	{
		return 'route ' .$this->route_id . ' direction ' . $this->direction . ': ' . $this->description . ' (internal ID ' . $this->id . ')';
	}

	function GetID()
	{
		return $this->id;
	}

	function GetStops()
	{
		global $Database;
		$res = $Database->Select('SELECT * FROM RouteDirectionStop LEFT JOIN Stop ON (Stop.code = RouteDirectionStop.stop_id) WHERE route_direction_id = ? ORDER BY sequence ASC', $this->id);

		if (count($res) > 0)
		{
			foreach ($res as $stop)
			{
				$stops[] = new Stop($stop);
			}
		}
		
		return $stops;
	}
	
	function AddStop($stop, $sequence)
	{	
		global $Database;
		
		if (is_numeric($stop) === false && is_int($stop) === false)
		{
			throw new Exception('"' . $stop . '" is not a valid stop ID');
		}
		
		$stopQuery = 'INSERT INTO Stop (code, latitude, longitude) VALUES (?,0,0) ON DUPLICATE KEY UPDATE latitude = 0';

		if ($Database->Alter($stopQuery, $stop)) {
			$dirStopQuery = 'INSERT INTO RouteDirectionStop (route_direction_id, stop_id, sequence, timepoint) VALUES (?,?,?,0)';

			if ($Database->Alter($dirStopQuery, $this->GetID(), $stop, $sequence) === false) {
				throw new Exception(sprintf('Cannot create RouteDirectionStop for stop_id %d, dir_id %d: %s', $stop, $this->GetID(), $Database->Error()));
			}
		}
		else
		{
			throw new Exception(sprintf('Cannot create/update Stop for stop_id %i', $stop));
		}
	}
	
	function DeleteStop($stop)
	{
		throw new Exception('DeleteStop() not yet implemented');
		
	/*	delete from RouteDirectionStop
		if there are no other RouteDirectionStops referencing this Stop, perhaps Stop->Delete? Or maybe not.*/
	}
	
	function Delete()
	{
		throw new Exception('Deleting directions is not yet implemented');
		
	/*	sample code here:
		$stops = $this->GetStops();
		if (count($stops) > 0)
		{
			foreach ($stops as $stop)
			{
				$this->DeleteStop($stop);
			}
		}
		
		$deleteQuery = 'DELETE FROM RouteDirection WHERE etc';
		return $Database->Alter($dirStopQuery, $this->GetID(), $stop, $sequence);*/
	}
}

?>
