<?php namespace RedshiftImporter;

class Import {

	private $db;

	public function import($tableName, $csvFilename, $sqlFilename) {
		$this->connectToRedshift();

		// Drop table
		$this->runQuery('DROP TABLE IF EXISTS ' . $tableName);

		// Create table
		$this->runQuery($this->generateCreateTableCommand($sqlFilename));

		// Import data
		$this->runQuery($this->generateCopyCommand($tableName, $csvFilename));
	}

	private function connectToRedshift() {
		$dsnTemplate = 'pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s';
		$dsn = sprintf($dsnTemplate, getenv('REDSHIFT_HOSTNAME'), getenv('REDSHIFT_PORT'), getenv('REDSHIFT_DATABASE'), getenv('REDSHIFT_USERNAME'), getenv('REDSHIFT_PASSWORD'));
		
		$this->db = new \PDO($dsn);
	}

	private function generateCopyCommand($tableName, $csvFilename) {
		$bucketName = getenv('S3_BUCKET_NAME');
		$accessKey = getenv('AWS_ACCESS_KEY_ID');
		$secretKey = getenv('AWS_SECRET_ACCESS_KEY');

		$sql = <<<SQL
		copy $tableName from 's3://$bucketName/$csvFilename'
		credentials 'aws_access_key_id=$accessKey;aws_secret_access_key=$secretKey'
		csv;
SQL;

		return $sql;
	}

	/**
	 * This will naively convert a MySQL statement to Postgres/Redshift compatible syntax
	 */
	private function generateCreateTableCommand($sqlFilename) {
		$sql = file_get_contents($sqlFilename);
		$sql = str_replace('`', '', $sql);

		// Update column types
		$sql = preg_replace('/\Wtinyint(\(\d+\))?\W/i', ' SMALLINT ', $sql);
		$sql = preg_replace('/\Wint(\(\d+\))?\W/i', ' INT ', $sql);

		$lines = explode("\n", $sql);
		foreach ($lines as &$line) {
			// Remove AUTO_INCREMENT if present
			$line = str_replace(' AUTO_INCREMENT', '', $line);

			// Remove any KEY statements
			if (strpos($line, ' KEY ') !== FALSE) {
				$line = '';
			} elseif ($line[0] == ')') { // This must be the end of the CREATE TABLE statement
				$line = ')'; // Skip everything else on the line
				break;
			}
		}

		$sql = implode("\n", $lines);

		// Fix possible syntax error
		$sql = preg_replace('/,\W+\)/', ')', $sql);
		
		return $sql;
	}

	private function runQuery($sql) {
		if ($this->db->exec($sql) === FALSE) {
			throw new ImportException($this->db->errorInfo()[2]);
		}
	}
	
}
