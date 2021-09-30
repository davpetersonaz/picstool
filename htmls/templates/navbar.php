<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
	<a class="navbar-brand" href="/home.php"><?=$config->getSiteTitle()?></a>
	<div class="collapse navbar-collapse" id="navbarCollapse">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/bestof.php">Best in Show</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/videos.php">Videos</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/pictures.php">Pictures</a>
			</li>
		</ul>

<?php if($alreadyLoggedIn){ ?>
		<a href="/config" class="btn">Config</a>
		<a href="/uploadfiles" class="btn">Admin</a>
<?php } ?>
					
		<form class="form-inline search-form" action='search.php' method='POST'>
			<input class="form-control search-form-input" type="text" placeholder="Search" aria-label="Search" name='search_for'>
			<button class="btn" type="submit">Search</button>
		</form>
	</div>
</nav>
