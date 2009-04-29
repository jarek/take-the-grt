<?php

$time_end = microtime(true);

echo "\n" . '<!--Execution time: ' . ($time_end-$time_start);
echo '; query count: ' . $Database->GetQueryCount() . '-->' . "\n";

?>
