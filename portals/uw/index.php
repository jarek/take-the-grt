<?php

include_once '../../common/headers.php';

echo '<';
?>?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/keats/styl.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<title>Take the GRT</title>
<?php /*	<style>
		ul {
			position: relative;
			list-style-type: none;
		}
		li {
			width: 45%;
		}
		li+li {
			position: absolute;
			top: 0;
			left: 50%;
		}
		li+li h3 {
			margin-top: 0;
		}
	</style>*/ ?>
	<style type="text/css">
		ul.split {
			width: 200px;
		}

		ul.split li {
			width: 50%;
			float: left;
		}

		div {
			margin-right: 5em;
		}
	</style>
</head>

<body>

<p class="note">This is fake test data.</p>

<h1><a href="/">Take the GRT</a>: <a href="/portals/">portals</a>: University of Waterloo</h1>

<div style="float:left">
<h2>connections to other portals</h2>

<p class="note">(these and more portals coming soon!)</p>

<ul>
<li>UW &rArr; <a href="#">UW Pharmacy campus</a>
	<ul>
		<li>12:54 from DC west side (7 Mainline)</li>
		<li>13:02 from DC west side (iXpress)</li>
	</ul></li>
<li>UW &rArr; <a href="#">Laurier</a>
	<ul>
		<li>12:55 from Seagram (12 Lincoln)</li>
		<li>12:55 from DC west side (9 Lakeshore)</li>
		<li>12:57 from DC west side (iXpress)</li>
	</ul></li>
<li>UW &rArr; <a href="#">UW Architecture campus</a>
	<ul>
		<li>12:57 from DC west side (iXpress)</li>
		<li>13:12 from DC west side (iXpress)</li>
	</ul></li>
</ul>
</div>

<div style="float:left">

<h2>connecting route schedules</h2>

<ul>
<li>iXpress: <a href="http://grtmobile.ca/route/200_ixpress_to_waterloo">to Conestoga</a>, <a href="http://grtmobile.ca/route/200_ixpress_to_cambridge">to Ainslie</a></li>
<li>7D / 7E: <a href="http://grtmobile.ca/route/7_mainline_to_kitchener">to Charles Terminal</a>, <a href="http://grtmobile.ca/route/7_mainline_to_waterloo">to UW</a></li>
<li>8: <a href="http://grtmobile.ca/route/8_university_fairview_park_to_kitchener">to Charles Terminal</a>, <a href="http://grtmobile.ca/route/8_university_fairview_park_to_waterloo">to UW</a></li>
<li>9: <a href="http://grtmobile.ca/route/9_lakeshore_to_conestoga_mall">to Conestoga</a>, <a href="http://grtmobile.ca/route/9_lakeshore_to_university">to UW</a></li>
<li>12: <a href="http://grtmobile.ca/route/12_fairview_park_conestoga_mall_to_waterloo">to Conestoga</a>, <a href="http://grtmobile.ca/route/12_fairview_park_conestoga_mall_to_kitchener">to Fairview Park</a></li>
<li>13: <a href="http://grtmobile.ca/route/13_laurelwood_to_laurelwood">to Laurelwood</a>, <a href="http://grtmobile.ca/route/13_laurelwood_to_uw">to UW</a></li>
<li>29: <a href="http://grtmobile.ca/route/29_keats_way_to_erbsville_rd">to Erbsville</a>, <a href="http://grtmobile.ca/route/29_keats_way_to_uw">to UW</a></li>
<li>31: <a href="http://grtmobile.ca/route/31_lexington_to_conestoga_mall">to Conestoga</a>, <a href="http://grtmobile.ca/route/31_lexington_to_uw">to UW</a></li>
</ul>
</div>

<div style="clear:both">
<h2>all upcoming departures</h2>

<h3>to Conestoga (<a href="/portals/uw/to/conestoga">permanent link</a>)</h3>

<p>iXpress, 9, 12, 31</p>

<h3>to Fairview Park</h3>

<p>iXpress, 8, 12</p>

<h3>to Laurier / University and King</h3>

<p>iXpress, 7D, 7E, 9, 12... just watch the destination signs</p>

<h3>to Keats / Erbsville</h3>

<?php

	$trips = array(336, 338);

	foreach ($trips as $trip) {

		$query = 'SELECT * FROM StopTime WHERE trip_id = ? ';
		$query .= ' AND time > ? ORDER BY time ASC LIMIT 5';
		$params = array($trip, date('H:i:s', time()-5*3600));
		$times[$trip] = $Database->Select($query, $params);
	}


	print_r($times);

?>

<table>
<tr>
<th>route</th>
<th>times</th>
</tr>
<tr>
<td rowspan="3">12</td>
<td>6:10 PM from Phillip</td></tr>
<tr><td>6:12 PM from Seagram / SCH</td></tr>
<tr><td>6:13 PM from past Seagram / PAS</td>
</tr>
<tr>
<td>29</td>
<td>6:00 PM from Ring Road / SCH</td>
</tr>
</table>

<h3>to Laurelwood</h3>

<p>13</p>

<h3>to Hazel / Lakeshore</h3>

<p>9</p>

<h3>to Uptown Waterloo</h3>

<p>iXpress, 7D, 7E</p>

<h3>to downtown Kitchener / Charles Terminal</h3>

<p>iXpress, 7D, 7E, 8</p>

</div>

<?php

include '../../common/footer.php';

?>

</body>
</html>
