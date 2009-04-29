<?php

if (empty($_POST['stoplist']) == false)
{
	$stops = explode("\n", $_POST['stoplist']);

	foreach ($stops as $stop)
	{
		$stopinfo = str_replace('<div id="StopComboBox_ComboBox_c', '', $stop);
		$stopinfo = str_replace('" class="ComboBoxItem_Default">', '', $stopinfo);
		$stopinfo = str_replace('</div>', '', $stopinfo);

		preg_match('/(?<stopnumber>\d*)\s*(?<stopid>\d*)\s*-\s*(?<stopname>.*)$/', $stopinfo, $matches);
		
		$stopinfos[] = array($matches['stopnumber'], $matches['stopid'], $matches['stopname']);
		$stopids[] = $matches['stopid'];
	}

echo '<pre>';

echo implode(',', $stopids);

	print_r($stopinfos);
echo '</pre>';
}

echo '<form method=post>';
echo '<textarea name="stoplist" id="stoplist">' . $_POST['stoplist'] . '</textarea>';
echo '<input type=submit /></form>';

?>
