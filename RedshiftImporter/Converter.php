<?php namespace RedshiftImporter;

class Converter {

	public function convert($inputFilename) {
		$outputFilename = tempnam(sys_get_temp_dir(), 'RedshiftImporter');

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

		return $outputFilename;
	}
}
