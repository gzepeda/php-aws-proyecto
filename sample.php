<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

	require 'awssdk/aws-autoloader.php';
	use Aws\S3\S3Client;

	// Use the us-east-2 region and latest version of each client.
	$sharedConfig = [
    		'profile' => 'default',
    		'region' => 'us-east-1',
    		'version' => 'latest'
	];

	// Create an SDK class used to share configuration across clients.
	$sdk = new Aws\Sdk($sharedConfig);

	// Use an Aws\Sdk class to create the S3Client object.
	$s3Client = $sdk->createS3();

	// Send a PutObject request and get the result object.
	$result = $s3Client->putObject([
	    'Bucket' => 'gzepeda',
            'Key' => 'akey',
	    'Body' => 'this is the body!'
	]);

	// Download the contents of the object.
	$result = $s3Client->getObject([
	    'Bucket' => 'gzepeda',
	    'Key' => 'akey'
	]);

	// Print the body of the result by indexing into the result object.
	echo $result['Body'];

?>