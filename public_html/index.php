<?php include('../router.php'); ?>
<?php include(HTMLS_PATH.'templates/header.php'); ?>

		<div class="container-fluid">
			<!--  CONTENT BELOW  -->

			<?php include(HTMLS_PATH.$page.'.php'); ?>

			<!--  CONTENT COMPLETE  -->
		</div><!-- //container-fluid -->

<?php include(HTMLS_PATH.'templates/footer.php'); ?>

	</body>
</html>

<script>
$(document).ready(function(){
	//in the background, create any thumbnails that do not exist
//	$.post('/ajax/verifyImages.php');
});
</script>
