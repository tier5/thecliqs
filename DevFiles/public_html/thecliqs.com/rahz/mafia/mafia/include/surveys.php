<?
	#surveys related functions
	
	function survey_module(){
	global $id;
	global $db1; 
		
		$survey_id = form_table($db1, "surveys", "survey_id", "survey_id");			
		if (!$survey_id) {
		    $survey_id = survey_getLast(); 
		}
	
		if (form_isset("survey_submitted")) {
		   $selected = survey_getSelectedAnswer();
	
		   if ($selected >= 0 && survey_canVote($survey_id, $id)) {
		       $answer_id = survey_getSelectedAnswersId($survey_id, $selected);
			   survey_updateResults($survey_id, $answer_id);
			   $db1->insertFields("survey_votes", array("survey_id", "pimp_id", "date"),
			   									 array($survey_id, $id, date("U")));
		   }
		}
		
		if ($survey_id) {	
		if (survey_canVote($survey_id, $id)): 
			survey_display($survey_id);
	    else:
	   		survey_displayResults($survey_id);
	    endif;		
		    
		}else {
			echo '<p>No survey found.</p><p>&nbsp;</p>';
		}
	}
	
	function survey_getAnswers($survey_id){
	global $db1;	
	
		return $db1->getAllRows("SELECT * 
								  FROM survey_options
							     WHERE survey_id = $survey_id");
		
	}
	
	function survey_getSurvey($survey_id){
	global $db1;
		
		$survey = $db1->getRow("SELECT * 
							     FROM surveys 
							    WHERE survey_id = $survey_id");
							  
		$survey['options'] = survey_getAnswers($survey_id);
		
		return $survey;
	}
	
	function survey_getLast(){
	global $db1;	
	
		$row = $db1->getRow("SELECT survey_id
							   FROM surveys
							  WHERE active = 'Yes'
						   ORDER BY date_added DESC
						     LIMIT 1");
							 
		if (sizeof($row)) {
		    return $row['survey_id'];
		}					 
		
		return false;
	}
	
	function survey_isActive($survey_id){
	global $db1;	
	
		$active = $db1->getField("surveys", "survey_id", $survey_id, "active");
		
		if ($active == 'Yes') {
		    return true;
		}
		return false;
	}
	
	function survey_display($survey_id){ 
	
		$survey = survey_getSurvey($survey_id); ?>
		<p><?= $survey['name'] ?></p>
		<script type="javascript">
			function survey_clear(selected, total){		
				for (i = 0; i < total; i++){
					if (i != selected){
						id = 'answer'+i;
						radio = document.getElementById(id);
						radio.checked = '';
					}
				}
				
				return true;	
			}
		</script>
		<form name="frmsurvey" method="post">
		<input type="hidden" name="survey_submitted" value="1" />
		<input type="hidden" name="survey_id" value="<?= $survey_id ?>" />
			
		<? foreach ($survey['options'] as $k => $option): ?>	
			<table width="141" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="96%" align="left" valign="top"><?= $option['name'] ?>:</td>
                <td width="4%" align="right" valign="bottom"><input type="radio" name="answer<?= $k ?>" id="answer<?= $k ?>" onclick="survey_clear('<?= $k ?>', '<?= sizeof($survey['options']) ?>');" /></td>
              </tr>
          </table>
			 
			<br />				
		<? endforeach; ?>	
			
		<br /><input type="submit" value="Submit" />
		</form>	
		
<?	}

	function survey_displayResults($survey_id){
	global $db1;
		
		$ansrs = $db1->getAllRows("SELECT option_id,
										 answers,
										 name
								    FROM survey_options
								   WHERE survey_id = $survey_id");	
								   
		$total = 0;
		foreach ($ansrs as $answer):
			$total += $answer['answers'];
		endforeach;	
		
		for ($i = 0; $i <= sizeof($ansrs)-1; $i++):
			if ($total != 0):
			    $ansrs[$i]['percent'] = round(($ansrs[$i]['answers'] / $total)*100, 2);		
			else:
				$ansrs[$i]['percent'] = 0;
			endif;
		endfor;					   		
		
		$question = $db1->getField("surveys", "survey_id", $survey_id, "name"); ?>
		
		<p><?= $question ?></p>
		
<?		foreach ($ansrs as $answer): ?>
			
			<table width="141" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="66%" align="left" valign="top"><?= $answer['name'] ?>: </td>
                <td width="34%" align="right" valign="bottom">( <?= commas($answer['answers'])?> )<br />
                <?= $answer['percent'] ?>%</td>
              </tr>
</table>
			<br />
			<?	endforeach;	?>
            <?	}
			
	function survey_canVote($survey_id, $pimp_id){
	global $db1;
		$row = $db1->getRow("SELECT * FROM survey_votes WHERE survey_id = $survey_id AND pimp_id = $pimp_id");
		
		if ($row['id'] > 0) {
		    return false;
		}
		
		return true;
	}
	
	function survey_updateResults($survey_id, $answer_id){
	global $db1;
		
		$votes = $db1->getField("survey_options", 'option_id', $answer_id, "answers");
		$votes++;		
		$db1->doSql("UPDATE survey_options 
					   SET answers = $votes 
					 WHERE option_id = $answer_id");
						
		return true;			 	
	}
	
	function survey_getSelectedAnswer(){
		
		foreach ($_POST as $k => $v){
			if (is_integer(strpos($k, "answer"))  && $v == 'on') {
			   return substr($k, 6, strlen($k));
			}
		}	
		
		return false;	
	}
	
	function survey_getSelectedAnswersId($survey_id, $order){
		
		$answers = survey_getAnswers($survey_id);
		return $answers[$order]['option_id'];		
	}	
?>
