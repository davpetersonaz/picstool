<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- declare CSS first -->
		<link href="/css/bootstrap4/bootstrap.css" rel="stylesheet" type="text/css">
		<?php /* <link href="/css/core_style.css?v<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/css/core_style.css')?>" rel="stylesheet" type="text/css"> */ ?>
		<link href="/css/style.css?v=<?=time()?>" rel="stylesheet" type="text/css">

		<!-- TODO: Place at the end of the document so the pages load faster (not sure if they work if at the end? got junit errors til i moved them back here...) -->
		<script src="/js/jquery/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="/js/bootstrap4/bootstrap.js" type="text/javascript"></script>

<?php if($alreadyLoggedIn && ($page === 'uploadfiles' || $page === 'pictures')){ ?>
		<script src="/js/pictures.js?v=<?=time()?>" type="text/javascript"></script>
<?php } ?>

		<title><?=$config->getSiteTitle()?></title>
	</head>
	<body>
		<header>
<?php include('navbar.php'); ?>
		</header>
