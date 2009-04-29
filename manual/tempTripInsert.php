<?php

include_once '../common/headers.php';

global $Database;

$res = $Database->Select('SELECT id FROM Trip ORDER BY id DESC LIMIT 1');
$currentTripID = $res[0]['id'] + 1;

$tripsToInsert = 23;

for ($i = $currentTripID; $i < $currentTripID+$tripsToInsert; $i++)
{
//	$Database->Alter('INSERT INTO Trip (route_id, service_id, headsign, direction) VALUES (1,4,"to Fairview Park","1")');
//	$Database->Alter('INSERT INTO Trip (route_id, service_id, headsign, direction) VALUES (4,2,"to ErbConestoga Mall","0")');
//	echo $i . '<br/>';
}

if (isset($_POST['times']))
{
	$current_stop = $_POST['stopnumber'];
	$trip_offset = $_POST['tripnumber'];

	if (is_numeric($current_stop) === false)
	{
		echo 'YOU\'RE DOING IT WRONG! (Stop number must be, well, a number)';
		return;
	}

	if (is_numeric($trip_offset) === false)
	{
		echo 'YOU\'RE DOING IT WRONG! (Trip number must be, well, a number)';
		return;
	}

	$times = explode("\n", $_POST['times']);

	foreach ($times as $time)
	{	
		$timevar = trim($time);
		if (substr($time, strlen($timevar)-1, 1) == ']' && substr($timevar, 0, 1) !== '[') // firefox-style selection
		{
			$new_times[] = substr($time, 0, strpos($timevar, '['));
		}
		else if (strpos($time, '[') === false) // Opera-style selection
		{
			$new_times[] = $time;
		}
	}

	$times = $new_times;

	for ($trip = $trip_offset; $trip < count($times)+$trip_offset; $trip++)
	{
		$time = $times[$trip-$trip_offset];

		if ((strpos($time, 'PM') !== false || substr($time,0,2) === '12') && (substr($time, 0, 2) !== '12' || strpos($time, 'AM') !== false))
		{
			$hours = substr($time, 0, strpos($time, ':'));
			$hours = (int)$hours + 12;
			$time = $hours . '' . substr($time, strpos($time, ':'));
		}
		
		echo 'trip ' . $trip . ' : ' . $time . '<br/>';

//		$Database->Alter('INSERT INTO StopTime (trip_id,stop_id,time) VALUES(?,?,?)', $trip, $current_stop, $time);
	}
}

?>

<form method="post">stop: <input type="text" name="stopnumber" id="stopnumber" />
<br />starting trip: <input type="text" name="tripnumber" id="tripnumber" value="<?= $_POST['tripnumber']; ?>" />
<br />stop info: <textarea name="times" id="times"></textarea><input type="submit" /></form>
