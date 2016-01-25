<?php

/*
    image_scaler.php
     
    Takes an image and returns it scaled to the given size
    Image must be a PNG or a JPEG, otherwise it will simply bounce the image
 
    Requires:
        $SRC : Location of image
        $Width, Height : required dimensions
        (if Width or Height is ommitted, it is scaled proportionally)
 
*/
 
$data_path = $_SERVER["DOCUMENT_ROOT"]."/uploads/";
ini_set("memory_limit", "64M") ;
 
 
 $_REQUEST["src"] = str_replace("..", "", $_REQUEST["src"]);
 
$SRC = "$data_path" . $_REQUEST["src"] ;


$Width1 = isset($_REQUEST["w"]) ?  $_REQUEST["w"] : false;
$Height1 = isset($_REQUEST["h"]) ? $_REQUEST["h"] : false;
 
 
// /images/products/image_grabber.php?src=/images/products/bertha/master.jpg&amp;w=280
// Added to save images once and then just redirect if needed...
$processedImageName = "_w_".$Width1."_h_".$Height1 . "_" . str_replace("/","",preg_replace('[^0-9a-zA-Z-.]',"",$_REQUEST["src"]));

if(file_exists($data_path . "/thumbs/" .$processedImageName))
{
  header("Location: " . "/uploads/thumbs/" .$processedImageName);
  exit(0);
}
//echo $processedImageName;
//exit(); 
 
 
 
$Info = getimagesize($SRC) ;
$Width2 = $Info[0] ;
$Height2 = $Info[1] ;
$Type = $Info[2] ;
 
if($Type >3 || $Type <1)
{
  echo "Bad File request!";
  exit(0);
}
 
 
$Ratio = $Height2 / $Width2 ;
if ( $Height1 and !$Width1 ) {
    $Width1 = $Height1 / $Ratio ;
 
} elseif ( $Width1 and !$Height1 ) {
    $Height1 = $Ratio * $Width1 ;
 
} elseif ( !$Height1 and !$Width1 ) {
    $Height1 = $Height2 ;
    $Width1 = $Width2 ;
 
}
 
 
if ( $Width1 == $Width2 and $Height1 == $Height2 ) {
    if ( $Type == 1 )
        header("Content-type: image/gif") ;
    elseif ( $Type == 2 )
        header("Content-type: image/jpeg") ;
    elseif ( $Type == 3 )
        header("Content-type: image/png") ;
 
    $F = fopen($SRC, "rb") ;
    fpassthru($F) ;
    fclose($F) ;
    exit ;
 
} elseif ( $Type == 2 )
    $SRC = imagecreatefromjpeg($SRC) or die("Could not create from $SRC") ;
 
elseif ( $Type == 3 )
    $SRC = imagecreatefrompng($SRC) or die("Could not create from $SRC") ;
 
else {
    //  Cannot do GIF's, so bug-out now
    header("Content-type: image/gif") ;
    $F = fopen($SRC, "rb") ;
    fpassthru($F) ;
    fclose($F) ;
    exit ;
}
 
$Temp = imagecreatetruecolor( $Width1, $Height1 ) or die("Could not create $Width1 x $Height1 image") ;
 
imagecopyresampled( $Temp, $SRC, 0,0, 0,0, $Width1,$Height1, $Width2,$Height2) ;
//$Temp = imagecopyresized( $Temp, $SRC, 0,0, 0,0, $Width1,$Height1, $Width2,$Height2) ;
 
ob_start(); // start a new output buffer
   imagejpeg( $Temp, null, 90 );
   $ImageData = ob_get_contents();
   $ImageDataLength = ob_get_length();
ob_end_clean(); // stop this output buffer
 
//echo $ImageDataLength;
// exit();
$Date = date("D, d M Y") ;
header("Pragma: cache") ;
header("Last-Modified: $Date 00:00:00 GMT") ;
header("Content-type: image/jpeg") ;
header("Content-Length: ".$ImageDataLength);
echo $ImageData;
//imagejpeg( $Temp, "", 90 ) ;
 
// exit();
// Save it for future usage
$handle = fopen($data_path . "/thumbs/" .$processedImageName, 'a');
fwrite ($handle, $ImageData );
 
?>
