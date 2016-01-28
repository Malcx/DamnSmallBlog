<?php
session_start();

include_once("config.php");

if(isset($_SESSION["loggedin"]))
{
	header("Location: /admin");
	exit(0);
}


$loginerror = false;
if(isset($_REQUEST["login"]))
{
	if(isset($_REQUEST["username"]) && $_REQUEST["username"] == $_CONFIG["username"] && isset($_REQUEST["password"]) && md5("dsb_" . $_REQUEST["password"]) == $_CONFIG["password"])
	{
	
		$_SESSION["loggedin"] = true;
		header("Location: /admin");
		exit(0);
	
	}
	else
	{
		$loginerror = true;
	}

	
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Administrator Login</title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>

<body>


<form method="post" action="">
<input type="hidden" name="login" value="1" />
<h1>Admin Log in.</h1>
<label>Admin Username:</label> <input value="" type="text" name="username" /><br />
<label>Admin password:</label> <input value="" type="password" name="password" /><br />

<label></label> <input type="submit" value="Login" />

<?php
if($loginerror)
	echo "<br /><small class='error'>Your username or password was incorrect.</small>"
?>
</form>

</body>
</html>
