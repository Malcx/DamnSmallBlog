<?php
session_start();
// User logged in?
if(!isset($_SESSION["loggedin"]))
{
	header("Location: /admin/login.php");
	exit(0);
}

include("config.php"); // for settings we include this maybe more than once when saving!
include_once("page_functions.php");

$errors = array();

$blogname = "";
$username = "";
$password = "";
$baseDomain = "";
$fullOrPart = "FULL";
$itemsPerPage = 5;
$menu = "";
$logoURL = "";
$copyright = "";
$googleAnalytics = "";
$hackernews = false;
$reddit = false;
$facebook = false;
$twitter = false;

if(isset($_REQUEST["submitting"]))
{

	$saveArray = array();
	
	$saveArray["blogname"] = addslashes($_REQUEST["blogname"]);
	$saveArray["username"] = $_CONFIG["username"];

	if($_REQUEST["password"]!="")
		$saveArray["password"] = $_REQUEST["password"];

	$saveArray["baseDomain"] = $_REQUEST["baseDomain"];
	$saveArray["fullOrPart"] = $_REQUEST["fullOrPart"];
	$saveArray["itemsPerPage"] = $_REQUEST["itemsPerPage"];
	$saveArray["menu"] = addslashes($_REQUEST["menu"]);
	$saveArray["logoURL"] = $_REQUEST["logoURL"];
	$saveArray["copyright"] = addslashes($_REQUEST["copyright"]);
	$saveArray["googleAnalytics"] = $_REQUEST["googleAnalytics"];

	$saveArray["social"] = "&" . $_REQUEST["hackernews"] . "&" . $_REQUEST["reddit"] . "&" . $_REQUEST["facebook"] . "&" . $_REQUEST["twitter"];
	


	saveConfigFile($saveArray);

	@file_put_contents("data/amends.dat", 0, FILE_APPEND|LOCK_EX);
	$_SESSION["successmessage"] = "Settings saved";
	header("Location: /admin");
	exit(0);

}
else
{

	$blogname = htmlentities($_CONFIG["blogname"]);
	$username = $_CONFIG["username"];
	$password = $_CONFIG["password"];
	$baseDomain = $_CONFIG["baseDomain"];
	$fullOrPart = $_CONFIG["fullOrPart"];
	$itemsPerPage = $_CONFIG["itemsPerPage"];
	$menu = htmlentities($_CONFIG["menu"]);
	$logoURL = $_CONFIG["logoURL"];
	$copyright = htmlentities($_CONFIG["copyright"]);
	$googleAnalytics = $_CONFIG["googleAnalytics"];
	$hackernews = strpos($_CONFIG["social"], "HackerNews") ? true : false;
	$reddit = strpos($_CONFIG["social"], "Reddit") ? true : false;
	$facebook = strpos($_CONFIG["social"], "Facebook") ? true : false;
	$twitter = strpos($_CONFIG["social"], "Twitter") ? true : false;
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $_CONFIG["blogname"];?> - Settings</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

</head>

<body>
<h1>Edit settings</h1>

<?php
include_once("menu.php");

foreach($errors as $e)
	echo "<span class='error'>$e<br /></span>";

?>

<form method="post">
<input type="hidden" name="submitting" value="1" />

<hr />
<h3>Credentials</h3>
<label>Admin Username:</label> <input type="text" name="username" value="<?php echo $username;?>" /><br />
<label>Password:</label> <input type="password" name="password" value="" /> <nobr><small>Leave blank to remain unchanged.</small></nobr><br />
<hr />
<h3>Appearance</h3>
<label>Blog Name:</label> <input type="text" name="blogname" value="<?php echo $blogname;?>" /><br />
<label>Menu String:</label> <input type="text" name="menu" value="<?php echo $menu;?>" /> <nobr><small>Markdown string for the main menu, e.g. [Projects](/blog/projects.html) [Contact](/blog/contact.html).</small></nobr><br />
<label>Logo URL:</label> <input type="text" name="logoURL" value="<?php echo $logoURL;?>" /><nobr> <small>URL of image to use as logo.</small></nobr><br />
<label>Copyright Text:</label> <input type="text" name="copyright" value="<?php echo $copyright;?>" /><br />

<label>Base URL:</label> <input type="text" name="baseDomain" value="<?php echo $baseDomain;?>" /><br />
<label>Google Analytics:</label> <input type="text" name="googleAnalytics" value="<?php echo $googleAnalytics;?>" /> <nobr><small>Tracking code - e.g. <em>UA-00000001-4</em>.</small></nobr><br />
<label>Show full posts:</label> <select name="fullOrPart">
			<option <?php echo ($fullOrPart=="FULL")?"selected":"";?> value="FULL">Full posts on index pages</option>
			<option <?php echo ($fullOrPart=="PART")?"selected":"";?> value="PART">Summaries only on index pages</option>
			</select><br />
			
<label>Items per page:</label> <select name="itemsPerPage">
			<option <?php echo ($itemsPerPage=="1")?"selected":"";?> value="1">1</option>
			<option <?php echo ($itemsPerPage=="2")?"selected":"";?> value="2">2</option>
			<option <?php echo ($itemsPerPage=="5")?"selected":"";?> value="5">5</option>
			<option <?php echo ($itemsPerPage=="10")?"selected":"";?> value="10">10</option>
			<option <?php echo ($itemsPerPage=="20")?"selected":"";?> value="20">20</option>
			<option <?php echo ($itemsPerPage=="25")?"selected":"";?> value="25">25</option>
			<option <?php echo ($itemsPerPage=="99999")?"selected":"";?> value="99999">ALL</option>
			</select><br />
<hr />
<h3>Sharing</h3>

<label>Hacker News:</label> <select name="hackernews">
			<option <?php echo ($hackernews)?"selected":"";?> value="HackerNews">Include Share Link</option>
			<option <?php echo (!$hackernews)?"selected":"";?> value="">No Share Link</option>
			</select><br />
<label>Reddit:</label> <select name="reddit">
			<option <?php echo ($reddit)?"selected":"";?> value="Reddit">Include Share Link</option>
			<option <?php echo (!$reddit)?"selected":"";?> value="">No Share Link</option>
			</select><br />
<label>Facebook:</label> <select name="facebook">
			<option <?php echo ($facebook)?"selected":"";?> value="Facebook">Include Share Link</option>
			<option <?php echo (!$facebook)?"selected":"";?> value="">No Share Link</option>
			</select><br />
<label>Twitter:</label> <select name="twitter">
			<option <?php echo ($twitter)?"selected":"";?> value="Twitter">Include Share Link</option>
			<option <?php echo (!$twitter)?"selected":"";?> value="">No Share Link</option>
			</select><br />
	


<label></label> <input type="submit" id="saveButton" value="Save" />

</form>

</body>
</html>