<?php 
/**
 * SocialEngine
 *
 * @package    Yncontest
 * @author     YounetCo Company
 */
?>
<?php $results = Engine_Api::_()->yncontest()->getPlugins();?>

<div id="typeyncontest-wrapper" class="form-wrapper">
	<div id="typeyncontest-label" class="form-label">
		<label for="typeyncontest" class="required"><?php echo $this->translate('Please select type');?>
		</label>
	</div>
	<div id="typeyncontest-element" class="form-element">
		<select name="typeyncontest" id="typeyncontest" onchange="changeType(this);">
			<?php foreach($results as $key => $item):?>
			<option value="<?php echo $key?>" label="<?php echo $item?>">
				<?php echo $item?>
			</option>
			<?php endforeach;?>
		</select>
	</div>
</div>
<script type="text/javascript">
	function changeType(e) {
		var typeChoose = e.get('value');
		change(typeChoose);
		
	} 
	function change(typeChoose)
	{
		var types = new Array( 
		<?php foreach($results as $key => $item):?>
			<?php $temp .=  "'".$key."',"; ?>
		<?php endforeach;
			echo substr($temp, 0, strlen($temp)-1);
		?>
		);
		for (var i=0;i<types.length;i++)
		{ 
			if($('max'+types[i]+'-wrapper'))
			{
				$('max'+types[i]+'-wrapper').setStyle('display','none');			
			}			
			if($('height'+types[i]+'-wrapper'))
			{
				$('height'+types[i]+'-wrapper').setStyle('display','none');
			}
			if($('width'+types[i]+'-wrapper'))
			{
				$('width'+types[i]+'-wrapper').setStyle('display','none');
			}			
		}
		
		if($('max'+typeChoose+'-wrapper'))
		{
			$('max'+typeChoose+'-wrapper').setStyle('display','block');
		}
		
		if($('height'+typeChoose+'-wrapper'))
		{
			$('height'+typeChoose+'-wrapper').setStyle('display','block');
		}
		if($('width'+typeChoose+'-wrapper'))
		{
			$('width'+typeChoose+'-wrapper').setStyle('display','block');
		}
	} 
    window.addEvent('domready', function() {    	
        change($('typeyncontest').get('value'));
		//$('maxadvalbum-wrapper').style.display = 'block';
	});
</script>
