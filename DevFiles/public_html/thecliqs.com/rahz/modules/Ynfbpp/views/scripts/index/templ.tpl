
<?php
$limit = $this->limit;
$fieldsShow = $this->fieldsShow;
// $fieldValues = $this->fieldValues;
$i = 0;
foreach ( $fieldsShow as $index => $show ) {
	if ($index == 5) {
		$gender = $show ['value'];
		if ($gender == 3) {
			$show ['value'] = $this->translate ( "Female" );
		} else {
			$show ['value'] = $this->translate ( "Male" );
		}
	}
	?>
<li>
                                    <?php echo $this->translate($show['label'] . ": ")?>
                                    <?php echo $this->translate($show['value'])?>
                                </li>

<?php
	$i ++;
	if ($i > 3) {
		break;
	}
}
?>