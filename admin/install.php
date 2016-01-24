<?php


// If config exists then we have already installed
if(file_exists("config.php"))
{
	header("Location: /admin");
	exit(0);
}

include_once("page_functions.php");

// we are trying to install
if(isset($_REQUEST["install"]))
{

	@mkdir("../uploads");
	@mkdir("../uploads/thumbs");
	@mkdir("../blog");
	@mkdir("data");
	@file_put_contents("data/amends.dat", 0);
	@file_put_contents("data/.htaccess", "Deny from all");



	saveConfigFile(array("blogname" => addslashes($_REQUEST["blogname"]), "username" => $_REQUEST["adminname"], "password" => $_REQUEST["adminpassword"]));

	

	@unlink("install.php");
	@file_put_contents("install.php", "Install file has been removed for security.");
	
	
	header("Location: /admin");
	exit(0);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install</title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>

<body>


<form method="post" action="">
<input type="hidden" name="install" value="1" />
<h1>Installing DamnSmallBlog on <?php echo $_SERVER["SERVER_NAME"]; ?></h1>
Running this might delete important files on your server - make sure to have a back up first!
<br /><br />
<label>Blog Name:</label> <input type="text" name="blogname" /><br />
<label>Admin Username:</label> <input value="Admin" type="text" name="adminname" /><br />
<label>Admin password:</label> <input value="" type="password" name="adminpassword" /><br />

<label></label> <input type="submit" value="Install" />

<br /><br />
<small>Make sure to remember your username and password - otherwise you'll need to edit config.php by hand.</small>
</form>

</body>
</html>
