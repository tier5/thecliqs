<?

class Pagination{
	var $rows = array();
	var $rows_on_page = 10;
	var $add_params = FALSE;
	var $current_page = 1;
	var $max_page_scroll = 3;
	var $page_param = "page";
	
	var $rez_pages = 1;
	var $rez_rows_count = 0;
	var $rez_first_row  = 0;
	var $rez_last_row   = 0;
	
	var $rows_on_page_options = array(10, 20, 30, 50, 100);
	
	var $arr_vals = array();
		
	function Pagination(&$rows, $add_params = FALSE,  $rows_on_page = 10){
		$this->rows = &$rows;
		$this->rows_on_page = $rows_on_page;
		$this->add_params = $add_params;
		
		#Get page parameter from form
		$this->current_page = form_int($this->page_param, 1);
		
		if(function_exists("lang_getParam")){
			$this->max_page_scroll = lang_getParam("PAGINATION_MAX_PAGE_SCROLL", $this->max_page_scroll);
		}
	}
	
	function Process(){
		$this->Calculate();
		if(is_array($this->rows)){
			$this->rows = array_splice($this->rows, $this->rez_first_row, $this->rows_on_page);
		}else{
			if(mysql_num_rows($this->rows) > 0 && $this->rez_first_row <= mysql_num_rows($this->rows)){
				mysql_data_seek($this->rows, $this->rez_first_row);
			}
				$result = array();
				for($i=0; $i<$this->rows_on_page; $i++){
					if($row = mysql_fetch_array($this->rows)){
						$result[] = $row;
					}else{
						break;
					}
					//$row = mysql_fetch_array($this->rows);
					//$result[] = $row;
				}
				$this->rows = $result;
//			}else{
//				$this->rows = array();
//			}
		}
		
		#Create result object
		$rez["first_row"] = $this->rez_first_row+1;
		$rez["last_row"]  = $this->rez_last_row+1;
		$rez["total_rows"]= $this->rez_rows_count;
		if($this->rez_pages > 1){
			$pages = array();
			for($i=1; $i<=$this->rez_pages; $i++){
				if($i >= ($this->current_page-$this->max_page_scroll) && 
				   $i <= ($this->current_page+$this->max_page_scroll)){
					$p["no"]   = $i;
					$link      = $this->GetPageLink($i);
					$p["link"] = ($i != $this->current_page) ? $link : FALSE;
					$pages[] = $p;
				}
			}
			$rez["pages"] = $pages;
		}
		if($this->current_page > 1){
			$rez["first"] = $this->GetPageLink(1);
			$rez["prev"]  = $this->GetPageLink($this->current_page - 1);
		}
		if($this->current_page < $this->rez_pages){
			$rez["next"]  = $this->GetPageLink($this->current_page + 1);
			$rez["last"]  = $this->GetPageLink($this->rez_pages);
		}
		$this->arr_vals = $rez;
		return $rez;
	}

	function GetPageLink($page_no){
//		$request_uri = $_SERVER["REQUEST_URI"];
		$request_uri = $_SERVER["PHP_SELF"];
//		$request_uri = substr($request_uri, 0, strpos($request_uri, "?"));
		return $request_uri."?".$this->page_param."=".$page_no.$this->add_params;
	}
	
	function isLastRow($pos){
		if($this->rez_pages == $this->current_page && 
		   (($this->current_page-1) * $this->rows_on_page + $pos) == ($this->rez_rows_count -1)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	function isFirstRow($pos){
		if($this->current_page == 1 && $pos == 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	function Calculate(){
		if(is_array($this->rows)){
			$this->rez_rows_count = sizeof($this->rows);
		}else{
			$this->rez_rows_count = mysql_num_rows($this->rows);
		}
		$this->rez_pages      = ceil($this->rez_rows_count / $this->rows_on_page);
		$this->rez_first_row  = ($this->current_page-1) * $this->rows_on_page;
		$this->rez_last_row   = $this->rez_first_row + $this->rows_on_page;
		if($this->rez_last_row > $this->rez_rows_count){
			$this->rez_last_row = $this->rez_rows_count -1;
		}else{
			$this->rez_last_row--;
		}
	
		#Got to last page is current page is greater than max number of pages
		if($this->current_page > $this->rez_pages){
			$this->current_page = $this->rez_pages;
		}
	}
	
	function getHTML($show_rows_on_page = FALSE){
	   $p = $this->arr_vals;
       $html = '<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
            		<td nowrap><p class="showing">'.lang_t('Showing').' '.$p["first_row"].' - '.$p["last_row"].' '.lang_t('of').' '.$p["total_rows"].' &nbsp;</p></td>
            		<td align="right" class="rt"  nowrap><p class="showing">
            			';
		if($p["pages"]){
			if($p["first"]){
				$html .= ' <a href="'.$p["first"].'">&#139;&#139; '.lang_t('first').'</a> ';
				$html .= ' <a href="'.$p["prev"].'">&#139; '.lang_t('prev').'</a> ';
			}else{
				$html .= "&#139;&#139; ".lang_t('first')." &#139; ".lang_t('prev');
			}
			$pages = array();
			foreach($p["pages"] as $page){
				if($page["link"]){
					$pages[] = "<a href=\"".$page["link"]."\">".$page["no"]."</a>";
				}else{
					$pages[] = $page["no"];
				}
			}
			$html .= " ".join(" | ", $pages)." ";
			if($p["last"]){
				$html .= ' <a href="'.$p["next"].'">'.lang_t('next').' &#155;</a> ';
				$html .= ' <a href="'.$p["last"].'">'.lang_t('last').' &#155;&#155;</a> ';
			}else{
				$html .= lang_t('next')." &#155; ".lang_t('last')." &#155;&#155;";
			}
		}
		$html .= "</p></td><td width=\"100%\" style=\"text-align: right; margin: 0; padding:0;\">".(($show_rows_on_page) ? $this->getRowsOnPageHTML() : "&nbsp;")."</td></tr></table>";
		
		return $html;
	}
	
	function getRowsOnPageHTML(){
		$action = $_SERVER["PHP_SELF"];
		$rez = "<form action=\"$action\" method=\"get\" name=\"_recsonpage_frm\" id=\"_rowsonpage_frm\" style=\"margin:0; padding:0;\">
				<input type=\"hidden\" name=\"page\" id=\"page\"\" value=\"1\">
				".lang_t("View")." <select name=\"rows_on_page\" id=\"rows_on_page\" class=\"pages\" onchange=\"document.getElementById('_rowsonpage_frm').submit()\">";
		foreach($this->rows_on_page_options as $s){
			$selected = ($s == $this->rows_on_page) ? "selected" : "";
			$rez .= "<option value=\"$s\" $selected>$s</option>";
		}
		$rez .= "</select> ".lang_t("on page")."</form>";
		
		return $rez;
		
	}
	
	function setRowsOnPageOptions($val){
		if(is_array($val)){
			$this->rows_on_page_options = $val;
		}
	}
}

?>