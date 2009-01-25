<?php

include_once 'headers.php';


echo '<a href="createDirection.php">Insert directions for routes</a><br /><br />';
echo '<a href="view.php">View existing routes and directions</a><br />';
exit;


print 'Select route:<br/>';

$route = new Route('12');
$route->long_name = 'Conestoga Mall/Fairview Mall';
$route->short_name = '12';
//$route->WriteToDatabase();

$Routes = Route::GetAllRoutes();
$dd = new DropDown('routeID', $Routes);
$dd->Write();

$ddsp = new DropDown('servicePeriod', ServicePeriod::GetAllServicePeriods());
$ddsp->Write();

echo '<pre>';

print Route::WriteGTFSHeader();
foreach ($Routes as $route)
{
	print $route->WriteToGTFS();
}

print '<br/><br/><br/>Query count: ' . $Database->GetQueryCount();

?>
