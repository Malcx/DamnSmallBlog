<?php
function loadDATA($slug)
{
	$CURRENTFILE = array();
	$CURRENTFILE["content"] = "";

	$str = file_get_contents("data/" . $slug . ".md");

	$lines = explode("\n", $str);
	$inContent = false;
	foreach($lines as $l)
	{

		if($inContent)
			$CURRENTFILE["content"] .= $l."\n";
		else
		{
			$b = explode(": ", $l, 2);
			if(count($b)<2)
				$inContent = true;
			else
				$CURRENTFILE[$b[0]] = $b[1];
				
		}
	}
	
	return($CURRENTFILE);
}


function getPostByUID($uid)
{
	$files = scandir("data");
	foreach($files as $f)
	{
		$info = new SplFileInfo($f);
			
		if($info->getExtension() == "md")
		{
			$post = loadDATA($info->getBasename(".md"));
			if($post["UID"] == $uid)
				return $post;
		}
	}
	
	return;
}



function loadPosts($type="LIVE", $orderBy = "NAME"){
	$posts = array();
	$files = scandir("data");
	foreach($files as $f)
	{
		$info = new SplFileInfo($f);
		if($info->getExtension() == "md")
		{
			$post = loadDATA($info->getBasename(".md"));
			if(trim($post["Status"]) == trim($type))
				$posts[] = $post;
		}
	}

	// Sort by?
	if($orderBy == "DATE")
		usort($posts, "cmp_by_publishDateDesc");

	if($orderBy == "NAME")
		usort($posts, "cmp_by_titleAsc");
	
	return $posts;


}
function makeSlug($slug)
{
	$slug = preg_replace("/[^A-Za-z0-9-_ ]/", '', $slug);
	$slug = str_replace(" ", "-", $slug);
	return $slug;
}

function saveDATA($uid, $title, $slug, $old_slug, $date, $status, $content, $tags, $inFeed)
{

	$slug = makeSlug($slug);


	$save = false;
	$modifier = "";
	$workingSlug = $slug;
	while(!$save)
	{
		if(file_exists("data/$workingSlug.md"))
		{
			$PAGE = loadDATA($workingSlug);
			if($PAGE["UID"] == $uid)
				$save = true;
		}
		else
			$save = true;

		if($save)
		{
			if(!$uid)
				$uid = md5(time() . rand());
			
$md = "UID: $uid
Title: $title
Slug: $workingSlug
Publish Date: $date
Status: $status
Tags: $tags
In Feed: $inFeed

$content";
			file_put_contents("data/$workingSlug.md", $md);
			
			if($old_slug && ($old_slug != $workingSlug) && file_exists("data/$old_slug.md"))
				unlink("data/$old_slug.md");
			return true;
		}
		else
		{
			$modifier++;
			$workingSlug = $slug . "-" . ($modifier + 1);
		}
	}


}



function saveConfigFile($args){ // pass an array of just the values that have changed

	$_CONFIG = array();

	if(file_exists("config.php"))
		include("config.php");

	// Create a basic config file
	$config = '<'.'?php' . "\n";
	$config .= '$_CONFIG = array();' . "\n";


	
	$config .= "$"."_CONFIG['blogname'] = \"";
	  $config .= isset($args["blogname"]) ? $args["blogname"] : (isset($_CONFIG["blogname"]) ? $_CONFIG["blogname"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['username'] = \"";
	  $config .= isset($args["username"]) ? $args["username"] : (isset($_CONFIG["username"]) ? $_CONFIG["username"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['password'] = \"";
	  $config .= isset($args["password"]) ? md5("dsb_" . $args["password"]) : (isset($_CONFIG["password"]) ? $_CONFIG["password"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['baseDomain'] = \"";
	  $config .= isset($args["baseDomain"]) ? $args["baseDomain"] : (isset($_CONFIG["baseDomain"]) ? $_CONFIG["baseDomain"] : "http:/"."/".$_SERVER["SERVER_NAME"]);
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['fullOrPart'] = \"";
	  $config .= isset($args["fullOrPart"]) ? $args["fullOrPart"] : (isset($_CONFIG["fullOrPart"]) ? $_CONFIG["fullOrPart"] : "FULL");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['itemsPerPage'] = ";
	  $config .= isset($args["itemsPerPage"]) ? $args["itemsPerPage"] : (isset($_CONFIG["itemsPerPage"]) ? $_CONFIG["itemsPerPage"] : 5);
	  $config .= ";\n";
	$config .= "$"."_CONFIG['menu'] = \"";
	  $config .= isset($args["menu"]) ? $args["menu"] : (isset($_CONFIG["menu"]) ? $_CONFIG["menu"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['logoURL'] = \"";
	  $config .= isset($args["logoURL"]) ? $args["logoURL"] : (isset($_CONFIG["logoURL"]) ? $_CONFIG["logoURL"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['copyright'] = \"";
	  $config .= isset($args["copyright"]) ? $args["copyright"] : (isset($_CONFIG["copyright"]) ? $_CONFIG["copyright"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['googleAnalytics'] = \"";
	  $config .= isset($args["googleAnalytics"]) ? $args["googleAnalytics"] : (isset($_CONFIG["googleAnalytics"]) ? $_CONFIG["googleAnalytics"] : "");
	  $config .= "\";\n";
	$config .= "$"."_CONFIG['milligram'] = ";
	  $config .= isset($args["milligram"]) ? $args["milligram"] : (isset($_CONFIG["milligram"]) ? $_CONFIG["milligram"] : "");
	  $config .= ";\n";
	$config .= "$"."_CONFIG['social'] = \"";
	  $config .= isset($args["social"]) ? $args["social"] : (isset($_CONFIG["social"]) ? $_CONFIG["social"] : "&HackerNews&Reddit&Facebook&Twitter");
	  $config .= "\";\n";

	$config .= "?".">";

	file_put_contents("config.php", $config);
	
	
}


function cmp_by_publishDateDesc($a, $b) {
  $aV = str_replace("/", "", $a["Publish Date"]);
  $bV = str_replace("/", "", $b["Publish Date"]);
  return $bV - $aV;
}


function cmp_by_titleAsc($a, $b) {
  return $a["Title"] > $b["Title"];
}




?>