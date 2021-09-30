<?php 
//logDebug('home');
$pictures = $pictureFactory->newInstance();
//logDebug('Pictures: '.var_export($pictures, true));
$pic = $pictures->getHomePic();
?>

<div class="home">
	<div class='row homehead'>
		<div class='col-xs-12 text-center full-width'>
			<?=$config->getHomepageHeader()?>
		</div>
	</div>

	<div class='row mainpic'>
		<div class='col-xs-12 full-width text-center home-image-box'>
			<img src='<?=Pictures::ORIG_PICS_URL.$pic['filename']?>' class='fancy-image-border home-image img-responsive' alt='<?=$pic['caption']?>' title='<?=$pic['caption']?>'>
		</div>
	</div>

	<div class='row description'>
		<div class='col-xs-12 offset-sm-1 col-sm-10'>
			<center><?=$config->getHomepageDescription()?></center>
		</div>
	</div>
</div>