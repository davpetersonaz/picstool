<?php
if(isset($_POST['search_for'])){
	$pictures = new Pictures;
	$allpics = $pictures->getImagesShuffled();
	$searchResults = array();
	$searchString = '/^.*'.$_POST['search_for'].'.*$/';
	foreach($allpics as $pic){
		if(preg_match($searchString, $pic) === 1){
			$searchResults[] = $pic;
		}
	}
	
	if(empty($searchResults)){
		?>

			<div class='row'>
				<div class='col-xs-12 text-center full-width'>
					<h4>Sorry, no matches for that search criteria, try using less characters, the search only matches on the filenames.</h4>
				</div>
			</div>
			
		<?php
	}
	
	else{
		?>
		
			<div class='row'>
				<div class='col-xs-12 text-center full-width'>
					<h4>Search matches among the picture filenames:</h4>
				</div>
			</div>

			<div class='pictures row'>

			<?php foreach($searchResults as $pic): ?>

				<div class='pic-pad col-xs-12 col-sm-6 col-md-4 col-lg-3'>
					<div class='pic-border'>
						<a href='<?=Pictures::PICS_URL.$pic?>' target="_blank">
							<img class='img-responsive' src='<?=Pictures::PIC_THUMBS_URL.$pic?>'>
							<div class='caption'><p><?=$pic?></p></div>
						</a>
					</div>
				</div>

			<?php endforeach; ?>

			</div>
		
		<?php
	}
}