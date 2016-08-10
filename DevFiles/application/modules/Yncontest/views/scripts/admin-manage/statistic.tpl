<h2><?php echo $this->translate("Contest Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>
<div class="profile_fields">
		<h4><span><?php echo $this->translate('Contest Statistic');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Contest") ?></span>
				<span>
					<?php echo $this->contest; ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Summary") ?></span>
				<span>
					<?php echo $this->translate(array('%s follow', '%s follows', $this->contest->follow_count), $this->locale()->toNumber($this->contest->follow_count)) ?>
					-
					<?php echo $this->translate(array('%s view', '%s views', $this->contest->view_count), $this->locale()->toNumber($this->contest->view_count)) ?>
					-
					<?php echo $this->translate(array('%s comment', '%s comments', $this->contest->comment_count), $this->locale()->toNumber($this->contest->comment_count)) ?>					
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->contest->getTotalProduct()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Available Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->contest->getAvailableProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("contest Rating") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->contest->rate_ave)." ".$this->translate('Stars'); ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->contest->getFeaturedProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Units Sold") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->contest->sold_products) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Income") ?></span>
				<span>
					<?php echo $this->currencycontest($this->contest->getTotalAmount()) ?>
				</span>
			</li>
		
			<li>
				<span><?php echo $this->translate("Total Publish Fee") ?></span>
				<span>
					<?php echo $this->currencycontest($this->contest->getPublishedFee()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Feature Fee") ?></span>
				<span>
					<?php echo $this->currencycontest($this->contest->getFeaturedFee()) ?> 
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
