<?php namespace RedshiftImporter;

class RedshiftImporter {

	private $csvFilename;
	private $sqlFilename;

	public function process($arguments) {
		if (count($arguments) < 2) {
			echo 'Please specify a file to import' . PHP_EOL;
			echo 'E.g. ./import members.csv' . PHP_EOL;
		} else {
			$basePath = dirname($arguments[1]);

			$tableName = pathinfo($arguments[1], PATHINFO_FILENAME); // E.g. members
			$this->csvFilename = $basePath . '/' . $tableName . '.csv';
			$this->sqlFilename = $basePath . '/' . $tableName . '.sql';

			$this->validateInput();

			echo 'Converting file...';
			$converter = new Converter();
			$tmpCsvFilename = $converter->convert($this->csvFilename);
			echo ' DONE' . PHP_EOL;

			echo 'Uploading file...';
			$uploader = new Uploader();
			$uploader->upload($tmpCsvFilename, basename($this->csvFilename));
			echo ' DONE' . PHP_EOL;

			echo 'Importing file...';
			$importer = new Import();
			$importer->import($tableName, basename($this->csvFilename), $this->sqlFilename);
			echo ' DONE' . PHP_EOL;
		}
	}

	private function validateInput() {
		if (!file_exists($this->csvFilename)) {
			throw new \Exception($this->csvFilename . ' doesn\'t exist');
		}

		if (!file_exists($this->sqlFilename)) {
			throw new \Exception($this->sqlFilename . ' doesn\'t exist');
		}
	}
}