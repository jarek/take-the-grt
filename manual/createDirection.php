<?php

include_once 'headers.php';

	echo '<p>Select route:';
	$dropdown = new DropDown('routeID', Route::GetAllRoutes());
	$form = new Form();
	$form->Add($dropdown);
	$form->Add(new SubmitButton());
	echo $form->ToString();

if (isset($_POST['routeID']))
{
	/* adding a direction */

	echo '<hr />';

	$route_id = $_POST['routeID'];
	$dir = $_POST['direction'];
	$descr = $_POST['description'];
	$stops = explode(',', $_POST['stops']);

	echo 'adding direction ' . $descr . ' to route ' . $route_id;
	
	$new_dir = new Direction($route_id, $dir, $descr);
	$new_dir->WriteToDatabase();
	
	echo '<p>route direction created as ID ' . $new_dir->GetID() . ', adding stops... '; 

	$sequence = 0;

	foreach ($stops as $stopValue)
	{
		$stop = trim($stopValue);
	
		$new_dir->AddStop($stop, $sequence);
		$sequence++;
	}
	
	echo $sequence . ' stops added.</p>';
	echo '<hr/>';
}

if (isset($_GET['routeID']))
{
	$route = new Route(false, false, $_GET['routeID']);

	echo '<p>Route: ' . $route->short_name . ' ' . $route->long_name;

	$route_directions = $route->GetDirections();

	if (count($route_directions) > 0)
	{
		echo '<blockquote><p>Existing directions (editing not currently supported)';

		foreach ($route_directions as $route_dir)
		{
			echo '<p>Direction #' . $route_dir->direction . ': ' . $route_dir->description;
			
			$stops = $route_dir->GetStops();
			
			if (count($stops) > 0)
			{
				echo '<blockquote><p>Stops:';

				echo '<table border=1 cellpadding=3>';
				echo '<tr>';
				foreach ($stops as $stop)
				{
					echo '<td>';
					if ($stop->timepoint)
					{
						echo '<strong>';
					}
					echo $stop->code;
					echo '</td>';
				}
				echo '</tr>' . "\n";
				echo '</table></blockquote>';
			}
			else
			{
				echo '<blockquote><p>No stops available for direction ID# ' . $route_dir->GetID() . '</p></blockquote>';
			}
		}
		echo '</blockquote>';
	}

	echo '<p>Add new directions:</p>';

	$addForm = new Form(false, 'post');
	$addForm->Add(new Input('Direction ID (integer, zero based)', 'direction'));
	$addForm->Add(new Input('<br />Description (eg "to University")', 'description'));
	$addForm->Add(new Input(
		'<br/>Stop IDs (comma-separated, in order from beginning of the route to the final destination as specified in Description):<br />', 'stops'));
	$addForm->Add(new Hidden('routeID', $_GET['routeID']));
	$addForm->Add(new SubmitButton());
	echo $addForm->ToString();
}

print '<br/><br/><br/><hr/>Query count: ' . $Database->GetQueryCount();

?>
