<?php

/* TODO: allow larger sizes because videos are so much bigger */

logDebug('uploadFiles');
logDebug('FILES: '.var_export($_FILES, true));
logDebug('POST: '.var_export($_POST, true));
if(!$alreadyLoggedIn){ exit; }
$imageGateway = new ImageGateway();
$pictures = $pictureFactory->newInstance();
//logDebug('Pictures: '.var_export($pictures, true));

//handle submitted upload form
if(isset($_POST["submitFiles"]) && !empty($_FILES['files'])){
	
	/* this is not called until all the filse are uploaded from the form */
	
	$errors = array();
	$uploadedFiles = array();
	$pictureExtensions = array("jpeg", "jpg", "png", "gif");
	$videoExtensions = array("mp4", "mpg");
	$extensions = array_merge($pictureExtensions, $videoExtensions);
	$bytes = 1024;
	$kb = 1024;
	$megs = 10;
	$totalBytes = $megs * $bytes * $kb;

	$counter = 0;
	foreach($_FILES['files']['tmp_name'] as $key=>$tmp_name){
		logDebug('processing file: '.$tmp_name);
		$temp = $_FILES['files']['tmp_name'][$key];
		$name = $_FILES['files']['name'][$key];
		if(empty($temp)){
			logDebug("[{$temp}] is empty, continuing");
			continue;
		}
		$counter++;
		$uploadOk = true;
		if($_FILES['files']['size'][$key] > $totalBytes){
			$uploadOk = false;
			array_push($errors, $name." file size is larger than the {$megs}mb.");
			logDebug("[{$name}] is too big, continuing");
			continue;
		}
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		if(in_array(strtolower($ext), $extensions) === false){
			$uploadOk = false;
			array_push($errors, $name." invalid file type.");
			logDebug("[{$name}] invalid type, continuing");
			continue;
		}
		
		if($imageGateway->doesNameAlreadyExist($name)){
			$uploadOk = false;
			array_push($errors, $name." is already in the database, rename the file if you wish to upload it.");
			logDebug("[{$name}] already in db, continuing");
			continue;
		}
		
		if($uploadOk === true){
			logDebug("[{$name}] uploadOK!");
			if(in_array(strtolower($ext), $videoExtensions) === false){
				$pictures->addUploadedPicture($temp, $name);
			}else{
				$pictures->addUploadedVideo($temp, $name);
			}
			array_push($uploadedFiles, $name);
		}
	}

	if($counter > 0){
		echo '<div class="upload-results">';
		if(count($errors) > 0){
			echo "<b>Errors:</b>";
			echo "<br/><ul>";
			foreach($errors as $error){
				echo "<li>{$error}</li>";
			}
			echo "</ul><br/>";
		}
		if(count($uploadedFiles) > 0){
			echo "<b>Uploaded Files:</b>";
			echo "<br/><ul>";
			foreach($uploadedFiles as $fileName){
				echo "<li>{$fileName}</li>";
			}
			echo "</ul><br/>";
			echo count($uploadedFiles)." file(s) successfully uploaded.";
		}								
		echo '</div>';
	}else{
		echo "Please, Select file(s) to upload.";
	}
	
	//this was an attempt to try to prevent the resubmission of the form on page-refresh
	unset($_POST['submitFiles'], $_FILES['files'], $uploadedFiles, $errors);
}

$sorting = (isset($_GET['s']) && in_array($_GET['s'], array('a', 'c', 'u')) ? $_GET['s'] : 'u');
if($sorting === 'a'){
	$images = $pictures->getImagesAlphabetical();
}elseif($sorting === 'c'){
	$images = $pictures->getImagesChronological();
}else{
	$images = $pictures->getImagesLastUploaded();
}
?>

<div class='uploadfiles_page'>
	<form id='formUploadFile' name="formUploadFile" method="POST" enctype="multipart/form-data">		
		<input type="file" name="files[]" multiple="multiple" />
		<input id="file-submit-button" type="submit" value="Upload File(s)" name="submitFiles"/>
	</form>		

	<!-- display sorting controls -->
	<hr />

	<div class='sorting-controls'>
		Sort by:<br />
		<label for='sort_alpha'><input  type="radio" id="sort_alpha"  name="sorting" value="alpha"  <?=($sorting === 'a' ? 'checked' : '')?>> alphabetical</label> &nbsp; 
		<label for='sort_chrono'><input type="radio" id="sort_chrono" name="sorting" value="chrono" <?=($sorting === 'c' ? 'checked' : '')?>> chronological</label> &nbsp;
		<label for='sort_upload'><input type="radio" id="sort_upload" name="sorting" value="upload" <?=($sorting === 'u' ? 'checked' : '')?>> upload date</label> &nbsp;
	</div>

	<!-- display last-uploaded images -->
	<div class='pic-strip'>
<?php foreach($images as $image){ ?>
		<div class="pic-pad">
			<?php include 'includes/image_display.php'; ?>
		</div>
<?php } ?>
		<div class='clear'></div>	<!-- TODO:  uh, why this? -->
	</div>
</div>

<script>
$(document).ready(function(){
	//NOTE: other javascript is in included file: pictures.js
	
	//prevent the resubmission of the form on page-refresh
	if(window.history.replaceState){
		window.history.replaceState(null, null, window.location.href);
	}
	
	//TODO: IS THERE A WAY TO CHECK IF THEY ALREADY EXIST IN DB, BEFORE UPLOADING THEM TO THE WEBSERVER?
	
	$('#sort_alpha').on('click', function(){
		window.location.href = '/uploadfiles.php?s=a';
	});
	
	$('#sort_chrono').on('click', function(){
		window.location.href = '/uploadfiles.php?s=c';
	});
	
	$('#sort_upload').on('click', function(){
		window.location.href = '/uploadfiles.php';
	});
	
	$('#file-submit-button').on('click', function(){
		$('.upload-results').css('display', 'none');
		//hide the output from the last upload (if any)
	});
});
</script>
