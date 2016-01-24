<?php
session_start();

// If config does not exists then we have not installed
if(!file_exists("config.php"))
{
	header("Location: /admin/install.php");
	exit(0);
}

// User logged in?
if(!isset($_SESSION["loggedin"]))
{
	header("Location: /admin/login.php");
	exit(0);
}

include_once("config.php");

//is_writable uploads, data, does install still exist?

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $_CONFIG["blogname"];?> - Dashboard</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

</head>

<body>


<h1><?php echo $_CONFIG["blogname"];?> &raquo; Admin Dashboard</h1>



<?php
include_once("menu.php");

if(isset($_SESSION["successmessage"]))
{
	echo "<span class='succss'>{$_SESSION["successmessage"]}<br /></span>";
	unset($_SESSION["successmessage"]);
}

// security checks
if(!file_exists("../uploads"))
{
	echo "<span class='error'>Warning: Your <code>/uploads</code> folder does not exist!<br /></span>";
}
elseif(!is_writable("../uploads"))
{
	echo "<span class='error'>Warning: Your <code>/uploads</code> folder is not writable by the webserver!<br /></span>";
}

// security checks
if(!file_exists("../blog"))
{
	echo "<span class='error'>Warning: Your <code>/blog</code> folder does not exist!<br /></span>";
}
elseif(!is_writable("../blog"))
{
	echo "<span class='error'>Warning: Your <code>/blog</code> folder is not writable by the webserver!<br /></span>";
}

if(!file_exists("data"))
{
	echo "<span class='error'>Warning: Your <code>/admin/data</code> folder does not exist!<br /></span>";
}
elseif(!is_writable("data"))
{
	echo "<span class='error'>Warning: Your <code>/admin/data</code> folder is not writable by the webserver!<br /></span>";
}



if(file_exists("data/amends.dat"))
{
	$fsz = filesize("data/amends.dat");
	if($fsz > 1)
		echo "<a href='generate.php' class='button publish'>Publish " . ($fsz-1) . " updates.</a>";
}
else
	echo "<span class='error'>Warning: Your <code>/data/amends.txt</code> file dfoes not exist!<br /></span>";

?>
</body>
</html>
