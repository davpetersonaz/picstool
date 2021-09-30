<?php
class Func{
	
	public static function errorlog($msg=''){
		$request_uri = (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']) ? $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : 'unset');
		$stacktrace = self::generateCallTraceString();
		error_log("LOG ERROR [{$msg}], uri [{$request_uri}], stacktrace [{$stacktrace}]");
	}

	static function generateCallTraceString(){
		/*
			[Wed Sep 16 12:56:13] array (
			  0 => '#0 /var/www/dave.framework/classes/Genezal.php(1534): Genexal::generateC@llTraceString()',
			  1 => '#1 /var/www/dave.cranenetwork.com/includes/search/parts/makexTab.php(5): General::errorlog(\'sample error, s...\')',
			  2 => '#2 /var/www/dave.cranenetwork.com/includes/partxSearch.php(7): include(\'/var/www/dave.c...\')',
			  3 => '#3 /var/www/dave.cranenetwork.com/includes/xearch.php(17): include(\'/var/www/dave.c...\')',
			  4 => '#4 /var/www/dave.cranenetwork.com/xearch-parts.php(7): include(\'/var/www/dave.c...\')',
			  5 => '#5 {main}',
			)
		*/
		$e = new Exception();
		$trace = explode("\n", $e->getTraceAsString());
		$trace = array_reverse($trace);// reverse array to make steps line up chronologically
		array_shift($trace); // remove {main}
		array_pop($trace); // remove call to this method
		$length = count($trace);
		$result = array();
		for($i=0; $i<$length; $i++){
			// replace '#someNum' with '$i)', which corrects ordering
			$tempresult = ($i + 1).')'.substr($trace[$i], strpos($trace[$i], ' '));
			$substrlength = 1 + strpos($tempresult, '): ');
			$result[] = substr($tempresult, 0, $substrlength);
		}
		return implode(", ", $result);
	}
	
	static function strpos_all($haystack, $needle){
		$offset = 0;
		$allpos = array();
		while (($pos = strpos($haystack, $needle, $offset)) !== false){
			$offset   = $pos + 1;
			$allpos[] = $pos;
		}
		return $allpos;
	}
	
	//strip html and extra whitespace and odd characters, etc
	static function stripTagsPlus($input){
		$input = str_replace( '<', ' <', $input);//first, add a space before any html (or any greater-than sign), so we can replace html blocks with a space (instead of nothing)
		$input = strip_tags($input);//strip html
		$input = str_replace(array("\n", "\r"), ' ', $input);//replace newline chars with spaces
		$input = self::replaceUnicode($input);//see function below
		$input = htmlspecialchars($input, ENT_QUOTES);//htmlspecialchars_decode doesnt get rid of &nbsp;
		$input = html_entity_decode($input, ENT_QUOTES, 'ISO-8859-1');//replace encoded chars (such as &nbsp;)
		$input = preg_replace("/(?:\s|&nbsp;)+/", ' ', $input, -1);//replace multiple spaces with a single space
//		logDebug("stripTagsPlus returns: [{$input}]");
		return $input;
	}

	static function replaceUnicode($input){
		$find[] = 'â€œ';  // left side double smart quote
		$find[] = 'â€';  // right side double smart quote
		$find[] = 'â€˜';  // left side single smart quote
		$find[] = 'â€™';  // right side single smart quote
		$find[] = 'â€¦';  // elipsis
		$find[] = 'â€”';  // em dash
		$find[] = 'â€“';  // en dash
		$replace[] = '"';
		$replace[] = '"';
		$replace[] = "'";
		$replace[] = "'";
		$replace[] = "...";
		$replace[] = "-";
		$replace[] = "-";
		$output = str_replace($find, $replace, $input);
//		logDebug("replaceUnicode({$input}): result: ".var_export($output, true));
		return $output;
	}
	
	public static function verifySite($number, $domain){
		$hash = md5($number.$domain);
		$sites = array('100littlemanharley.com'=>'d08eb0506fa227f1b1bb33b228b2b8c9');
		return (in_array($hash, $sites));
	}
	
}