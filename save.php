<?php
error_reporting(E_ALL);
ini_set("display_errors", "On");
header("Content-type: application/json");

require "vendor/autoload.php";

use Intervention\Image\ImageManagerStatic as Image;

//change this to the folder you want your images saved in
$saveFolder = "images";
//set this variable to false if you just want to upload files
$extractColours = empty($_POST['extract']) ? 0 : (boolean)$_POST['extract'];
//the number of colours to extract into the palette - this just gets passed on to extract.php but needs to be set here - you should make this a POST variable
$paletteSize = isset($_POST['paletteSize']) ? (int)$_POST['paletteSize'] : 4;

//what do we do if and when we've extracted the colours - just spit out the JSON or show a preview
$preview = isset($_POST['preview']) ? (boolean) $_POST['preview'] : false;

//change this if you want to use some kind of rule to set the saved image filename - by deafult it will use the original filename
$saveFilename = $_FILES['upload']['name'];

//first set the path to the folder to save the images in
$savePath = getcwd()."/".$saveFolder;
//check that the save path folder is writable or throw an error
if (!is_writable($savePath)) {
	echo json_encode(array("status"=>0, "message"=>"", "error"=>"Cannot save images in ".$savePath));
	exit;
}

//now add the filename for the actual save - this is the absolute path to the file that you have set up with differrent bits above
$savePath .= "/".$saveFilename;

$image = Image::make($_FILES['upload']['tmp_name']);
if ($image->save($savePath)) {
	if ($extractColours) {
		header("Location: extract.php?pathToImage=".urlencode("/".$saveFolder."/".pathinfo($savePath, PATHINFO_BASENAME))."&paletteSize=".$paletteSize.($preview ? "&preview" : ""));
		exit;
	} else {
		echo json_encode(array("status"=>1, "error"=>"", "message"=>"Image saved to ".$savePath));
		exit;
	}
} else {
	echo json_encode(array("status"=>0,"message"=>"", "error"=>"Saving the image failed"));
	exit;
}