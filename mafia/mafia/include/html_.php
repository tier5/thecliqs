<?

function html_optionsFromTable($db, $table, $valueField, $textField, $selectedValue, $orderField = "") {
	$sql = "select $valueField, $textField from $table";
	if ($orderField) {
		$sql .= " order by $orderField";
	}

	$rs = $db->createRecordset($sql);

	return html_optionsFromRecordset($rs,$valueField,$textField,$selectedValue);
}

function html_optionsFromRecordset($rs, $valueField, $textField, $selectedValue) {
	if (!is_array($selectedValue)) {
		$selectedValue = array($selectedValue);
	}

	$html = "";
	while ($row = mysql_fetch_array($rs)) {
		$html .= "<option ";
		if (in_array($row[$valueField],$selectedValue)) {
			$html .= "selected ";
		}
		$html .= "value=\"" . html_escapeHTML($row[$valueField]) . "\">" . html_escapeHTML($row[$textField]) . "</option>\n";
	}
	return $html;
}


function html_escapeHTML($string, $keep_amp = FALSE) {
	$escaped = htmlentities($string,ENT_QUOTES);
	$escaped = str_replace("(","&#40;",$escaped);
	$escaped = str_replace(")","&#41;",$escaped);
	$escaped = str_replace("€", "&euro;", $escaped);
	$escaped = str_replace("’", "'", $escaped);
	$escaped = str_replace("&reg;", "&#174;", $escaped);
	$escaped = str_replace("&eacute;", "&#233;", $escaped);
	$escaped = str_replace("&Eacute;", "&#201;", $escaped);
	$escaped = str_replace("&uuml;", "&#252;", $escaped);
	$escaped = str_replace("&Uuml;", "&#220;", $escaped);
	$escaped = str_replace(";", ";", $escaped);
	$escaped = str_replace(chr(147), "&#147;", $escaped);
	$escaped = str_replace(chr(148), "&#148;", $escaped);
	$escaped = str_replace(chr(151), "-", $escaped);
	$escaped = str_replace(chr(153), '&#153;', $escaped);
	$escaped = str_replace(chr(148), '"', $escaped);
	$escaped = str_replace(chr(132), '"', $escaped);
	$escaped = str_replace(chr(147), '"', $escaped);
	$escaped = str_replace(chr(150), "&#150;", $escaped);
	$escaped = str_replace(chr(133), "...", $escaped);

	# Not the best solution to be able to manage arabic / japan char codes
	if($keep_amp){
		$escaped = str_replace("&amp;", "&", $escaped);
	}
	
	return($escaped);
}
	


function html_redirect($location){
	header("Location: $location");
	die();
}

function html_escapeURL($string) {
	return(urlencode($string));
}

function html_removeImgPaths($html){
	//return preg_replace("/(src)=(\"|')(.*)(\/)(.*)(\")/", "$1=$2$5$6", $html);
	//return preg_replace("/(src)=(\"|')(.*)(\/)(.*?)(\")/", "$1=$2$5$6", $html);
	return preg_replace("/(src)=(\"|')([^\">]*)(\/)(.*?)(\")/", "$1=$2$5$6", $html);
}

function html_addImgPaths($html, $path){
	return preg_replace("/(src)=(\"|')(.*?)(\")/", "$1=$2" . $path . "/$3$4", $html);
//	return preg_replace("/(src)=(\"|')(.*)(\")/", "$1=$2" . $path . "/$3$4", $html);
}


function html_optionsFromArray($values, $selectedValue, $text = FALSE) {
	if (!is_array($selectedValue)) {
		$selectedValue = array($selectedValue);
	}
	$html = "";
	for ($i=0; $i<sizeof($values); $i++) {
		$html .=  "<option ";
		if (in_array($values[$i],$selectedValue)) {
			$html .= "selected ";
		}
		if ($text && is_array($text) && isset($text[$i])) {
			$display = $text[$i];
		} else {
			$display = $values[$i];
		} 
		$html .= "value=\"" . htmlentities($values[$i]). "\">" . htmlentities($display) . "</option>\n";
	}

	return $html;
}

function html_RemoveParamFromUrlQuery($query, $param){
   	$q = split("&", $query);
	
	$result = array();
	foreach($q as $p){
		if(strpos($p, $param) !== 0){
			$result[] = $p;
		}
	}
   	$result = join("&", $result);
	
   	return $result;
} 

function html_AddParamToUrl($url, $param){
	if($param){
	   	$q = split("&", $url);
		if(sizeof($q) == 1 and (strpos($url, "?") == 0)){
			$result = $url."?".$param;
		}else{
			$q[]    = $param;
			$result = join("&", $q);
		}
	}else{
		$result = $url;
	}
	
	return $result;
}

function html_createHiddensFromQuery($query){
	$q = split("&", $query);
	$rez = "";
	foreach($q as $p){
		$param = split("=", $p);
		
		$param[1] = (!isset($param[1]))? "" : $param[1];
		$rez .= "<input type=\"hidden\" name=\"".
				html_escapeHTML($param[0])."\" id=\"".
				html_escapeHTML($param[0])."\" value=\"".
				html_escapeHTML($param[1])."\" /> ";
	}
	
	return $rez;
}

function html_removeAllParamsFromURL($url){
	$path = explode("?", $url);
	
	return $path[0];
}

function html_getPathFromURL($url){
	$urlx = parse_url($url);

	return $urlx["path"];
}

function html_escapeJS($string) {
		#$escaped = HTML_escapeHTML($string);
		$escaped = $string;
        $escaped = str_replace("'","\\'",$escaped);
        $escaped = str_replace("\"","\\\"",$escaped);
        $escaped = str_replace("\"","",$escaped);
		$escaped = str_replace("\n","\\n",$escaped);
		$escaped = str_replace("\r","\\r",$escaped);
		return($escaped);
} 


?>