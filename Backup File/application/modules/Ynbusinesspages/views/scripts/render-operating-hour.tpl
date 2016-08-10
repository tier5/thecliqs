<li class="business-operatingHour">
    <?php $format = Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynbusinesspages_time_format', 0);
    foreach($this->operatingHours as $hour) :?>
    <div>
        <span class="day"><?php echo ucfirst(substr($hour->day, 0, 3))?></span>
        <span class="time-from">
        	<?php $from = $hour->from;
			if($format)
			{
				$from = date("H:i", strtotime($from)); 
			}
			echo $from;
        	?>
        </span>
        <span>-</span>
        <span class="time-to">
        	<?php $to = $hour->to;
			if($format)
			{
				$to = date("H:i", strtotime($to));
			}
			echo $to;
        	?>
        	</span>
    </div>
    <?php endforeach;?>
</li>