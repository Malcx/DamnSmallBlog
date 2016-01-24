<?php
session_start();
// User logged in?
if(!isset($_SESSION["loggedin"]))
{
	header("Location: /admin/login.php");
	exit(0);
}

include_once("config.php");
include_once("page_functions.php");

$errMessage = "";
if(isset($_REQUEST["submitting"]))
{
	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) {
	        //echo "File is an image - " . $check["mime"] . ".";
	        $uploadOk = 1;
	    } else {
	        $errMessage .= "File is not an image.<br />";
	        $uploadOk = 0;
	    }
	}
	// Check if file already exists
	if (file_exists($target_file)) {
	    $errMessage .= "File already exists with this name.<br />";
	    $uploadOk = 0;
	} 

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
	    $errMessage .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br />";
	    $uploadOk = 0;
	}


	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	   // $errMessage .= "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        $errMessage .= "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br />";
	    } else {
	        $errMessage .= "Sorry, there was an error uploading your file.<br />";
	    }
	}
}

if(isset($_REQUEST["delete"]) && isset($_REQUEST["f"]))
{
	if(file_exists("../uploads/".$_REQUEST["f"]))
		unlink("../uploads/".$_REQUEST["f"]);
}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Images</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

</head>

<body>
<h1>Images</h1>
<?php
include_once("menu.php");
?>

<form action="images.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submitting" value="1" />
    <input type="file" name="fileToUpload" id="fileToUpload" />
    <input type="submit" value="Upload New Image" name="submit" />
</form>
<br />

<span class="error"><?php echo $errMessage; ?></span>
<br />


<table>
<tr>
	<th width="100">Thumbnail</th>
	<th width="999">Markdown</th>
	<th width="1"></th>
</tr>
<?php

	$files = scandir("../uploads");
	foreach($files as $f)
	{
		if($f != "." && $f != ".." && $f != "thumbs")
			echo "<tr><td><a href=\"/uploads/$f\"><img src=\"thumbnail.php?src=$f&w=100\" /></a></td><td>![File $f](/uploads/$f)</td><td>[<a href=\"?delete=1&f=$f\">Delete</a>]</td></tr>";
	}


?>

</table>

</body>
</html>