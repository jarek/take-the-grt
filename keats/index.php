<?php

include_once '../common/headers.php';
include_once 'header.php';

if (Page::IsShowingFullSchedule())
{
	$limit = false;
	$time = false;
}
else
{
	$limit = 5; // default;
	$time = time(); // show stuff just passed also just in case it turns out to be useful; 5 minutes

	if (date('H') < 5) // before four a.m., rewind to the previous days' schedule
	{
		$time = mktime(23, 58, 0, date('m'), date('d')-1, date('Y'));
	}
}

// full lists of stops in both directions
//$inbound_stop_ids = array(3148, 3149, 3150, 3151, 3152, 3153, 3154, 3631, 2780, 2515, 2781, 2782, 2783, 3620, 2785);
//$outbound_stop_ids = array(3621, 3619, 2673, 2674, 2675, 2676, 2515, 2677, 3589, 3155, 3156, 3157, 3158, 3159, 3160, 3161);

// truncated lists, skipping a few
$inbound_stop_ids = array(3148, 3149, 3150, 3151, 3152, 3153, 3154, 2515, 2781, 2782, 3620);
$outbound_stop_ids = array(3619, 2673, 2675, 2676, 2515, 2677, 3155, 3156, 3158, 3159, 3161);

$inbound_stops = Stop::CreateStopsFromList($inbound_stop_ids);
$outbound_stops = Stop::CreateStopsFromList($outbound_stop_ids);

$routes = array(new Route(12), new Route(29));

$inbound_trips = Trip::GetTrips($routes, $inbound_stops, '0', $limit, $time);
$inbound_times = Trip::GetStopTimesForTrips($inbound_trips, $inbound_stops);
uasort($inbound_times, 'TripTimeComparer');

// TODO: when we are displaying a day's last trip + some morning trips, we want to treat the 24:48:00 differently than we 
// treat a day's list with the last trip wrapping over. Currently the last trip(s) are sorting to the end, which is highly
// undesired. We do however need the sort for merging the two routes' schedules during weekdays.
// Some thoughts: 1) other than the route merge, sorting seems to be handled by the database. This may however be implicit
// by trip_id, which is not the safest. See if we can sort in SQL. (Still a problem since evening+morning come from two separate
// SQL queries, but we can just sort inside GetTrips in that case.) 2) if not, incorporate some sort of day/timestamp identity
// to each stoptime returned. This is somewhat absurd, as we'd probably need full timestamp to avoid problems wrapping
// over ends of service periods, months, years, etc.
// Currently leaving unsolved -- ~qviri, 2009.02.22

$outbound_trips = Trip::GetTrips($routes, $outbound_stops, '1', $limit, $time);
$outbound_times = Trip::GetStopTimesForTrips($outbound_trips, $outbound_stops);
uasort($outbound_times, 'TripTimeComparer');

$schedule = Page::GetRequestedServicePeriod($time);
echo '<p style="margin-bottom: -1.5em;">viewing ';
if (Page::IsShowingFullSchedule() === false)
{
	echo $schedule->name . ' schedule upcoming trips';
	echo ' &#183; view full schedule: ';

	// TODO: there is a GetAllServicePeriods() but it also includes 'All week' and foreach leaves dangling commas... meh
	echo ' <a href="?serviceperiod=2">weekday</a>, ';
	echo ' <a href="?serviceperiod=3">Saturday</a>, ';
	echo ' <a href="?serviceperiod=4">Sunday</a>';
}
else
{
	echo 'full ' . $schedule->name . ' schedule';
	echo ' &#183; <a href="/keats/">view upcoming trips</a>';
}
echo '</p>' . "\n\n";

echo '<h2>to universities</h2>';
PrintStopList($inbound_stops, $inbound_times);

echo '<h2>to Fischer-Hallman Rd.</h2>';
PrintStopList($outbound_stops, $outbound_times);


function PrintStopList($stops, $triptimes)
{
	echo '<table class="schedule">';

	echo '<tr>';
	foreach ($stops as $stop)
	{
		echo '<th>';
		echo $stop->ToString(false); // false for not verbose, this makes it exclude direction which is evident here
		echo '</th>';
	}
	echo '</tr>' . "\n";

	$i = 0;
	foreach ($triptimes as $tripnumber => $trip)
	{
		echo '<!-- trip #' . $tripnumber . '--><tr';

		if ($i % 2 === 1)
		{
			echo ' class="row-highlight"';
		}
		$i++;
		echo '>';

		foreach ($stops as $stop)
		{
			echo '<td>';

			if (array_key_exists($stop->id, $trip))
			{
				$stoptime = $trip[$stop->id];
				
				$hours = (int) substr($stoptime, 0, 2);
				if ($hours > 23)
				{
					//$newHours = str_pad(($hours - 24), 2, '0', STR_PAD_LEFT);
					$newHours = '0' . ($hours - 24); // just as good for our purposes, and faster
					$stoptime = $newHours . substr($stoptime, 2);
				}
				$stoptime = substr($stoptime, 0, -3); // trim off seconds, useless for bus schedules
				echo $stoptime;
			}
			else
			{
				echo '&nbsp;';
			}
			
			echo '</td>';
		}
		
		echo '</tr>' . "\n";
	}
		
	echo '</table>';
}

function TripTimeComparer($a, $b)
{
	$a0 = $a[key($a)]; // wow. just wow. (grab first key)
	$b0 = $b[key($b)];
	reset($a);
	reset($b);

/*	$ahours = substr($a0, 0, 2);
	$bhours = substr($b0, 0, 2);

	if ($ahours >= 24)
	{
		$a0 = ($ahours-24) . substr($a0, 2, 0);
	}
	if ($bhours >= 24)
	{
		$b0 = ($bhours-24) . substr($b0, 2, 0);
	}*/
	
	if ($a0 > $b0)
	{
		return 1;
	}
	else if ($b0 < $b0)
	{
		return -1;
	}
	else
	{
		return 0;
		// TODO: implement some sort of recursion. Also, handle cases where [0] is empty and slot it in properly: look through $a/$b until we find a non-empty value and then compare against the value at the same index in the other array
		// also, do it not horribly slowly (glhf?)
	}
}

include_once 'footer.php';
include_once '../common/footer.php';

?>
