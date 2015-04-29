<?php namespace RedshiftImporter;

use Aws\S3\S3Client;

class Uploader {

	public function upload($fileToUpload, $filename) {
		// Credentials are automatically loaded from the environment
		$client = S3Client::factory();

		// Upload file to S3
		$result = $client->putObject([
		    'Bucket'     => getenv('S3_BUCKET_NAME'),
		    'Key'        => $filename,
		    'SourceFile' => $fileToUpload
		]);

		// Wait until the file has been fully uploaded
		$client->waitUntil('ObjectExists', [
		    'Bucket' => getenv('S3_BUCKET_NAME'),
		    'Key'    => $filename
		]);

		return $result;
	}
	
}
