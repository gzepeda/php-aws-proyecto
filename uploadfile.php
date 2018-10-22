<?php
   if(isset($_FILES['image']))
   {
	$file_name = $_FILES['image']['name'];   
	$temp_file_location = $_FILES['image']['tmp_name']; 

    require 'awssdk/aws-autoloader.php';

	$s3 = new Aws\S3\S3Client([
                'profile' => 'default',
                'region' => 'us-east-1',
                'version' => 'latest'
	]);

	$result = $s3->putObject([
		'Bucket' => 'gzepeda-aws-proyecto',
		'Key'    => $file_name,
		'SourceFile' => $temp_file_location			
	]);

	var_dump($result);
   }
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>  