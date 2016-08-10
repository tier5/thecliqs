<ul class="contest_rule">
<?php if(count($this->rules) > 0):?>
	<?php foreach ($this->rules as $rule):?>	
	<li>
		<ul>
			<li class="contest_rule_title"> <?php echo $rule->rule_name?> </li>
			<li class="contest_rule_time"> 
				<span class="ynContest_ruleTime"><?php echo $this->translate('Time period:')?></span>
				<?php echo $rule->start_date?> -- <?php echo $rule->end_date?> 
			</li>
			<li class="contest_permission">
				<h4><span><?php echo $this->translate('Permission:')?></span></h4>		  
				<?php if($rule->submitentries ==1) echo $this->translate("- Allow to submit entries")?> <br/>	
				<?php if($rule->voteentries ==1) echo $this->translate("- Allow to vote for entries")?> 
			</li>
			<li class="contest_rule_desription">
				<h4><span><?php echo $this->translate('Description:')?></span></h4>
				<?php echo $rule->description?> 	       
			</li>		
		</ul>
	</li>
	<?php endforeach;?>
<?php else:?>
<?php endif;?>
</ul>

