<?php
class Pictures{

	public function __construct(){
		$this->imageGateway = new ImageGateway();
	}
	
	public function addBestpagePic($filename){
		$origFile = self::ORIG_PICS_PATH.$filename;
		if(file_exists($origFile)){
			if(!file_exists(self::BEST_PICS_PATH.$filename)){
				$imagick = new Imagick();
				$imagick->readImage($origFile);
								
				//best-of-show page pics -- ratio: 16 x 9, specifically: ??? x 300  (don't worry about ratio, just size for the page)
				Imagick::scaleImage(0, 300, true);
				
				$imagick->writeimage(self::BEST_PICS_PATH.$filename);
				$this->imagickClearDestroy($imagick);
			}
		}else{
			logDebug('addBestpagePic: file does not exist: '.var_export($origFile, true));
		}
	}
	
	public function addFeaturepagePic($filename){
		$origFile = self::ORIG_PICS_PATH.$filename;
		if(file_exists($origFile)){
			if(!file_exists(self::FEAT_PICS_PATH.$filename)){
				$imagick = new Imagick();
				$imagick->readImage($origFile);
				
				//feature pics (on best-of-show page) -- ratio: 3.3 x 1 (stretches across entire screen so the height will vary)
				$width = Imagick::getImageWidth();
				$height = Imagick::getImageHeight();
				$cropHeight = intval($width / 3);
				if($height > $cropHeight){
					$imagick->cropImage($width, $cropHeight, 0, intval(($height - $cropHeight)/2));
				}
				
				$imagick->writeimage(self::FEAT_PICS_PATH.$filename);
				$this->imagickClearDestroy($imagick);
			}
		}else{
			logDebug('addFeaturepagePic: file does not exist: '.var_export($origFile, true));
		}
	}
	
	public function addHomepagePic($filename){
		$origFile = self::ORIG_PICS_PATH.$filename;
		if(file_exists($origFile)){
			if(!file_exists(self::HOME_PICS_PATH.$filename)){
				$imagick = new Imagick();
				$imagick->readImage($origFile);
				
				/* TODO: manipulate the image for homepage-display */
				//home page pics -- ratio: 4 x 3, specifically: 800 x 600
				
				$imagick->writeimage(self::HOME_PICS_PATH.$filename);
				$this->imagickClearDestroy($imagick);
			}
		}else{
			logDebug('addHomepagePic: file does not exist: '.var_export($origFile, true));
		}
	}
	
	public function addUploadedPicture($tempfile, $name){
		$originalFilename = $name;
		$uniqueName = $this->makeNameUnique($name, self::ORIG_PICS_PATH);
		$destination = self::ORIG_PICS_PATH.$uniqueName;
		logDebug("file_exists({$tempfile}): ".var_export(file_exists($tempfile), true));
		logDebug("file_exists({$destination}): ".var_export(file_exists($destination), true));
		logDebug("moving temp [{$tempfile}] to [{$destination}]");
		if(!move_uploaded_file($tempfile, $destination)){
			logDebug("ERROR: unable to move uploaded file: ".$tempfile);
		}
		$pathinfo = pathinfo($destination);//dirname, basename, extension, filename
//		logDebug('path_info: '.var_export($pathinfo, true));
		
		$exif = exif_read_data($destination);
		$dateTimeOriginal = (empty($exif) ? filemtime($destination) : $exif['DateTimeOriginal']);
		
		$imagick = new Imagick($destination);
		$imagick->setimageformat('jpg');//or, perhaps just 'setFormat' since the filename was given to the constructor?
		$newFileName = $pathinfo['filename'].'.jpg';
		
		//save image to original directory
		unlink($destination);
		$imagick->writeImage("{$pathinfo['dirname']}/{$newFileName}");
		
		$this->resizeAndWriteAndDestroyImagick($imagick, $newFileName);
		
		$imageId = $this->imageGateway->addNewImage($newFileName, $originalFilename, $dateTimeOriginal);
		logDebug('uploaded image db id: '.var_export($imageId, true));
		return $imageId;
	}
	
	public function addUploadedVideo($tempfile, $name){
		$originalFilename = $name;
		$uniqueName = $this->makeNameUnique($name, self::VIDS_URL);
		$destination = self::VIDS_PATH.$uniqueName;
		logDebug("file_exists({$tempfile}): ".var_export(file_exists($tempfile), true));
//		logDebug("file_exists({$destination}): ".var_export(file_exists($destination), true));
		logDebug("moving temp [{$tempfile}] to [{$destination}]");
		if(!move_uploaded_file($tempfile, $destination)){
			logDebug("ERROR: unable to move uploaded file: ".$tempfile);
		}
		//mp4 is already a compressed video format
		$videoId = $this->imageGateway->addNewVideo($uniqueName, $originalFilename, filemtime($destination));
		logDebug('uploaded video db id: '.var_export($videoId, true));
		return $videoId;
	}
	
//	protected function doesBestPicExist($filename){
//		return (file_exists(self::BEST_PICS_PATH.$filename));
//	}
//
//	protected function doesFeaturePicExist($filename){
//		return (file_exists(self::FEAT_PICS_PATH.$filename));
//	}
//
//	protected function doesHomePicExist($filename){
//		return (file_exists(self::HOME_PICS_PATH.$filename));
//	}
	
	protected function imagickClearDestroy($imagick){
		$imagick->clear();
		$imagick->destroy(); 
	}

	protected function makeNameUnique($name, $path){
		$dotpos = strpos($name, '.');
		$newname = substr($name, 0, $dotpos);
		$ext = strtolower(substr($name, $dotpos+1));
		while(file_exists($path.$newname.'.'.$ext)){
			$newname .= rand(0,9);
		}
		$newname .= '.'.$ext;
		logDebug("origname [{$name}] newuniquename [{$newname}]");
		return "{$newname}";
	}
	
	/**
	 * crop the image to given proportions, then resize it to given height/width, and save it to given directory
	 * @param string $filename name of the image file
	 * @param string $dir path of the image file directory
	 * @param int $desiredWidthProportion the width proportion for the final image
	 * @param int $desiredHeightProportion the height proportion for the final image
	 * @param int $thumbWidth the width of the final image
	 * @param int $thumbHeight the height of the final image
	 * @param string $thumbDir path of the final image directory
	 */
	//ex) createThumbnail('somefile.jpg', self::PICS_DIR, 9, 16, self::BEST_THUMB_W, self::BEST_THUMB_H, self::THUMBS_DIR);
	public function createThumbnail($filename, $dir, $desiredWidthProportion, $desiredHeightProportion, $thumbWidth, $thumbHeight, $thumbDir){
		try{

			//first, crop it square by the dimensions of the shortest edge, with equal amounts of the longer edge chopped off.
			$imagick = new Imagick($dir.$filename);
			$width = $imagick->getimagewidth();
			$height = $imagick->getimageheight();
			$proportion = $height / $width;
			if($proportion < ($desiredHeightProportion / $desiredWidthProportion)){
				//landscape
				$desired_width = $desiredWidthProportion * $height / $desiredHeightProportion;
				$crop_each_side_by = ($width - $desired_width) / 2;
				$top_left_corner_x = ($width - $crop_each_side_by - $desired_width);
				$top_left_corner_y = 0;
				if(!$imagick->cropimage($desired_width, $height, $top_left_corner_x, $top_left_corner_y)){
					//apparently cropimage can return false sometimes, even though the image is cropped successfully.
				}
			}elseif($proportion > ($desiredHeightProportion / $desiredWidthProportion)){
				//portrait
				$desired_height = $desiredHeightProportion * $width / $desiredWidthProportion;
				$crop_top_bottom_by = ($height - $desired_height) / 2;
				$top_left_corner_x = 0;
				$top_left_corner_y = ($height - $crop_top_bottom_by - $desired_height);
				if($imagick->cropimage($width, $desired_height, $top_left_corner_x, $top_left_corner_y)){
					//apparently cropimage can return false sometimes, even though the image is cropped successfully.
				}
			}else{
				//it is the proper proportions already
			}

			//resize it
			if(!$imagick->scaleimage($thumbWidth, $thumbHeight)){
				logDebug('FAILURE resizing thumbnail['.$thumbWidth.'x'.$thumbHeight.']: '.$filename);
			}

			//save it
			if(!$imagick->writeimage($thumbDir.$filename)){
				logDebug('FAILURE writing thumbnail: '.$thumbDir.$filename);
			}
			
			$this->imagickClearDestroy($imagick);

		}catch(Exception $e){
			logDebug('EXCEPTION creating thumbnail: '.var_export($e, true));
		}
	}
	
	/**
	 * returns all the pictures in alphabetical order
	 * @return array all pictures in alphabetical order
	 */
	public function getImagesAlphabetical(){
		return $this->imageGateway->getImagesAlphabetical();
	}

	/**
	 * returns all the pictures in chronological order
	 * @return array all pictures in chronological order
	 */
	public function getImagesChronological(){
		return $this->imageGateway->getImagesChronological();
	}

	/**
	 * returns all the pictures in the order the were uploaded (latest first)
	 * @return array all pictures (latest first)
	 */
	public function getImagesLastUploaded(){
		return $this->imageGateway->getImagesLastUploaded();
	}

	/**
	 * returns all the pictures after shuffling them
	 * @return array all pictures in random order
	 */
	public function getImagesShuffled(){
		return $this->imageGateway->getImagesShuffled();
	}

	/**
	 * returns all the best image filenames in a random array
	 * @return array featured image filenames
	 */
	public function getBestPics(){
		return $this->imageGateway->getBestImages();
	}

	/**
	 * returns all the featured image filenames in a random array
	 * @return array featured image filenames
	 */
	public function getFeaturedPics(){
		return $this->imageGateway->getFeatureImages();
	}

	/**
	 * returns all the featured image filenames in a random array
	 * @return array featured image filenames
	 */
	public function getRandomFeaturedPic(){
		return $this->imageGateway->getRandomFeatureImage();
	}

	/**
	 * returns a random picture record for the home-page
	 * @return array includes filename, caption, etc
	 */
	public function getHomePic(){
		return $this->imageGateway->getHomeImage();
	}

	public function resizeAndWriteAndDestroyImagick($imagick, $filename){
		//resize image to a large display size, maybe 1900x1200?
		$imagick->resizeimage(1900, 1200, Imagick::FILTER_CATROM, 1, true);
		$imagick->writeImage(self::PICS_PATH.$filename);
		
		//now, create thumbnail size
		$imagick->resizeimage(300, 300, Imagick::FILTER_CATROM, 1, true);
		$imagick->writeImage(self::THUMBS_PATH.$filename);
		
		$this->imagickClearDestroy($imagick);
	}
	
	/**
	 * given an image record id, will create an Imagick object of the original file, 
	 * rotate it clockwise, save it, and then resize and save a large-image-display and a thumbnail image.
	 * @param integer $image_id
	 */
	public function rotateImageClockwise($image_id){
		$image_record = $this->getImageById($image_id);
		$originalFile = self::ORIG_PICS_PATH.$image_record['filename'];
		$imagick = new Imagick();
		$imagick->readImage($originalFile);
		$imagick->rotateImage(new ImagickPixel('none'), 90);
		$imagick->writeImage($originalFile);
		logDebug('rotated orig-img: '.$originalFile);
		$this->resizeAndWriteAndDestroyImagick($imagick, $image_record['filename']);
	}
	
	/**
	 * given an image record id, will create an Imagick object of the original file, 
	 * rotate it counter-clockwise, save it, and then resize and save a large-image-display and a thumbnail image.
	 * @param integer $image_id
	 */
	public function rotateImageCounterclockwise($image_id){
		$image_record = $this->getImageById($image_id);
		$originalFile = self::ORIG_PICS_PATH.$image_record['filename'];
		$imagick = new Imagick();
		$imagick->readImage($originalFile);
		$imagick->rotateImage(new ImagickPixel('none'), -90);
		$imagick->writeImage($originalFile);
		logDebug('rotated orig-img: '.$originalFile);
		$this->resizeAndWriteAndDestroyImagick($imagick, $image_record['filename']);
	}
	
	public function __toString() {
		try{
			$array = array(
			
			);
			return $array;
		}catch(Exception $e){
			return $e;
		}
	}
	
	const ORIG_PICS_URL		= '/images/orig/';		//uploaded originals are saved here, these should not be edited
	const PICS_URL			= '/images/pics/';		//normal (large) pic size to display when someone clicks on a pic, 1900 x 1200?
	const HOME_PICS_URL		= '/images/home/';		//home page pics -- ratio: 4 x 3, specifically: 800 x 600
	const THUMBS_URL		= '/images/thumbs/';	//upload-page pics -- ratio: 1 x 1, specifically: 300 x 300
	const BEST_PICS_URL		= '/images/best/';		//best-of-show page pics -- ratio: 16 x 9, specifically: 608 x 360
	const FEAT_PICS_URL		= '/images/feat/';		//feature page pics -- ratio: 3.3 x 1 (stretches across entire screen so the height will vary)
	const VIDS_URL			= '/images/vids/';		//videos
	const VIDS_THUMBS_URL	= '/images/vid_thumbs/';//video stills

	const WWW_PATH			= REAL_PATH.'public_html';

	const ORIG_PICS_PATH	= self::WWW_PATH.self::ORIG_PICS_URL;	//uploaded originals are saved here, these should not be edited
	const PICS_PATH			= self::WWW_PATH.self::PICS_URL;		//normal (large) pic size to display when someone clicks on a pic, 1900 x 1200?
	const HOME_PICS_PATH	= self::WWW_PATH.self::HOME_PICS_URL;	//home page pics -- ratio: 4 x 3, specifically: 800 x 600
	const THUMBS_PATH		= self::WWW_PATH.self::THUMBS_URL;		//upload-page pics -- ratio: 1 x 1, specifically: 300 x 300
	const BEST_PICS_PATH	= self::WWW_PATH.self::BEST_PICS_URL;	//best-of-show page pics -- ratio: 16 x 9, specifically: 608 x 360
	const FEAT_PICS_PATH	= self::WWW_PATH.self::FEAT_PICS_URL;	//feature pics (on best-of-show page) -- ratio: 3.3 x 1 (stretches across entire screen so the height will vary)
	const VIDS_PATH			= self::WWW_PATH.self::VIDS_URL;		//videos
	const VIDS_THUMBS_PATH	= self::WWW_PATH.self::VIDS_THUMBS_URL;	//video stills

	const PICS_WIDTH = 1900;
	const PICS_HEIGHT = 1200;
	
	const HOME_WIDTH = 800;
	const HOME_HEIGHT = 600;
	
	const THUMB_WIDTH = 300;
	const THUMB_HEIGHT = 300;

	const BEST_WIDTH = 608;
	const BEST_HEIGHT = 360;

	private $imageGateway = false;

	/* TODO: gotta think about heights, perhaps make them all the same (300), less resizing (by exactly 2), or just do 300px (see below) */
	
	//height of pics on fileupload page: 260px	-- maybe no big deal smaller - just download the 300h image.
	//height of pics on best page: 300px
	//height of pics on pictures page: 300px
	
}