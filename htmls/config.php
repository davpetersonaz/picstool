<?php
//logDebug('page config "tostring": '.var_export($config, true));

//process ajax first
if(isset($_POST['config_form'])){
	logDebug('config POST: '.var_export($_POST, true));
	$homepage_header = "";
//	if(empty($_POST['hSelect1'])){ 
//		$homepage_header .= $_POST['homepage_header1'];
//	}else{
		$homepage_header .= "<{$_POST['hSelect1']}>{$_POST['homepage_header1']}</{$_POST['hSelect1']}>";
		if(!empty($_POST['hSelect2'])){ 
			$homepage_header .= "<{$_POST['hSelect2']}>{$_POST['homepage_header2']}</{$_POST['hSelect2']}>";
		}
		if(!empty($_POST['hSelect3'])){ 
			$homepage_header .= "<{$_POST['hSelect3']}>{$_POST['homepage_header3']}</{$_POST['hSelect3']}>";
		}
//	}
	$values = array('browser_title'=>$_POST['browser_title'], 'homepage_header'=>$homepage_header, 'homepage_description'=>$_POST['homepage_description']);
	$config->updateConfig($values);
	echo 'done';
	exit;
}

/*
	TODO: add ability to change colors of borders/background/font/etc on picture pages
*/

$header_array = $config->getHomepageHeaderArray();
//logDebug('homepage header: '.var_export($header_array, true));
$hSelectLevel1 = $header_array['hval'][0];
//logDebug("hSelectLevel1 [{$hSelectLevel1}]");
$hSelectLevel2 = $header_array['hval'][1];
//logDebug("hSelectLevel2 [{$hSelectLevel2}]");
$hSelectLevel3 = $header_array['hval'][2];
//logDebug("hSelectLevel3 [{$hSelectLevel3}]");

$hSelect1 = "<textarea id='homepage_header1' name='homepage_header1' class='select-by-textarea' rows='2' cols='80' placeholder='pre-title'>{$header_array['head'][0]}</textarea>";
$hSelect1 .= "<select id='hSelect1' name='hSelect1'>";
$hSelect1 .= "	<option value='h1'".($hSelectLevel1===1?' selected':'').">h1</option>
				<option value='h2'".($hSelectLevel1===2?' selected':'').">h2</option>
				<option value='h3'".($hSelectLevel1===3?' selected':'').">h3</option>
				<option value='h4'".($hSelectLevel1===4?' selected':'').">h4</option>
				<option value='h5'".($hSelectLevel1===5?' selected':'').">h5</option>
			</select><br />";

$hSelect2 = "<textarea id='homepage_header2' name='homepage_header2' class='select-by-textarea' rows='2' cols='80' placeholder='site-title'>{$header_array['head'][1]}</textarea>";
$hSelect2 .= "<select id='hSelect2' name='hSelect2'>";
if($hSelectLevel2 === false){
	$hSelect2 .= "<option value=''></option>";
}
$hSelect2 .= "	<option value='h1'".($hSelectLevel2===1?' selected':'').">h1</option>
				<option value='h2'".($hSelectLevel2===2?' selected':'').">h2</option>
				<option value='h3'".($hSelectLevel2===3?' selected':'').">h3</option>
				<option value='h4'".($hSelectLevel2===4?' selected':'').">h4</option>
				<option value='h5'".($hSelectLevel2===5?' selected':'').">h5</option>
			</select><br />";

$hSelect3 = "<textarea id='homepage_header3' name='homepage_header3' class='select-by-textarea' rows='2' cols='80' placeholder='sub-title'>{$header_array['head'][2]}</textarea>";
$hSelect3 .= "<select id='hSelect3' name='hSelect3'>";
if($hSelectLevel3 === false){
	$hSelect3 .= "<option value=></option>";
}
$hSelect3 .= "	<option value='h1'".($hSelectLevel3===1?' selected':'').">h1</option>
				<option value='h2'".($hSelectLevel3===2?' selected':'').">h2</option>
				<option value='h3'".($hSelectLevel3===3?' selected':'').">h3</option>
				<option value='h4'".($hSelectLevel3===4?' selected':'').">h4</option>
				<option value='h5'".($hSelectLevel3===5?' selected':'').">h5</option>
			</select>";
?>

<div id='config_page' class='text-center'>

	<form id='config' method="POST">
		<input type='hidden' name='config_form'>
		
		<div class='form-group'>
			<label for='browser_title'>Homepage Title</label><br />
			<input type='text' id='browser_title' name='browser_title' size="80" value='<?=$config->getSiteTitle()?>'>
		</div>

		<div class='form-group'>
			<label for='homepage_header'>Homepage Header</label><br />
			<?=$hSelect1?>
			<?=$hSelect2?>
			<?=$hSelect3?>
		</div>

		<div class='form-group'>
			<label for='homepage_description'>Homepage Description</label><br />
			<textarea id='homepage_description' name='homepage_description' rows='20' cols='120'><?=$config->getHomepageDescription()?></textarea>
		</div>
		
		<input type='submit' value='submit'>
	</form>

</div>
