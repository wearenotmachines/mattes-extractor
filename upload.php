<?php
require "vendor/autoload.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Upload an image</title>
</head>
<body>
	<form method="post" enctype="multipart/form-data" action="save.php">
		<div>
			<p>What do you want to do with the image?</p>
			<label for="saveAction">Extract the palette <input type="radio" name="extract" value="1" checked /></label>
			<label for="saveAction">Just save the image <input type="radio" name="extract" value="0" /></label>
		</div>
		<div>
			<p>What kind of output do you want?</p>
			<label for="outputAction">Just the JSON <input type="radio" name="preview" value="0" checked /></label>
			<label for="outputAction">Show a preview <input type="radio" name="preview" value="1" /></label>
		</div>
		<div>
			<p>What size palette do you want?</p>
			<input type="number" min="1" max="32" step="1" value="4" name="paletteSize" />
			<div>
				<label>Select your file</label>
				<input type="file" name="upload" />
			</div>
			<input type="submit" value="Upload it" />
		</form>
	</body>
	</html>