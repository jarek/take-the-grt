<?php

include_once '../common/headers.php';

$selectForm = new Form();
$selectForm->Add(new DropDown('routeID', Route::GetAllRoutes()));
$selectForm->Add(new SubmitButton());
echo $selectForm->ToString();

if (isset($_GET['routeID']))
{

$route = new Route(false, false, $_GET['routeID']);

echo '<p>Route: ' . $route->short_name . ' ' . $route->long_name;

$routedirs = $route->GetDirections();

if (count($routedirs) > 0)
{
	foreach ($routedirs as $route_dir)
	{
		echo '<p>Direction: ' . $route_dir->description;
		
		$stops = $route_dir->GetStops();
		if (count($stops) > 0)
		{
			echo '<blockquote><p>Stops:';

			echo '<table border=1 cellpadding=3>';
			echo '<tr>'; //<td>&nbsp;</td>';
			foreach ($stops as $stop)
			{
				echo '<td>';
				if ($stop->timepoint)
				{
					echo '<strong>';
				}
				echo $stop->ToString();
				echo '</td>';
			}
			echo '</tr>' . "\n";

		/*	for($i = 0; $i < 5; $i++)
			{
				echo '<tr><td>trip #' . $i . '</td>';
				for($j = 0; $j < count($stops); $j++)
				{
					echo '<td>&nbsp;</td>';
				}
				echo "</tr>\n";
			}*/

			echo '</table></blockquote>';
		}
		else
		{
			echo '<blockquote><p>No stops available for this direction</p></blockquote>';
		}
	}
}
else
{
	echo '<p>No directions currently in the database';
}

}

print '<br/><br/><br/><hr/>Query count: ' . $Database->GetQueryCount();

?>
