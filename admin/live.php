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
$content = "";



?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Live posts</title>
<link rel="stylesheet" type="text/css" href="admin.css" />

</head>

<body>
<h1>Live posts</h1>
<?php
include_once("menu.php");
?>
<table>
<?php
$posts = loadPosts("LIVE", "DATE");

foreach($posts as $p)
	echo "<tr><td width='1'>{$p["Publish Date"]}</td><td>{$p["Title"]}</td><td width='1'><nobr><a href='edit.php?uid={$p["UID"]}' class='button'>Edit</a> <a href='delete.php?uid={$p["UID"]}' class='button'>Delete</a></nobr></td>";

?>
</table>

</body>
</html>