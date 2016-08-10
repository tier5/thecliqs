<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script language="javascript" type="text/javascript"
			src="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/scripts/jquery-1.7.1.min.js' ?>">
		</script>		
		<script language="javascript" type="text/javascript"
			src="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/scripts/jcarousellite_1.0.1.min.js' ?>">
		</script>
		<script language="javascript" type="text/javascript"
			src="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/scripts/colorpicker.js' ?>">
		</script>
		<script language="javascript" type="text/javascript"
			src="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/scripts/jquery.dd.js' ?>">
		</script>
		<script language="javascript" type="text/javascript"
			src="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/scripts/iframe.js' ?>">
		</script>
		
		<link href="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/styles/iframe.css'?>"
			media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/styles/colorpicker.css'?>"
			media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->baseUrl() . '/application/modules/Ynprofilestyler/externals/styles/dd.css'?>"
			media="screen" rel="stylesheet" type="text/css" />
			
		<script type="text/javascript">		
			ynps2.baseUrl = '<?php echo $this->url(array(), 'default', true)?>';
			ynps2.addTranslatedSentence('You have not selected any slides', 
				'<?php echo $this->translate('You have not selected any slides')?>');
			
			$(document).ready(function(){
				$.ajaxSetup({async:false});
				if (ynps2.time == null) {
					ynps2.live();
				}	
				ynps2.activeTab('#content-themes','index/themes', {}, 0);
				
				<?php foreach ($this->groups as $group) : ?>
					ynps2.addTab('#content-<?php echo $group->group_name ?>','index/custom-<?php echo $group->group_name ?>',{rulegroup_id : <?php echo $group->rulegroup_id?>}, true);					
				<?php endforeach;?> 
			});		
				
		</script>					
	</head>
	<body>
		<div class="ynprofile_style_top">
			<div class="ynprofile_style_left">
				<?php echo $this->translate('MENU'); ?>
			</div>
			<div class="ynprofile_style_right">
				<form>
					<div class="allow_members_element left">
						<?php if ($this->isAllowed) : ?>
                        	<input type="checkbox" name="allow_members_to_user" checked="checked" />
						<?php else : ?>    
                        	<input type="checkbox" name="allow_members_to_user" />
                        <?php endif; ?>    
						<span><?php echo $this->translate('Allow members to use my theme') ?></span>
					</div>
					
					<div class="right">
						<a href="javascript: ynps2.save()"><?php echo $this->translate('Save') ?></a>
						&nbsp;|&nbsp;
						<a href="javascript:void(0)" onclick="ynps2.switchBackToDefaultLayout(this)"><?php echo $this->translate('Switch Back To Default Theme') ?></a>
						&nbsp;|&nbsp;
						<a href="javascript:void(0)" onclick="ynps2.switchBackToDefaultSettings(this)"><?php echo $this->translate('Switch Back To User Current Theme') ?></a>
						&nbsp;|&nbsp;
						<?php if ($this->viewer->isAdmin()) : ?>
    						<a href="javascript: ynps2.addToDefaultThemes()"><?php echo $this->translate('Add To Default Themes') ?></a>
    						&nbsp;|&nbsp;
						<?php endif; ?>
						<a href="javascript: ynps2.close()"><?php echo $this->translate('Close') ?></a>
					</div>
					
					<div class="clear"></div>
				</form>
			</div>
		</div>
		
		<div class="ynprofile_style_body">
			<div id="vtab">
                <ul class="ynprofile_style_left">
                    <li onclick="javascript: ynps2.activeTab('#content-themes','index/themes', {}, 0)">
                    	<a href="javascript:void(0)">
                            <?php echo $this->translate('Themes') ?>
                        </a>
                    </li>
                    
                    <?php foreach ($this->groups as $group) : ?>
                		<li onclick="javascript:ynps2.activeTab('#content-<?php echo $group->group_name ?>','index/custom-<?php echo $group->group_name ?>',{rulegroup_id : <?php echo $group->rulegroup_id?>}, <?php echo $this->groups->key() + 1?>, true)">
                			<a href="javascript:void(0)">
                				<?php echo $this->translate($group->title) ?>
                			</a>
                		</li>
            		<?php endforeach; ?>
            		
                </ul>
                
                <div id="content-themes" class="middle"></div>                
                <?php foreach ($this->groups as $group) : ?>
                	<div id="content-<?php echo $group->group_name?>" class="middle"></div>
                <?php endforeach; ?>
                <div id="content-slideshow" class="middle"></div>
            </div>
		</div>
		
		<div class="clear"></div>
		
	</body>
</html>