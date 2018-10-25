<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    include "database.php";
    require 'vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    class Image {
        var $key;
        function set_key($new_key) {
			$this->key = $new_key;
		}
		function get_key() {
			return $this->key;
		}
        var $link;
        function set_link($new_link) {
			$this->key = $new_link;
		}
		function get_link() {
			return $this->link;
		}
    }

    class Images {

        public static function Upload() {

            $maxsize = 8388608; // set to 4 MB
            //Variables
            $file_name = $_FILES['imageToUpload']['name'];   
            $temp_file_location = $_FILES['imageToUpload']['tmp_name']; 
            $bucket = 'gzepeda-aws-proyecto';

            // check associated error code
            //if ($_FILES['imageToUpload']['error'] == UPLOAD_ERR_OK) {

                // check whether file is uploaded with HTTP POST
                //if (is_uploaded_file($_FILES['imageToUpload']['tmp_name'])) {    

                    // check size of uploaded image on server side
                    if ( $_FILES['imageToUpload']['size'] < $maxsize) {  

                        // check whether uploaded file is of image type
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if (strpos(finfo_file($finfo, $_FILES['imageToUpload']['tmp_name']), "image") === 0) {    

                            $s3 = new Aws\S3\S3Client([
                                'profile' => 'default',
                                'region' => 'us-east-1',
                                'version' => 'latest'
                            ]);
                        
                            $result = $s3->putObject([
                                'Bucket' => $bucket,
                                'Key'    => $file_name,
                                'SourceFile' => $temp_file_location, 
                                'ACL'    => 'public-read'		
                            ]);

                            $imageURL = $s3->getObjectUrl($bucket, $file_name);

                            // open the image file for insertion
                            //$imagefp = fopen($_FILES['imageToUpload']['tmp_name'], 'rb');

                            // put the image in the db...
                            $database = new Database();
                            $id = $database->UploadImage($file_name, $imageURL);

                            header("Location: index.php");
                            exit;
                        }
                        else { // not an image
                            echo '<script type="text/javascript">';
                            echo 'alert("Uploaded file is not an image");';
                            echo 'window.location.href = "index.php";';
                            echo '</script>';
                            exit;
                        }
                    }
                    else { // file too large
                        echo '<script type="text/javascript">';
                        echo 'alert("Uploaded file is too large");';
                        echo 'window.location.href = "index.php";';
                        echo '</script>';
                        exit;
                    }
                //}
                //else { // upload failed
                //    echo '<script type="text/javascript">';
                //    echo 'alert("File upload failed");';
                //    echo 'window.location.href = "/";';
                //    echo '</script>';
                //    exit;
                //}
            // }
            //else {
            //    echo '<script type="text/javascript">';
            //    echo 'alert("File upload failed");';
            //    echo 'window.location.href = "/";';
            //    echo '</script>';
            //    exit;
            //}
        }

        public static function GetImages() {

            //Get an array of image objects

            $database = new Database();
            $images = $database->GetAllImages();

            return $images; 

            // //Esto ya no se usa pero lo dejo de referencia
            // $bucket = 'gzepeda-aws-proyecto';

            // // Instantiate the client.
            // $s3 = new S3Client([
            //     'profile' => 'default',
            //     'region' => 'us-east-1',
            //     'version' => 'latest'
            // ]);

            // $images = array();

            // try {
            //     $objects = $s3->listObjects([
            //         'Bucket' => $bucket
            //     ]);
            //     foreach ($objects['Contents']  as $object) {
            //         $image = new Image;
            //         $image->key = $object['Key'];
            //         $image->link = $s3->getObjectUrl($bucket, $image->key);
            //         array_push($images,$image);
            //     }
            // } catch (S3Exception $e) {
            //     echo $e->getMessage() . PHP_EOL;
            // }
            // return $images;
        }

        public static function GetImage($id) {

            $database = new Database();
            $image = $database->FindImage($id);
            return $image;
            
            // //Esto ya no se usa
            // $bucket = 'gzepeda-aws-proyecto';

            // // Instantiate the client.
            // $s3 = new S3Client([
            //     'profile' => 'default',
            //     'region' => 'us-east-1',
            //     'version' => 'latest'
            // ]);

            // $image = new Image;

            // try {
            //     // Get the object.
            //     $image->key = $keyname;
            //     $image->link = $s3->getObjectUrl($bucket, $image->key);

            // } catch (S3Exception $e) {
            //     echo $e->getMessage() . PHP_EOL;
            // }

        }
    }
?>