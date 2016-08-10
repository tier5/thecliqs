<?
include("html.php");
require_once("echo_setup.php");
//require_once($inc_path."surveys.php");
//include($full_path."setup.php");


if (form_isset("delete_id")) {
	$delete_id = form_int("delete_id");
    $db1->doSql("DELETE FROM surveys WHERE survey_id = $delete_id");
	$db1->doSql("DELETE FROM survey_options WHERE survey_id = $delete_id");
	$db1->doSql("DELETE FROM survey_votes WHERE survey_id = $delete_id");
}

$survey_id = form_int("survey_id");
if ($survey_id) {
    $row = $db1->getRow("SELECT * FROM surveys WHERE survey_id = $survey_id");
	$options = $db1->getAllRows("SELECT * FROM survey_options WHERE survey_id = $survey_id");
}

$surveys = $db1->getAllRows("SELECT * FROM surveys ORDER BY date_added DESC");

if (form_isset("submitted")){

    $row = array();
    $error = array();
    
	$row['name'] = form_text("name");
	$row['options'] = form_digits("options");
	$row['active'] = form_text("active");
	
	$row['active'] == "on" ? $row['active'] = 'Yes' : $row['active'] = 'No';
	
	if (!$row['name'] || $row['name'] == '') {
	    $error['name'] = "This field is required.";
	}
	
	if (!form_isset("survey_id")) {
		if (!$row['options'] || $row['options'] == '') {
		    $error['options'] = "This field is required. You need to enter a number bigger than 0.";
		}
	}
	
	if ($survey_id) {
	    foreach ($_POST as $k => $v){
			if (is_integer(strpos($k, "optioni")) && $v != "") {
			    $row[$k] = $v;
			}
		}
	}

	if (!sizeof($error)) {
	    if (!$survey_id) {
	        $db1->insertFields("surveys", array("name", "options", "active", "date_added"), 
									 array($row['name'], $row['options'], $row['active'], date("U")));
			$survey_id = $db1->getIdentity();			
	    }else {
			$db1->updateFields("surveys", "survey_id", $survey_id, array("name", "active"), 
									 array($row['name'], $row['active']));
		}
		
		if (!sizeof($options)) {
			
		 	foreach ($row as $k => $v){
				if (is_integer(strpos($k, "optioni"))) {
			    	$db1->insertFields("survey_options", array("survey_id", "name"), array($survey_id, $db1->escapeSql($v)));
				}
			}
		}else {
			foreach ($row as $k => $v){
				if (is_integer(strpos($k, "optioni"))) {
					$option_id = substr($k, 7);
			    	$db1->doSql("UPDATE survey_options SET name = '" . $db1->escapeSql($v) . "' WHERE option_id = $option_id");
				}
			}
		}
		
		header("location: survey.php?survey_id=$survey_id");
									 
	}	
}	

if (sizeof($options)) {
    $opts = sizeof($options)-1;
}else {
	$opts = $row['options']-1;
}

admin();
$menu='pimp/';
secureheader();
siteheader();
?><body>
    
<h2 align="center"><? if ($survey_id) { ?>Edit<? }else{ ?>Create<? } ?> survey</h2>

<form method="post" action="survey.php">
  <div align="center">
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="survey_id" value="<?= $survey_id ?>" />
    
  <table cellpadding="0" cellspacing="0" width="90%">
    <tr>
      <td width="100">Survey: </td>
          <td><input type="text" name="name" size="40" value="<?= $row['name'] ?>" />
           <? if ($error['name']): ?><br /><span class="error"><?= $error['name'] ?></span><? endif; ?></td> 
      </tr>
    <? if (!$survey_id){ ?>
    <tr>
      <td>Option number: </td>
          <td><input type="text" name="options" size="10" value="<?= $row['options'] ?>" />
          <? if ($error['options']): ?><br /><span class="error"><?= $error['options'] ?></span><? endif; ?></td> 
      </tr>
    <? } ?> 
    <tr>
      <td>Active: </td>
          <td><input type="checkbox" name="active" <? if ($row['active'] == 'Yes') { echo 'checked'; } ?> /></td> 
      </tr>
    
    <? if ($survey_id) { ?>
    <? for ($i = 0; $i <= $opts; $i++){ ?>
    <tr>
      <td>Option <?= $i+1 ?>: </td>
          <td><input type="text" name="optioni<? if ($options[$i]['option_id']) { echo $options[$i]['option_id']; }else{ echo $i; }?>" value="<?= $options[$i]['name'] ?>" /></td> 
	          </tr>
    <? } ?>  
    <? } ?>
    
    <tr>
      <td>&nbsp;</td>
          <td><input type="submit" value="<? if ($survey_id) { ?>Edit<? }else{ ?>Create<? } ?>" /></td> 
      </tr>
  </table>
  </div>
</form>
	
		
	<div align="center">
	  <? if (sizeof($surveys)) { ?>	       
</div>
	<p align="center">&nbsp;</p>
	<h2 align="center">Surveys</h2>
	<div align="center">
	  <table cellpadding="0" cellspacing="0" width="90%">
	    <? foreach ($surveys as $survey): ?>    
	    <tr>
	      <td><?= $survey['name'] ?></td>
		   <td><a href="survey.php?survey_id=<?= $survey['survey_id'] ?>">edit</a> / <a href="survey.php?delete_id=<?= $survey['survey_id'] ?>">delete</a></td>
		   </tr>
	    <? endforeach; ?>
	    </table>
	  <? } ?>
</div> 
 <?
sitefooter();
?>
