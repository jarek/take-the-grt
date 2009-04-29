<?php

include_once '../common/headers.php';

class Trip
{
	private $id = false;
	public $route_id;
	public $direction;
	public $headsign;
		
	function __construct($route_id=false, $direction=false, $headsign=false, $trip_id=false)
	{
		if ($route_id !== false && $direction !== false && $headsign !== false)
		{
			// creating route from definition

			if (is_numeric($direction) === false || $direction < 0)
			{
				throw new Exception('Trip name must be a zero-based integer; provided value was ' . $direction);
			}
			
			$this->id = $id;
			$this->route_id = $route_id;
			$this->direction = $direction;
			$this->headsign = $headsign;
		}
		else if ($route_id !== false && is_numeric($route_id) === true)
		{
			$this->id = $id;
			self::ReadFromDatabase();
		}
		else if ($route_id !== false && is_numeric($route_id) === false)
		{
			$sql_data = $route_id;
			self::ReadFromSQLResult($sql_data);
		}
		else
		{
			throw new Exception('Cannot create empty trip');
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
			$result = $Database->Select('SELECT * FROM Trip WHERE id = ?', $this->id);
		}

		if (count($result) < 1)
		{
			throw new Exception('Trip ID ' . $this->id . ' not found in database');
		}
		else if (count($result) > 1)
		{
			throw new Exception('More than one trip with ID ' . $this->id . ' found in database (this breaks referential integrity)');
		}

		self::ReadFromSQLResult($result[0]);
	}

	function ReadFromSQLResult($result)
	{
		$this->id = $result['id'];
		$this->route_id = $result['route_id'];
		$this->direction = $result['direction'];
		$this->headsign = $result['headsign'];
		$this->service_id = $result['service_id'];
	}

	function WriteToDatabase()
	{
		throw new Exception('unimplemented');
	
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

	function GetStopTimes()
	{
		global $Database;

		$res = $Database->Select('SELECT * FROM StopTime WHERE trip_id = ?', $this->id);

		if (count($res) > 0)
		{
			foreach ($res as $time)
			{
				$result[$time['stop_id']] = $time['time'];
			}
		}

		return $result;
	}

	static function GetStopTimesForTrips($trips, $stops)
	{
		// TODO: implement passing of either arrays of stop/trip objects, or stop/trip integer IDs. Preferably interchangeable.
	
		global $Database;

		if (count($trips) < 1)
		{
			return array();
		}

		if (count($stops) < 1)
		{
			return array();
		}

		$query = 'SELECT * FROM StopTime WHERE trip_id IN (';
		foreach ($trips as $trip)
		{
			$sql_param_placeholders[] = '?';
			$sql_params[] = $trip->GetID();
		}
		$query .= implode(',', $sql_param_placeholders) . ') ';

		$query .= ' AND stop_id IN (';
		foreach ($stops as $stop)
		{
			$sql_stop_param_placeholders[] = '?';
			$sql_params[] = $stop->GetID();
		}
		$query .= implode(',', $sql_stop_param_placeholders) . ') ';
		$query .= ' order BY trip_id ASC, time ASC';

		$res = $Database->Select($query, $sql_params);
		
		if (count($res) > 0)
		{
			foreach ($res as $stoptime)
			{
				$triptimes[$stoptime['trip_id']][$stoptime['stop_id']] = $stoptime['time'];
			}
		}

		return $triptimes;
	}

	static function GetTrips($routes, $stops, $direction, $numberOfTrips=false, $startTime=false, $endTime=false)
	{
		// TODO: handling of $endTime in different service period than $startTime
		// TODO: actually decide /how/ before implementing...

		global $Database;
		
		if (is_array($routes) === false)
		{
			$routes = array($routes);
		}

		if (is_array($stops) === false)
		{
			$stops = array($stops);
		}

		if ($numberOfTrips !== false && is_int($numberOfTrips) === false)
		{
			throw new InvalidArgumentException('$numberOfTrips must be false or an integer');
		}

		if ($startTime !== false && (is_numeric($startTime) === false))
		{
			throw new InvalidArgumentException('$startTime must be false or an integer, value provided: ' . $startTime);
		}

		if ($endTime !== false && is_int($endTime) === false)
		{
			throw new InvalidArgumentException('$endTime must be false or an integer, value provided: ' . $endTime);
		}

		$query = 'SELECT *, Trip.id AS id FROM Trip INNER JOIN StopTime ON (StopTime.trip_id = Trip.id) '
			. ' INNER JOIN Stop ON (StopTime.stop_id = Stop.code) '
			. ' INNER JOIN Service ON (Trip.service_id = Service.id) '
			. ' INNER JOIN Route ON (Trip.route_id = Route.id) '
			. ' WHERE Trip.direction = ? ';

		$sql_params[] = $direction;

		foreach ($routes as $route)
		{
			$sql_route_param_placeholders[] = '?';
			if (is_int($route) === true)
			{
				$sql_params[] = $route;
			}
			else if (get_clasS($route) === "Route")
			{
				$sql_params[] = $route->GetID();
			}
			else
			{
				throw new InvalidArgumentException('Invalid argument in $routes: ' . $route);
			}
		}
		$query .= ' AND Route.id IN (' . implode(',', $sql_route_param_placeholders) . ') ';
				
		foreach ($stops as $stop)
		{
			$sql_stop_param_placeholders[] = '?';
			if (is_int($stop) === true)
			{
				$sql_params[] = $stop;
			}
			else if (get_clasS($stop) === "Stop")
			{
				$sql_params[] = $stop->GetID();
			}
			else
			{
				throw new InvalidArgumentException('Invalid argument in $stops: ' . $stop);
			}
		}
		$query .= ' AND Stop.code IN (' . implode(',', $sql_stop_param_placeholders) . ') '; 

//		$servicePeriod = ServicePeriod::GetServicePeriod($startTime);
//		if ($servicePeriod !== false)
//		{

		$query .= ' AND Service.id = ? ';
		$sql_params[] = Page::GetRequestedServicePeriod()->GetID();

//			if (empty($_GET['serviceperiod']) === false)
//			{
//				$sql_params[] = ((int) $_GET['serviceperiod']);
//			}
//			else
//			{
//				$sql_params[] = $servicePeriod->GetID();
//			}
//		}

		if ($startTime !== false)
		{
			$query .= ' AND StopTime.time > ? ';
			$sql_params[] = date('H:i:s', $startTime);
		}

		$query .= ' GROUP BY Trip.id ORDER BY StopTime.time ASC ';
		
		if ($numberOfTrips !== false)
		{
			$query .= ' LIMIT ?';
			$sql_params[] = $numberOfTrips;
		}

		$res = $Database->Select($query, $sql_params);

		// if we don't have enough trips, try to select the first few from next service period
		//if ($numberOfTrips !== false)
		if (false)
		{
			if (count($res) < $numberOfTrips)
			{
				$i = count($sql_params);
				
				$sql_params[$i-1] = $numberOfTrips - count($res);
				if ($startTime !== false)
				{
					$sql_params[$i-2] = '00:00:00';
					$spParamIndex = $i-3;
				}
				else
				{
					$spParamIndex = $i-2;
				}

				$sp = Page::GetRequestedServicePeriod();
				if ($sp !== false)
				{
					$sql_params[$spParamIndex] = $sp->NextID();
				}

				$extraRes = $Database->Select($query, $sql_params);

				if (count($extraRes) > 0)
				{
					$res = array_merge($res, $extraRes);
				}
			}
		}

		if (count($res) > 0)
		{
			foreach ($res as $tripdata)
			{
				$trips[] = new Trip($tripdata);
			}
		}
		else
		{
			$trips = array();
		}

		return $trips;
	}
}

?>
