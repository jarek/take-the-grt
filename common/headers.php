<?php

$time_start = microtime(true);

putenv("TZ=US/Eastern");

error_reporting(E_ALL);

include_once '../forms/Controls.php';
include_once 'Database.php';

function __autoload($class_name)
{
	$filename = '../gtfsoo/' . $class_name . '.php';

	if (file_exists($filename) === true)
	{
	        include_once $filename;
	}
	else
	{
		$filename = '../forms/' . $class_name . '.php';

		if (file_exists($filename) === true)
		{
			include_once $filename;
		}
	}
}

$Database = new Database();

?>
