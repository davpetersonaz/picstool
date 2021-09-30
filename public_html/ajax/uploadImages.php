<?php
include_once('../../config.php');
$imageGateway = new ImageGateway();
logDebug('ajax/uploadImages POST: '.var_export($_POST, true));
$pictures = $pictureFactory->newInstance();
//logDebug('Pictures: '.var_export($pictures, true));

//if(isset($_POST['add_home'])){
//	$imageGateway->updateHomepage($_POST['add_home'], 1);
//}else
//	
//if(isset($_POST['add_best'])){
//	$imageGateway->updateBestpage($_POST['add_best'], 1);
//}else
//	
//if(isset($_POST['add_feat'])){
//	$imageGateway->updateFeaturepage($_POST['add_feat'], 1);
//	$imageGateway->updateBestpage($_POST['add_best'], 0);
//}else

if(isset($_POST['caption'], $_POST['text'])){
	$imageGateway->updateCaption($_POST['caption'], $_POST['text']);
}else

if(isset($_POST['delete'])){
	$imageToDelete = $imageGateway->getImageByid($_POST['delete']);
	$imageGateway->deleteImage($_POST['delete']);
	if(file_exists(Pictures::BEST_PICS_PATH.$imageToDelete['filename'])){ unlink(Pictures::BEST_PICS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::FEAT_PICS_PATH.$imageToDelete['filename'])){ unlink(Pictures::FEAT_PICS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::HOME_PICS_PATH.$imageToDelete['filename'])){ unlink(Pictures::HOME_PICS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::ORIG_PICS_PATH.$imageToDelete['filename'])){ unlink(Pictures::ORIG_PICS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::THUMBS_PATH.$imageToDelete['filename'])){ unlink(Pictures::THUMBS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::VIDS_PATH.$imageToDelete['filename'])){ unlink(Pictures::VIDS_PATH.$imageToDelete['filename']); }
	if(file_exists(Pictures::VIDS_THUMBS_PATH.$imageToDelete['filename'])){ unlink(Pictures::VIDS_THUMBS_PATH.$imageToDelete['filename']); }
}else

if(isset($_POST['rotate_left'])){
	$pictures->rotateImageCounterclockwise($_POST['rotate_left']);
}else

if(isset($_POST['rotate_right'])){
	$pictures->rotateImageClockwise($_POST['rotate_right']);
}else

//add_feat, add_best, add_home checkboxes
if(isset($_POST['id'], $_POST['page'], $_POST['on'])){
	if($_POST['page'] === 'home'){
		$imageGateway->updateHomepage($_POST['id'], $_POST['on']);
		if(!$_POST['on']){
			unlink(Pictures::HOME_PICS_PATH.$imageToDelete['filename']);
		}
	}elseif($_POST['page'] === 'best'){
		$imageGateway->updateBestpage($_POST['id'], $_POST['on']);
		if(!$_POST['on']){
			unlink(Pictures::BEST_PICS_PATH.$imageToDelete['filename']);
		}
	}elseif($_POST['page'] === 'feat'){
		$imageGateway->updateFeaturepage($_POST['id'], $_POST['on']);
		if(!$_POST['on']){
			unlink(Pictures::FEAT_PICS_PATH.$imageToDelete['filename']);
		}
	}
}

echo 'done';
exit;