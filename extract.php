<?php
error_reporting(E_ALL);
ini_set("display_errors", "On");
require "vendor/autoload.php";
use League\ColorExtractor\Client as ColorExtractor;


$output = [
	"status" => 1,
	"message" => "",
	"error" => "",
	"palette" => []	
];

//what should we do when we've extracted the palette? The default is to echo JSON showing it as an array - otherwise if 'preview' is passed in the url - show a little preview
$preview = isset($_GET['preview']);

//what palette size was requested - if none specified then set to 4
$paletteSize = !empty($_GET['paletteSize']) ? $_GET['paletteSize'] : 4;

//check that a path to an image is set
if (empty($_GET['pathToImage'])) {
	$output["error"] = "No path to image was sent";
	echo json_encode($output, JSON_PRETTY_PRINT);
	exit;
}

//the path was urlencoded so we could pass it in a url - decode it so that we can use it as a filesystem path
$imagePath = urldecode($_GET['pathToImage']);

//check that we can read the image at the path - expanded the path using getcwd() to get the filesystem path not the relative path
if (!is_readable(getcwd().$imagePath)) {
	$output['status'] = 0;
	$output['error'] = "The image at $imagePath is not readable";
	echo json_encode($output, JSON_PRETTY_PRINT);
	exit;
}

//what kind of image is at $imagePath? remember we need the absolute filesystem path here - which is what getcwd() is doing
$type = image_type_to_mime_type(exif_imagetype(getcwd().$imagePath));

//we can only do jp(e)g, png and gif so if these are not in the mime/type let's bail out
if (!preg_match("/jp(e)?g|gif|png/", $type)) {
	$output['status'] = 0;
	$output['error'] = "Palettes from $type images cannot be extracted";
	echo json_encode($output, JSON_PRETTY_PRINT);
	exit;
}

//process the image using a ColorExtractor
$c = new ColorExtractor;
switch ($type) {

	case "image/png":
		$image = $c->loadPng(getcwd().$imagePath);
	break;

	case "image/gif":
		$image = $c->loadGif(getcwd().$imagePath);
	break;

	case "image/jpeg":
	case "image/jpg":
	case "image/jpe":
	default:
		$image = $c->loadJpeg(getcwd().$imagePath);

}

$output['palette'] = $image->extract($paletteSize);
$output['message'] = "Palette for $imagePath extracted";

if (!$preview) {
//if we're not showing a preview we're just outputting json
header("Content-type: application/json");
echo json_encode($output, JSON_PRETTY_PRINT);

} else {
	echo '<div class="imagePreview">
			<img src="'.$imagePath.'" width="400" />
		  </div>
		<div class="palette" style="max-width:800px">';
	foreach ($output['palette'] AS $colour) { 
		echo '<div class="swatch" style="width:50px; height:50px; float:left; background-color:'.$colour.'"></div>';
	}
	echo '</div>';
}