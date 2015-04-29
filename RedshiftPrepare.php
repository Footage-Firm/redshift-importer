<?php
/**
 * Redshift preparation tool
 * This will take a CSV file and transform it into a format that
 * Redshift won't choke on, based on string replacements.
 */

if (count($argv) < 3) {
	echo 'Please specify a csv file to prepare' . PHP_EOL;
	echo 'Syntax:' . PHP_EOL;
	echo 'php RedShiftPrepare.php example.csv' . PHP_EOL;
	exit;
}

$inputFilename = $argv[1];
$outputFilename = $argv[2];

$nullValues = ['NULL', '0000-00-00', '0000-00-00 00:00:00'];

$firstRow = true;
$writeHandle = fopen($outputFilename, 'w');
if (($readHandle = fopen($inputFilename, 'r')) !== FALSE) {
    while (($row = fgetcsv($readHandle, 2048, ',')) !== FALSE) {
    	// Skip first row
    	if ($firstRow) {
    		$firstRow = false;
    		continue;
    	}

    	// Perform field replacements
    	foreach ($row as $key => $val) {
    		$row[$key] = str_replace('\"', '', $val);

    		if (in_array($val, $nullValues)) {
    			$row[$key] = '';
    		}
    	}

    	fputcsv($writeHandle, $row);
    }
    fclose($readHandle);
}
fclose($writeHandle);
