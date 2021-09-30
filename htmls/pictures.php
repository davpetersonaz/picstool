<?php 
$pictures = new Pictures;
if(isset($_GET['chrono'])){
	$pics = $pictures->getImagesChronological();
}elseif(isset($_GET['alpha'])){
	$pics = $pictures->getImagesAlphabetical();
}else{
	$pics = $pictures->getImagesShuffled();
}
//logDebug('pics: '.var_export($pics, true));
logDebug('number of pics: '.count($pics));

//WOULD BE INTERESTING TO TRY THIS OUT??
// https://www.w3schools.com/howto/howto_js_image_grid.asp

//USING ARRAY-CHUNK: https://stackoverflow.com/questions/43573004/how-to-display-multiple-uploaded-images-in-row

//SIDE BY SIDE IMAGES: https://www.w3schools.com/howto/howto_css_images_side_by_side.asp

//https://owlcation.com/stem/how-to-align-images-side-by-side
//Obviously, sometimes you'll have images of all different dimensions, in which case you can't use width. The imperfect solution I've found is to change width to height and then specify height with a fixed number of ems. Like so:
//<img src="imageLocation" style="float: left; height: 15em; margin-right: 1%; margin-bottom: 0.5em;">
//Repeat that for each image in the gallery, then, as usual, end the gallery with <p style="clear: both;"> to turn off side-by-side tiling.
//Ems are proportional to the vertical size of the page, so they'll grow and shrink with screen size. If all your images are the same number of ems tall, they'll be the same height relative to each other.
//Unfortunately, I've had trouble making this work with captions.
?>

<div class='pictures_page'>
	<div class="row">
		<div class="col-sm-12 sort-pics text-center">
			<a href='/pictures.php?chrono' class='btn'>sort chronologically</a>
			<a href='/pictures.php?alpha' class='btn'>sort alphabetically</a>
			<a href='/pictures.php?shuffle' class='btn'>shuffle the order</a>
		</div>
	</div>

	<div class='pictures row'>
<?php foreach($pics as $image){ ?>
		<div class='pic-pad col-xs-12 col-sm-6 col-md-4 col-lg-3'>
			<?php include 'includes/image_display.php'; ?>
		</div>
<?php } ?>
	</div>
</div>
