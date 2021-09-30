<?php 
		/* TODO:  figure out a better way to display full-image ... maybe iframes? (nice border, still see background if image is narrow/wide) */ 
		/* https://stackoverflow.com/questions/18545077/image-fullscreen-on-click/50430187 */
		/* use <img data-enlargeable> to have the image go fullscreen when clicked. */
?>

		<div class="pic-border" id="pic_<?=$image['image_id']?>">
			<div class="img-span">
				<img data-enlargeable data-filename='<?=Pictures::PICS_URL?><?=$image['filename']?>' style="cursor:zoom-in" src="<?=Pictures::THUMBS_URL?><?=$image['filename']?>">
			</div>
			<hr />
			<div class="caption">
				<p>
					<div id='caption_text_<?=$image['image_id']?>' class='caption_text'><?=$image['caption']?></div>
<?php if($page === 'uploadfiles'){ ?>
					<button id='caption_<?=$image['image_id']?>' class='caption_change'>Change</button>
					<textarea id='caption_change_text_<?=$image['image_id']?>' class='caption_change_text'><?=$image['caption']?></textarea>
					<button id='caption_changed_<?=$image['image_id']?>' class='caption_changed'>Update</button>
<?php } ?>
				</p>
			</div>
<?php if($page === 'uploadfiles'){ ?>
			<hr />
			<div class="filename">
				<?=$image['filename']?>
				<div class='filename_date'><?=$image['image_date']?></div>
				<div class='filename_date_added'><?=$image['date_added']?></div>
			</div>
			<hr />
			<div class='add-on-options'>
				<label for="add_home_<?=$image['image_id']?>">
					<input type="checkbox" id="add_home_<?=$image['image_id']?>" class="additions" value="add_home" <?=($image['home']?'checked':'')?>> home
				</label> &nbsp; 
				<label for="add_best_<?=$image['image_id']?>">
					<input type="checkbox" id="add_best_<?=$image['image_id']?>" class="additions" value="add_best" <?=($image['best']?'checked':'')?> <?=($image['featured']?'disabled':'')?>> best
				</label> &nbsp; 
				<label for="add_feat_<?=$image['image_id']?>">
					<input type="checkbox" id="add_feat_<?=$image['image_id']?>" class="additions" value="add_feat" <?=($image['featured']?'checked':'')?> <?=($image['best']?'disabled':'')?>> feature
				</label> &nbsp; 
				<button id='delete_<?=$image['image_id']?>' class='delete'>Delete</button>
			</div>
	<?php /* rotate doesnt seem to have any effect
	
			i think i have to remove EXIF info to rotate, should probably be doing that anyway for location-protection
	
			<hr />
			<div class='action-buttons'>
				<button id='rotate_left_<?=$image['image_id']?>' class='rotate-left'><-Rotate</button>
				<button id='delete_<?=$image['image_id']?>' class='delete'>Delete</button>
				<button id='rotate_right_<?=$image['image_id']?>' class='rotate-right'>Rotate-></button>
			</div>
	*/ ?>
<?php } ?>
			
			<!-- this is where i will have the heart, and double-heart (double can only be clicked if the first was clicked more than a month previously) -->
			<!-- glyphicon glyphicon-heart and glyphicon glyphicon-heart-empty -->
			<!-- might as well include a download button -->
			<!-- maybe a "set as screensaver" button?  (naw) -->
			<!-- include a 'flag as inappropriate' for all those sjw's -->
			<!-- maybe there is something else i can do -->
			
		</div>
