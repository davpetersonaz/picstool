<?php
$pictures = new Pictures;
$featured_pics = $pictures->getFeaturedPics();
$best_pics = $pictures->getBestPics();
$featured_ctr = 0;
$best_ctr = 0;
?>

<?php while(isset($best_pics[$best_ctr])): ?>

	<?php if(isset($featured_pics[$featured_ctr])): ?>
		<div class='row'>
			<div class='col-xs-12 full-width'>
				<a href='<?=Pictures::PICS_URL.$featured_pics[$featured_ctr]['filename']?>'>
					<img src='<?=Pictures::PICS_URL.$featured_pics[$featured_ctr]['filename']?>' class='full-width fancy-image-border' title="<?=$featured_pics[$featured_ctr++]['caption']?>">
				</a>
			</div>
		</div>
	<?php endif; ?>

	<?php if(isset($best_pics[$best_ctr])): ?>
		<div class='pictures row'>
			
			<?php for($i=0;$i<8;$i++): ?>
				<?php if(isset($best_pics[$best_ctr])): ?>
			
					<div class='pic-pad col-xs-12 col-sm-6 col-md-3'>
						<div class='pic-border'>
							<a href='<?=Pictures::PICS_URL.$best_pics[$best_ctr]['filename']?>'>
								<img class='img-responsive' src='<?=Pictures::THUMBS_URL.$best_pics[$best_ctr]['filename']?>' title="<?=$best_pics[$best_ctr]['filename']?>">
								<div class='caption'><p><?=$best_pics[$best_ctr++]['caption']?></p></div>
							</a>
						</div>
					</div>
			
				<?php endif; ?>
			<?php endfor; ?>

		</div>
	<?php endif; ?>

<?php endwhile;