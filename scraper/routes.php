<?php

include_once 'common.php';

class RouteScraper
{
	static function ListRoutes()
	{
		global $user_agent;
		$target_url = 'http://192.237.29.245/HastinfoWeb/Home.aspx';
		$target_url = 'http://192.237.29.245/HastinfoWeb/StartTimetableForm.aspx';
		$cookies_file = 'routes.cookies.temp';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_URL,$target_url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch,CURLOPT_COOKIEJAR,$cookies_file);
		curl_setopt($ch,CURLOPT_COOKIEFILE, $cookies_file);
		$html = curl_exec($ch);

//print $html;

$dom = new DOMDocument();
@$dom->loadHTML($html);

$xpath = new DOMXPath($dom);
$forms = $xpath->evaluate("/html/body/form");

		for ($i = 0; $i < $forms->length; $i++)
		{
			$form = $forms->item($i);
			$action = $form->getAttribute('action');
//			print '<h1>' . $action . '</h1>';
		}

		$moniker = substr($action, strpos($action, '=') + 1);
		$day = '11';
		$month = '5';
		$year = '2009';
		$timestamp = time() . '123';

		$routes_link = 'http://192.237.29.245/HastinfoWeb/TimetableQuery.aspx?moniker=' . $moniker . '&rcbID=RouteDirectionComboBox_ComboBox&rcbServerID=ComboBox&text=&comboText=&comboValue=&skin=Default&clientDataString=%7B%22ResetCount%22%3Atrue%7D~*~*~' . $day . '~~' . $month . '~~' . $year . '&timeStamp=' . $timestamp;
			
		//http://192.237.29.245/HastinfoWeb/TimetableQuery.aspx?moniker=Vmlld1RpbWV0YWJsZTpUaW1ldGFibGVPcHRpb25zOjUxMjQzOGNkLWNmYmQtNDJiYS05NjM2LWExZjczYjNmMDE4Mw==&rcbID=RouteDirectionComboBox_ComboBox&rcbServerID=ComboBox&text=&comboText=&comboValue=&skin=Default&clientDataString=%7B%22ResetCount%22%3Atrue%7D~*~*~11~~5~~2009&timeStamp=1242069554931

print $routes_link;
		$routes_referer = $target_url . '?moniker=' . $moniker;

print '<p>' . $routes_referer . '</p>';

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL,$routes_link);
		curl_setopt($ch, CURLOPT_REFERER, $routes_referer);
		curl_setopt($ch,CURLOPT_COOKIEJAR,$cookies_file);
		curl_setopt($ch,CURLOPT_COOKIEFILE, $cookies_file);
		$html2 = curl_exec($ch);
		print curl_error($ch);

		print $html2;
		var_dump($html2);

		if (file_exists($cookies_file))
		{
			unlink($cookies_file);
		}

		curl_close($ch);
	}
}

?>
