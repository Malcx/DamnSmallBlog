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
include_once("Parsedown-with-video.php");

$Parsedown = new Parsedown();

$template = file_get_contents("template.html");
$original_files = scandir("../blog"); // files as it stands now - delete old ones after that are no longer backed by an md file 
$new_files = array();

$tagPages = array(); // pages listing by category


$googleAnalytics = ($_CONFIG["googleAnalytics"]) ? "<script>  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');  ga('create', '{$_CONFIG["googleAnalytics"]}', 'auto');  ga('send', 'pageview');</script>" : "";

$copyright = $_CONFIG["copyright"] ? "<small>{$_CONFIG["copyright"]}<br /></small>" : "";



$menu = "<table width=\"100%\"><tr>";
if($_CONFIG["logoURL"])
	$menu .= "<td><a href=\"/\"><img src=\"{$_CONFIG["logoURL"]}\" alt=\"{$_CONFIG["blogname"]}\" class=\"logo\" /></a></td>";

if($_CONFIG["menu"])
	$menu .= "<td align=\"right\">" . $Parsedown->text($_CONFIG["menu"]) . "</td>";

$menu .= "</tr></table>";


$menu = "<table width=\"100%\"><tr>";
if($_CONFIG["logoURL"])
	$menu .= "<td><a href=\"/\"><img src=\"{$_CONFIG["logoURL"]}\" alt=\"{$_CONFIG["blogname"]}\" class=\"logo\" /></a></td>";

if($_CONFIG["menu"])
	$menu .= "<td align=\"right\" valign=\"top\">" . Parsedown::instance()->line($_CONFIG["menu"]) . "</td>";

$menu .= "</tr></table>";



// Generate actual posts
$posts = loadPosts("LIVE", "DATE");
foreach($posts as $p)
{

	$directURL = $_CONFIG["baseDomain"] ? $_CONFIG["baseDomain"] : "http://{$_SERVER["HTTP_HOST"]}";
	$directURL .= "/blog/{$p["Slug"]}.html";
	$shareTitle = urlencode($p["Title"]);
	
	$sharing = "";
	if(strpos($_CONFIG["social"], "HackerNews"))
		$sharing .= "[<a href=\"http://news.ycombinator.com/submitlink?u={$directURL}=&t=".str_replace("+", " ", $shareTitle)."\" target=\"_blank\">Hacker News</a>] ";
	if(strpos($_CONFIG["social"], "Facebook"))
		$sharing .= "[<a href=\"http://www.facebook.com/sharer.php?u={$directURL}&t={$shareTitle}\" target=\"_blank\">Facebook</a>] ";
	if(strpos($_CONFIG["social"], "Reddit"))
		$sharing .= "[<a href=\"http://reddit.com/submit?url={$directURL}&title={$shareTitle}\" target=\"_blank\">Reddit</a>] ";
	if(strpos($_CONFIG["social"], "Twitter"))
		$sharing .= "[<a href=\"https://twitter.com/intent/tweet?text={$shareTitle}&url={$directURL}\" target=\"_blank\">Twitter</a>] ";
	if($sharing)
		$sharing = "<strong>Share this on:</strong><br />" . $sharing . "<br />";
	
	$output = str_replace("{title}", $p["Title"], $template);
	$output = str_replace("{description}", $p["Tags"] . " - " . $p["Title"], $output);
	$output = str_replace("{tags}", $p["Tags"], $output);
	$output = str_replace("{menu}", $menu, $output);
	$output = str_replace("{copyright}", $copyright, $output);
	$output = str_replace("{googleAnalytics}", $googleAnalytics, $output);
	$output = str_replace("{sharing}", $sharing, $output);
	$output = str_replace("{body}", "<h1>{$p["Title"]}</h1>" . $Parsedown->text($p["content"]), $output);

	// Add to menu pages
	if(strlen($p["Tags"]) > 1)
	{
		$tagList = "<br /><strong>Tags:</strong> ";
		$tags = explode(",", $p["Tags"]);
		foreach($tags as $tag)
		{
			$tagList .= "<a href=\"index-of-".makeSlug(trim($tag)).".html\">".trim($tag)."</a> ";
		}
		$output = str_replace("{tag-list}", $tagList, $output);
	}

	file_put_contents("../blog/{$p["Slug"]}.html", $output);
	$new_files[] = $p["Slug"].".html";
	
	
	
	// Add to menu pages
	if(strlen($p["Tags"]) > 1)
	{
		$tags = explode(",", $p["Tags"]);
		foreach($tags as $tag)
		{
			$tag = makeSlug(trim($tag));
			if(!isset($tagPages[$tag]))
				$tagPages[$tag] = array();
			$tagPages[$tag][] = array($p["Publish Date"], $p["Title"], $p["Slug"]);
		}
	}
}

// make index pages...
foreach($tagPages as $tpk => $tpv)
{
	$myBody = "";
	foreach($tpv as $p)
		$myBody .= "<a href=\"{$p[2]}.html\">{$p[1]}</a> <small>({$p[0]})</small><br />";
	
	$output = str_replace("{title}", "Index of posts tagged with: $tpk", $template);
	$output = str_replace("{tags}", $tpk, $output);
	$output = str_replace("{description}", $tpk, $output);
	$output = str_replace("{menu}", $menu, $output);
	$output = str_replace("{copyright}", $copyright, $output);
	$output = str_replace("{googleAnalytics}", $googleAnalytics, $output);
	$output = str_replace("{sharing}", "", $output);
	$output = str_replace("{tag-list}", "", $output);
	$output = str_replace("{body}", "<h1>Index of posts tagged with: $tpk</h1>" . $myBody, $output);
	file_put_contents("../blog/index-of-{$tpk}.html", $output);
	$new_files[] = "index-of-" . $tpk.".html";
}


// Main subjects listing:
ksort($tagPages);
$myBody = "";
foreach($tagPages as $tpk => $tpv)
{
	$myBody .= "<a href=\"index-of-$tpk.html\">$tpk</a><br />";
	
}
$output = str_replace("{title}", "Index of all tags", $template);
$output = str_replace("{tags}", "", $output);
$output = str_replace("{menu}", $menu, $output);
$output = str_replace("{copyright}", $copyright, $output);
$output = str_replace("{googleAnalytics}", $googleAnalytics, $output);
$output = str_replace("{sharing}", "", $output);
$output = str_replace("{tag-list}", "", $output);
$output = str_replace("{body}", "<h1>Index of all tags</h1>" . $myBody, $output);
file_put_contents("../blog/index-of-ALL-TAGS.html", $output);
$new_files[] = "index-of-ALL-TAGS.html";


// TODO make homepage and paginated pages
$pageOn = 0;
$postCount = 0;
$thisBody = "";
$postsWIP = array();
// Strip posts that are not in feed
foreach($posts as $p)
	if($p["In Feed"] == "YES")
		$postsWIP[] = $p;

while(count($postsWIP))
{
	$p = array_shift($postsWIP);
	$thisPostContent = getExtract($p["content"], $_CONFIG['fullOrPart'], "/blog/{$p["Slug"]}.html");
	$thisBody .= "<h1>{$p["Title"]}</h1>" . $Parsedown->text($thisPostContent);
	$postCount++;
	if($postCount != $_CONFIG["itemsPerPage"] && count($postsWIP))
		$thisBody .= "<hr /><hr />";
	if($postCount == $_CONFIG["itemsPerPage"])
	{
		$pageTitle = "{$_CONFIG["blogname"]} Page $pageOn";
		if(!$pageOn)
			$pageTitle = "{$_CONFIG["blogname"]} blog - built with DSB";

		$thisFooter = "";
		if(count($postsWIP) || $pageOn)
			$thisFooter = "<hr /><table width=\"100%\"><tr><td>" . ($pageOn ? "<a href=\"/blog/index-".($pageOn-1).".html\">&laquo;Previous posts</a>" : "") . "</td><td align=\"right\">" . (count($postsWIP) ? "<a href=\"/blog/index-".($pageOn+1).".html\">More posts &raquo;</a>" : "") . "</td></tr></table>";

		$output = str_replace("{title}", $pageTitle, $template);
		$output = str_replace("{tags}", "", $output);
		$output = str_replace("{menu}", $menu, $output);
		$output = str_replace("{copyright}", $copyright, $output);
		$output = str_replace("{googleAnalytics}", $googleAnalytics, $output);
		$output = str_replace("{sharing}", "", $output);
		$output = str_replace("{tag-list}", "", $output);
		$output = str_replace("{body}", $thisBody.$thisFooter, $output);
		file_put_contents("../blog/index-$pageOn.html", $output);
		$new_files[] = "index-$pageOn.html";
		if($pageOn == 0)
			file_put_contents("../index.html", $output);
		
		$pageOn++;
		$postCount = 0;
		$thisBody = "";
	}


}

if(strlen($thisBody))
{
	$pageTitle = "{$_CONFIG["blogname"]} Page $pageOn";
	if(!$pageOn)
		$pageTitle = "{$_CONFIG["blogname"]} blog - built with DSB";

	$thisFooter = "";
	if(count($postsWIP) || $pageOn)
		$thisFooter = "<hr /><table width=\"100%\"><tr><td>" . ($pageOn ? "<a href=\"/blog/index-".($pageOn-1).".html\">&laquo;Previous posts</a>" : "") . "</td><td align=\"right\">" . (count($postsWIP) ? "<a href=\"/blog/index-".($pageOn+1).".html\">More posts &raquo;</a>" : "") . "</td></tr></table>";

	$output = str_replace("{title}", $pageTitle, $template);
	$output = str_replace("{tags}", "", $output);
	$output = str_replace("{menu}", $menu, $output);
	$output = str_replace("{copyright}", $copyright, $output);
	$output = str_replace("{googleAnalytics}", $googleAnalytics, $output);
	$output = str_replace("{sharing}", "", $output);
	$output = str_replace("{tag-list}", "", $output);
	$output = str_replace("{body}", $thisBody.$thisFooter, $output);
	file_put_contents("../blog/index-$pageOn.html", $output);
	$new_files[] = "index-$pageOn.html";
	if($pageOn == 0)
		file_put_contents("../index.html", $output);
	
}



// Delete old files
foreach($original_files as $f)
	if($f != "." && $f != "..")
		if(in_array($f, $new_files))
			if(($key = array_search($f, $original_files)) !== false) 
			    unset($original_files[$key]);

foreach($original_files as $f)
	if($f != "." && $f != "..")
		unlink("../blog/$f");

@file_put_contents("data/amends.dat", 0, LOCK_EX);
$_SESSION["successmessage"] = "Site generated!";
header("Location: /admin");
exit(0);



function getExtract($content, $amount, $slug){
	if($amount == "FULL")
		return $content;

	$newStr = "";
	$arr = explode("\n", $content);
	if(count($arr) > 10) { // you've got more than 4 line breaks
		$arr = array_splice($arr, 0, 10); // reduce the lines to four
	   	foreach($arr as $line) { $newStr .= $line; } // store them all in a string
	   	$newStr .= "\n\n*[View More &gt;]($slug)*";
	} else {
	   	$newStr = $content; // there was less or equal to four rows so to us it'all ok
	}

	return $newStr;
}


?>