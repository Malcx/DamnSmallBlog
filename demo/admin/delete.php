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

$post = getPostByUID($_REQUEST["uid"]);

if(isset($_REQUEST["confirm"]))
{
	unlink("data/{$post["Slug"]}.md");
	@file_put_contents("data/amends.dat", 0, FILE_APPEND|LOCK_EX);
	$_SESSION["successmessage"] = "Post deleted OK";
	header("Location: /admin");
	exit(0);
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Delete Post</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

</head>

<body>
<h1>Delete post</h1>

<?php
include_once("menu.php");
?>

Are you sure you want to delete <strong><?php echo $post["Title"]; ?></strong>, this cannot be undone?
<br />
<a href="?uid=<?php echo $post["UID"];?>&confirm=1" class="button">YES - DELETE</a>
<a href="/admin" class="button">NO</a>

</body>
</html>