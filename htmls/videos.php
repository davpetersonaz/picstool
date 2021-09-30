<?php /*
	<!-- PROBLEM: slow loading -->
	<!-- SOLUTION: show thumbnail, when clicked then play video -->
	<!-- assume you'd just use javascript to replace a thumbnail image with a video element -->
	<!-- MAYBE THIS: -->
	<!-- https://stackoverflow.com/questions/8492239/click-on-image-splashscreen-to-play-embedded-youtube-movie -->

*/ ?>

	<div class='videos row'>

<?php $videos = new Videos; ?>
<?php foreach($videos->getVideos() as $video){ ?>

		<div class='vid-pad col-xs-12 col-sm-6 col-md-4 col-lg-3'>
			<div class='vid-border'>
				<div class='col-xs-12 video'>
					<video controls>
						<source src="/images/vids/<?=$video?>" type="video/mp4">
					</video>
					<div class='col-xs-12 caption'>
						<p><?=$video?></p>
					</div>
				</div>
			</div>
		</div>

<?php } ?>

	</div>
