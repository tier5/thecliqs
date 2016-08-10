<h2><?php echo $this->translate("Contest Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

<div class="profile_fields">
		<h4><span><?php echo $this->translate('Contest Statistics');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Total Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalContests); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Approved Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->approveContest); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->featuredContest); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Premium Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->premiumContest); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Ending Soon Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->endingsoonContest); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Followed Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->followContest); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Favorite Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->favoriteContest); ?>
				</span>
			</li>
		</ul>
	<h4><span><?php echo $this->translate('Entries Statistics');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Total Entries") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalEntries); ?>
				</span>
			</li>
				
		</ul>
	<h4><span><?php echo $this->translate('Paid Fee');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Published Fee") ?></span>
				<span>
				<?php echo $this->currencycontest($this->publishedFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Feature Service") ?></span>
				<span>
				<?php echo $this->currencycontest($this->featuredFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Premium Service") ?></span>
				<span>
				<?php echo $this->currencycontest($this->premiumFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Ending Soon Service") ?></span>
				<span>
				<?php echo $this->currencycontest($this->endingSoonFee) ?>
				</span>
			</li>			
	</ul>	
	</div>


<style type="text/css">
.profile_fields {
    margin-top: 10px;
    overflow: hidden;
}
.profile_fields h4 {
    border-bottom: 1px solid #EAEAEA;
    font-weight: bold;
    margin-bottom: 10px;
    padding: 0.5em 0;
}
.profile_fields h4 > span {
    background-color: #FFFFFF;
    display: inline-block;
    margin-top: -1px;
    padding-right: 6px;
    position: absolute;
    color: #717171;
    font-weight: bold;
}
.profile_fields > ul {
    padding: 10px;
    list-style-type: none;
}
.profile_fields > ul > li {
    overflow: hidden;
    margin-top: 3px;
}

.profile_fields > ul > li > span {
    display: block;
    float: left;
    margin-right: 15px;
    overflow: hidden;
    width: 275px;
}

.profile_fields > ul > li > span + span {
    display: block;
    float: left;
    min-width: 0;
    overflow: hidden;
    width: auto;
}

</style>
