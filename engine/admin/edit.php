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

$errors = array();

$uid = "";
$title = "";
$slug = "";
$old_slug = "";
$date = date("Y/m/d");
$status = "";
$inFeed = "";
$tags = "";
$content = "";

if(isset($_REQUEST["submitting"]))
{
	$uid = $_REQUEST["uid"];
	$title = htmlentities($_REQUEST["title"]);
	$slug = ($_REQUEST["slug"]);
	$old_slug = $_REQUEST["old_slug"];
	$date = $_REQUEST["date"];
	$status = $_REQUEST["status"];
	$inFeed = $_REQUEST["inFeed"];
	$tags = $_REQUEST["tags"];
	$content = str_replace("</textarea>", "&lt;/textarea>", $_REQUEST["content"]);

	if(!$title) $errors[] = "A title is pretty useful.";
	if(!$date) $errors[] = "A publish date is needed for a post!";
	if(!$content) $errors[] = "Some content might be a good idea";

	if(count($errors) == 0)
	{
		// save file and update the changes counter
		if(!$slug)
			$slug = $_REQUEST["title"];
		@file_put_contents("data/amends.dat", 0, FILE_APPEND|LOCK_EX);
		saveDATA($uid, $title, $slug, $old_slug, $date, $status, $content, $tags, $inFeed);
		$_SESSION["successmessage"] = "Post saved";
		header("Location: /admin");
		exit(0);
		
	}

}
elseif(isset($_REQUEST["uid"]))
{
	$post = getPostByUID($_REQUEST["uid"]);
	$uid = $post["UID"];
	$title = htmlentities($post["Title"]);
	$slug = $post["Slug"];
	$old_slug = $post["Slug"];
	$date = $post["Publish Date"];
	$status = $post["Status"];
	$inFeed = $post["In Feed"];
	$tags = $post["Tags"];
	$content = $post["content"];
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $_CONFIG["blogname"];?> - Edit post</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

<script>
// I caved - added some js, for the admin only
var isFull = false;
function toggleFullscreen()
{
	if(isFull)
	{
		document.querySelector("#contentarea").classList.remove("full")
		document.querySelector("#toggleButton").classList.remove("fullButton")
	}
	else
	{
		document.querySelector("#contentarea").classList.add("full")
		document.querySelector("#toggleButton").classList.add("fullButton")
	}	
	isFull = !isFull;
}


</script>
</head>

<body>
<h1>Create/Edit post</h1>

<?php
include_once("menu.php");

foreach($errors as $e)
	echo "<span class='error'>$e<br /></span>";

?>

<form method="post">
<input type="hidden" name="old_slug" value="<?php echo $old_slug;?>" />
<input type="hidden" name="uid" value="<?php echo $uid;?>" />
<input type="hidden" name="submitting" value="1" />

<label>Title:</label> <input type="text" name="title" value="<?php echo $title;?>" /><br />
<label>Page slug:</label> <input type="text" name="slug" value="<?php echo $slug;?>" /> <nobr><small>Leave slug blank to automatically generate from title.</small></nobr><br />
<label>Publish date:</label> <input type="text" name="date" value="<?php echo $date;?>" /> <nobr><small>YYYY/MM/DD format</small></nobr><br />
<label>Status:</label> <select name="status">
			<option <?php echo ($status=="LIVE")?"selected":"";?> value="LIVE">Live</option>
			<option <?php echo ($status=="DRAFT")?"selected":"";?> value="DRAFT">Draft</option>
			</select><br />

<label>Display In Feed:</label> <select name="inFeed">
			<option <?php echo ($inFeed=="YES")?"selected":"";?> value="YES">Yes - Normal blog post.</option>
			<option <?php echo ($inFeed=="NO")?"selected":"";?> value="NO">No - Use for menu items etc</option>
			</select><br />

<textarea id="contentarea" name="content"><?php echo $content;?></textarea><br />
<div onclick='toggleFullscreen()' class='button' id="toggleButton"><div class='deg45'>&harr;</div></div>
<label>Tags:</label> <input type="text" name="tags" value="<?php echo $tags;?>" /> <nobr><small>Comma seperated topics.</small></nobr><br />


<label></label> <input type="submit" id="saveButton" value="Save" />

</form>

</body>
</html>