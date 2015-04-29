<?php namespace RedshiftImporter;

class Import {

	public function process($arguments) {
		if (count($arguments) < 2) {
			echo 'Please specify a csv file to import' . PHP_EOL;
			echo 'Syntax:' . PHP_EOL;
			echo './import example.csv' . PHP_EOL;
		} else {
			$converter = new Converter();
			$csvFile = $converter->convert($arguments[1]);

			$upload = new Uploader();
			$uploader->upload($csvFile);

			$importer = new Import();
			$importer->import(basename($csvFile));
		}
	}
}