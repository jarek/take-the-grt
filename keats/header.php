<?php

class Page
{
	static $requestedServicePeriod;
	static $spAge;

	static function GetRequestedServicePeriod($date=false)
	{
		if ($date === false)
		{
			$date = time();
		}

		if (empty(self::$requestedServicePeriod) || ($date - 10*60) > self::$spAge)
		{
			if (empty($_GET['serviceperiod']) === false)
			{
				self::$requestedServicePeriod = new ServicePeriod((int) $_GET['serviceperiod']);
			}
			else
			{
				self::$requestedServicePeriod = ServicePeriod::GetServicePeriod($date);
			}
			
			self::$spAge = time();
		}

		if (empty(self::$requestedServicePeriod))
		{
			// still empty - an appropriate SP was not found
			self::$requestedServicePeriod = new ServicePeriod();
		}

		return self::$requestedServicePeriod;
	}

	static function IsShowingFullSchedule()
	{
		if (isset($_GET['view']) && ($_GET['view'] === 'full'))
		{
			return true;
		}
		else if (isset($_GET['serviceperiod']) === true)
		{
			return true;
		}
		else
		{
			return (Page::IsShowingCurrentServicePeriod() === false);
		}
	}

	static function IsShowingCurrentServicePeriod($date=false)
	{
		$requested = Page::GetRequestedServicePeriod($date);
		$current = ServicePeriod::GetServicePeriod();

		if (empty($requested) || empty($current)) {
			return false;
		}
		else {
			return (Page::GetRequestedServicePeriod($date)->GetID() == ServicePeriod::GetServicePeriod()->GetID());
		}
	}
}

echo '<';
?>?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="styl.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<title>Take the GRT - Keats/University</title>
</head>

<body>

<p class="note">The GRT switched to a spring schedule on Monday, April 6th.
Among <a href="http://grt.ca/web/transit.nsf/$All/8EBA77354278EAAB85256D8000409481?OpenDocument">the changes</a>,
route 12 and 29 schedules are being adjusted, and route 12 is seeing service reductions.
The new schedules will not be shown on this page until our new automated scraper is written. 
We'll try to get it done as soon as possible, but that looks to be late April. 
Sorry for any inconvenience and thanks for using! 
We'll be back to help you in the spring term.</p>
