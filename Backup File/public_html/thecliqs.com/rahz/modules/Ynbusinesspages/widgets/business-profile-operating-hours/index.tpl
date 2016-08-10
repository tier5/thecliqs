<?php if(!empty($this -> business -> timezone)):?>
<div style="padding: 5px 0">
	<?php echo $this -> business -> timezone;?>
</div>
<?php endif;?>
<?php if(count($this -> operationHours)):?>
<ul class="ynbusinesspages-overview-listtime">		
	<?php $format = Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynbusinesspages_time_format', 0);
	foreach($this -> operationHours  as $operationHour) :?>
	<li>
		<span><?php echo $this -> translate(ucfirst($operationHour -> day))?></span>
		<span>
			<?php if($operationHour -> from == 'CLOSED') :?>
				<?php echo $this -> translate('CLOSED') ?>
			<?php else :?>
				<?php $from = $operationHour->from;
				if($format)
				{
					$from = date("H:i", strtotime($from));
				}
				echo $from?>
				-	
				<?php $to = $operationHour->to;
				if($format)
				{
					$to = date("H:i", strtotime($to)); 
				}
				echo $to?>
			<?php endif;?>
		</span>
	</li>
	<?php endforeach;?>
</ul>
<?php endif;?>