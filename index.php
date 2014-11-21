<?php

echo '<';
?>?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="keats/styl.css" media="screen, projection" rel="stylesheet" type="text/css" />
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
</head>

<body>

<h1 style="margin-top: 2em;">Take the GRT</h1>

<p>is a project aiming to put <a href="http://www.grt.ca/">Grand River Transit</a>'s rider information data 
into a format that can be easily used by computer programs and use this information 
in handy tools and utilities.</p>

<p>For more information, see <a href="http://takethegrt.ca/wiki/">our wiki</a>.</p>

<h2>offerings</h2>

<p>We're in the ramp-up phase, and we hope to have more useful and handy tools here shortly. 
For now, enjoy:</p>

<ul>
<li>
	<h3>system maps</h3>
	<p>The system maps published by GRT are massive PDFs, 16&nbsp;MB and 23&nbsp;MB for Kitchener-Waterloo and Cambridge respectively. 
		They take a long time to download and open, and trying to view them using anything but a desktop computer is an exercise in frustration.</p>
	<p>We've converted the PDFs into standard images, resulting in dramatically smaller files that are more practical to use. We are also making the map available as a Google Maps overlay for convenient in-browser use.</p>
	<p>Browse live overlay:
		<strong><a href="/map/">GRT system map</a></strong>.
	Download: <a href="/downloads/grt-kw.gif">K-W map</a> (2.3&nbsp;MB) or 
		<a href="/downloads/grt-kw-small.gif">a smaller version</a> (1.3&nbsp;MB),
		<a href="/downloads/grt-cambridge.gif">Cambridge map</a> (1.0&nbsp;MB). Save them on your computer or device for future reference, or come back anytime to view them online.
	See a (short) <a href="/map/about/#errata">list of inaccuracies</a> in these maps (concerns routes 14, 26, 33).</p>
	<p>TriTAG also hosts <a href="http://www.tritag.ca/resources/transit-in-waterloo-region/grt-map/">a friendlier version of the GRT map</a>.</p>
</li>
<li>
	<h3>Keats&nbsp;Way schedule</h3>
	<p>As a proof-of-concept demonstration of our ongoing work with GRT's bus schedules, we've put together 
		a page that lists schedules of buses travelling along Keats&nbsp;Way to and from the universities. 
		It knows about weekday and weekend schedules and automatically displays the upcoming trips first.</p>
	<p>See for yourself: <a href="/keats/">Keats/University demo</a>. 
		The page is lightweight (~3&nbsp;kB); try viewing it on your mobile device for convenience on the go.</p>
</li>
</ul>

</body>
</html>
