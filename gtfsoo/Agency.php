<?php

class Agency
{
	public static $id = 1337;
	public static $name = 'GRT';
	public static $url = 'http://www.grt.ca/';
	public static $timezone = 'America/Toronto';
	public static $lang = 'en';
	public static $phone = '519-585-7555';

	static function ToString()
	{
		$fields = array(self::$id, self::$name, self::$url, self::$timezone, self::$lang, self::$phone);
	
		$res = "agency_id,agency_name,agency_url,agency_timezone,agency_lang,agency_phone\n";
		$res .= implode(',',$fields);
		$res .= "\n";

		return $res;
	}
}

?>
