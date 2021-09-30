$(document).ready(function(){
	$('.caption_change').on('click', function(){
		console.warn('caption_change');
		var full_id = $(this).attr('id');
		console.warn('full_id', full_id);
		var id = full_id.substring(8);
		console.warn('id', id);
		$('#caption_'+id).hide();
		$('#caption_change_text_'+id).show();
		$('#caption_change_text_'+id).focus();
		$('#caption_changed_'+id).show();
	});
	
	$('.caption_changed').on('click', function(){
		console.warn('caption_changed');
		var full_id = $(this).attr('id');
		console.warn('full_id', full_id);
		var id = full_id.substring(16);
		console.warn('id', id);
		var caption = $('#caption_change_text_'+id).val();
		console.warn('caption', caption);
		$.ajax({
			method: 'POST',
			url: '/ajax/uploadImages.php',
			data: { caption: id, text: caption }
		}).done(function(data){
			$('#caption_change_text_'+id).hide();
			$('#caption_changed_'+id).hide();
			$('#caption_text_'+id).text(caption);
			$('#caption_'+id).show();
		});
	});
	
	$('.additions').on('click', function(){
		var full_id = $(this).attr('id');//add_home_918
		var id = full_id.substring(9);
		var page = full_id.substring(4, 8);
		var val = $(this).val();
		var on = (this.checked ? 1 : 0);
		console.warn('full_id', full_id, 'id', id, 'page', page, 'val', val, 'on', on);
		if(val === 'add_home'){
			if(on === 1){
				var home_text = 'Home Images appear are the large splash pictures on the "Home" page. '+
						'These images should be approximately square, or landscaped, we recommend large images (800x600 plus) cropped to 4 x 3 ratio.';
				if(!confirm(home_text)){ return; }
			}
			executeCheckboxAction(id, page, on);
		}else 
		if(val === 'add_feat'){
			if(on === 1){
				var featured_text = 'Featured Images appear on the "Best in Show" page, stretched across the entire screen at intervals. '+
						'Therefore these images must be extremely wide and short, we recommend large images cropped to 3.3 x 1 ratio (or greater).';
				if($('#add_best_'+id).checked === 1){
					//this should not happen since i've disabled the checkbox in the other's input
					featured_text += ' This image will no longer be select for "Best in Show".';
				}
				if(!confirm(featured_text)){ return; }
				executeCheckboxAction(id, 'best', 0);
				console.warn('disabling add_best_'+id);
				$('#add_best_'+id).prop('disabled', true);
			}else{
				console.warn('enabling add_best_'+id);
				$('#add_best_'+id).prop('disabled', false);
			}
			executeCheckboxAction(id, page, on);
		}else
		if(val === 'add_best'){
			if(on === 1){
				var best_text = 'Best of Show Images are the normal-sized pictures that appear on the "Best in Show" page. '+
						'These images can be of any size.';
				if($('#add_feat_'+id).checked === 1){
					//this should not happen since i've disabled the checkbox in the other's input
					best_text += ' This image will no longer be select as featured in "Best in Show".';
				}
				if(!confirm(best_text)){ return; }
				executeCheckboxAction(id, 'feat', 0);
				console.warn('disabling add_feat_'+id);
				$('#add_feat_'+id).prop('disabled', true);
			}else{
				console.warn('enabling add_feat_'+id);
				$('#add_feat_'+id).prop('disabled', false);
			}
			executeCheckboxAction(id, page, on);
		}
	});
	
	function executeCheckboxAction(id, page, on){
		$.ajax({
			method: 'POST',
			url: '/ajax/uploadImages.php',
			data: { id: id, page: page, on: on }
		}).done(function(data){
//			alert('additions completed', data);
		});
	}
	
	$('.delete').on('click', function(){
		if(confirm('Are you sure you wish to delete this image?')){
			var full_id = $(this).attr('id');
			var id = full_id.substring(7);
//			console.warn('pic id', id);
			$.ajax({
				method: 'POST',
				url: '/ajax/uploadImages.php',
				data: { delete: id }
			}).done(function(data){
				if(data === 'done'){
					console.warn('removing div');
					$('#pic_'+id).parent().remove();
				}
			});
		}
	});

	$('.rotate-left').on('click', function(){
		var full_id = $(this).attr('id');
		var id = full_id.substring(12);
		$.ajax({
			method: 'POST',
			url: '/ajax/uploadImages.php',
			data: { rotate_left: id }
		}).done(function(data){
			window.location.href = '/uploadfiles';
		});
	});

	$('.rotate-right').on('click', function(){
		var full_id = $(this).attr('id');
		var id = full_id.substring(13);
		$.ajax({
			method: 'POST',
			url: '/ajax/uploadImages.php',
			data: { rotate_right: id }
		}).done(function(data){
			window.location.href = '/uploadfiles';
		});
	});

	$('img[data-enlargeable]').addClass('img-enlargeable').click(function(){
		console.warn('enlargeable on click', this);
		var src = $(this).attr('src');
		console.warn('src', src);
		var filename = $(this).attr('data-filename');
		console.warn('filename', filename);
		var modal;
		function removeModal(){ 
			modal.remove(); 
			$('body').off('keyup.modal-close'); 
		}
		
		/* gotta redo this model, the css properties below should belong to the outer-div, and the img should be displayed inside it, with a border and max-width/heigth of 100% */
				
		modal = $('<div>').css({
			background: 'RGBA(0,0,0,.5) url('+filename+') no-repeat center',
			backgroundSize: 'contain',
			/* dont add border here -- it just adds the border around the perimeter of the viewport -- not around the image */
			cursor: 'zoom-out',
			position: 'fixed',
			top: '0', 
			left: '0',
			width: '100%', 
			height: '100%',
			zIndex: '10000'
		}).click(function(){
			removeModal();
		}).appendTo('body');
		//handling ESC
		$('body').on('keyup.modal-close', function(e){
			if(e.key === 'Escape'){ 
				removeModal();
			} 
		});
	});
});