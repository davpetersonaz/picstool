<?php
include_once('../../config.php');
$pictures = new Pictures;

//original pics get 1x1 350px thumbnails for pictures.php
foreach($pictures->getImagesAlphabetical() as $filename){
	if(!file_exists(Pictures::THUMBS_DIR.$filename)){
		$pictures->createThumbnail($filename, Pictures::PICS_DIR, 1, 1, Pictures::THUMB_SIZE, Pictures::THUMB_SIZE, Pictures::THUMBS_DIR);
	}
}

//best-of-show pics get 16x9 608x360 thumbnails
foreach($pictures->getBestPics() as $filename){
	if(!file_exists(Pictures::THUMBS_DIR.$filename)){
		$pictures->createThumbnail($filename, Pictures::BEST_PICS_DIR, 16, 9, Pictures::BEST_THUMB_W, Pictures::BEST_THUMB_H, Pictures::THUMBS_DIR);
	}
}

//home-page pics get 4x3 800x600 "thumbnails"
foreach($pictures->getHomePics() as $filename){
	if(!file_exists(Pictures::THUMBS_DIR.$filename)){
		$pictures->createThumbnail($filename, Pictures::HOME_PICS_DIR, 4, 3, Pictures::HOME_THUMB_W, Pictures::HOME_THUMB_H, Pictures::THUMBS_DIR);
	}
}

exit;