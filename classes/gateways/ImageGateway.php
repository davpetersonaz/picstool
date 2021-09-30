<?php
class ImageGateway extends AbstractGateway{

	public function addNewImage($filename, $original_filename, $filemtime){
		$datetime = (new DateTime())->format('Y-m-d h:i:s');
		$values = array('filename'=>$filename, 'original_filename'=>$original_filename, 'date_added'=>$datetime, 'image_date'=>$filemtime, 'caption'=>$original_filename, 'description'=>$original_filename, 'pic_or_vid'=>0, 'site_number'=>SITE_NUMBER);
		$lastInsertId = $this->db->insert('images', $values, 'addNewImage');
		return $lastInsertId;
	}
	
	public function addNewVideo($filename, $original_filename, $filemtime){
		$datetime = (new DateTime())->format('Y-m-d h:i:s');
		$values = array('filename'=>$filename, 'original_filename'=>$original_filename, 'date_added'=>$datetime, 'image_date'=>$filemtime, 'caption'=>$original_filename, 'description'=>$original_filename, 'pic_or_vid'=>1, 'site_number'=>SITE_NUMBER);
		$lastInsertId = $this->db->insert('images', $values, 'addNewVideo');
		return $lastInsertId;
	}
	
	public function doesNameAlreadyExist($originalFilename){
		$result = $this->getImageByOriginalName($originalFilename);
		return ($result ? true : false);
	}
	
	public function deleteImage($image_id){
		$values = array('image_id'=>$image_id);
		$rowsAffected = $this->db->delete('images', $values, 'image_id=:image_id');
		return $rowsAffected;
	}
	
	public function getAllImages($orderby='image_date DESC'){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid, best, featured, home
					FROM images
					WHERE site_number = :site_number
					ORDER BY {$orderby}";
		$values = array('site_number'=>SITE_NUMBER);
//		$this->db->logQueryAndValues($query, $values, 'getAllImages');
		$rows = $this->db->select($query, $values);
		return $rows;
	}
	
	public function getBestImages(){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid
					FROM images
					WHERE site_number = :site_number
						AND best = 1
					ORDER BY RAND()";
		$values = array('site_number'=>SITE_NUMBER);
//		$this->db->logQueryAndValues($query, $values, 'getBestImages');
		$rows = $this->db->select($query, $values);
		return $rows;
	}
	
	public function getFeatureImages(){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid
					FROM images
					WHERE site_number = :site_number
						AND featured = 1
					ORDER BY RAND()";
		$values = array('site_number'=>SITE_NUMBER);
//		$this->db->logQueryAndValues($query, $values, 'getFeatureImages');
		$rows = $this->db->select($query, $values);
		return $rows;
	}
	
	public function getRandomFeatureImage(){
		$return = false;
		$featuredImages = $this->getFeatureImages();
		if(count($featuredImages) > 0){
			$return = $featuredImages[0];
		}
		return $return;
	}
	
	public function getHomeImage(){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid
					FROM images
					WHERE site_number = :site_number
						AND home = 1
					ORDER BY RAND()
					LIMIT 1";
		$values = array('site_number'=>SITE_NUMBER);
//		$this->db->logQueryAndValues($query, $values, 'getHomeImage');
		$rows = $this->db->select($query, $values);
		return (isset($rows[0]) ? $rows[0] : false);
	}
	
	public function getImageById($imageId){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid
					FROM images
					WHERE image_id = :image_id";
		$values = array('image_id'=>$imageId);
		$rows = $this->db->select($query, $values);
		return (isset($rows[0]) ? $rows[0] : false);
	}
	
	public function getImageByOriginalName($originalFilename){
		$query = "SELECT image_id, filename, original_filename, date_added, image_date, caption, description, keywords, pic_or_vid
					FROM images
					WHERE original_filename = :original_filename";
		$values = array('original_filename'=>$originalFilename);
//		$this->logQueryAndValues($query, $values, 'getImageByOriginalName');
		$rows = $this->db->select($query, $values);
		return (isset($rows[0]) ? $rows[0] : false);
	}
	
	public function getImagesAlphabetical(){
		return $this->getAllImages('filename ASC');
	}

	public function getImagesChronological(){
		return $this->getAllImages('image_date ASC');
	}

	public function getImagesLastUploaded(){
		return $this->getAllImages('date_added DESC');
	}
	
	public function getImagesShuffled(){
		$randomFilter = array('image_id', 'filename', 'original_filename', 'date_added', 'image_date', 'caption', 'description', 'keywords');
		$images = $this->getAllImages($randomFilter[array_rand($randomFilter)].' ASC');
		shuffle($images);
		return $images;
	}
	
	public function updateBestpage($image_id, $on=1){
		$on = ($on ? 1 : 0);
		$rowsAffected = $this->db->update('images', array('best'=>$on), "image_id=".intval($image_id));
		return $rowsAffected;
	}
	
	public function updateCaption($id, $text){
		$rowsAffected = $this->db->update('images', array('caption'=>$text), "image_id=".intval($id), 'updateCaption');
		return $rowsAffected;
	}
	
	public function updateFeaturepage($image_id, $on=1){
		$on = ($on ? 1 : 0);
		$rowsAffected = $this->db->update('images', array('featured'=>$on), "image_id=".intval($image_id), 'addToFeaturepage');
		return $rowsAffected;
	}
	
	public function updateHomepage($image_id, $on=1){
		$on = ($on ? 1 : 0);
		$rowsAffected = $this->db->update('images', array('home'=>$on), "image_id=".intval($image_id), 'addToHomepage');
		return $rowsAffected;
	}
	
}