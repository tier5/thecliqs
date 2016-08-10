<?php if(count($this->award)>0) :?>
	<ul>
	<?php foreach ($this->award as $award):?>
		<li class="ynContest_award_<?php echo $award->award_type?>">
			<div>
				<a href="javascript:void(0)" class="ynContest_Awardtt">
					<div class="ynContest_spanAward_<?php echo $award->award_type?>">
						<span class = "ynContest_Award_bgRight"><?php echo $award->award_name ?></span>
					</div>
					<span class="tooltip">
						<span class="top"></span>
						<span class="middle"> 							
							
							<div><?php echo $this->translate('Given awards:') ?> <?php echo $award->numbers; ?> </div>
						</span>
						<span class="bottom"></span>
					</span>						
				</a>
				<?php if($award->award_type == 1):?>
					<span class="ynContest_valueAward_<?php echo $award->award_type?>"><?php echo $award->currency.$award->value ?></span>
				<?php else:?>
					<span class="ynContest_valueAward_<?php echo $award->award_type?>"><?php echo $award->description ?></span>
				<?php endif;?>
				<span class="ynContest_numberAward_<?php echo $award->award_type?>">(x<?php echo $award->quantities ?>)</span>
			</div>
		</li>
	<?php endforeach;?>
	</ul>
<?php endif;?>

