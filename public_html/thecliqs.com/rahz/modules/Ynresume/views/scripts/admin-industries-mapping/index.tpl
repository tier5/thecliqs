<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>   

<script type="text/javascript">
	function saveMapping() {
	    $$('.mapping_industry').each(function(el) {
	    	var data_id = el.getAttribute('data-id');
		    var url = '<?php echo $this->url(array('module'=>'ynresume', 'controller' => 'industries-mapping','action'=>'save-mapping'), 'admin_default') ?>';
		    if(el.value != 'none')
		    {
		    	new Request.JSON({
			        url: url,
			        method: 'post',
			        async: true,
			        data: {
			        	'idResume': data_id,
			            'idJob': el.value,
			        },
			        onSuccess : function(responseJSON, responseText)
			        {
			        }
		    	}).send();
		    }
  		});
		alert('<?php echo $this -> translate('Save mapping successfully!');?>');
	}
</script>

<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php
	// Populate job industry list.
	$industriesJob = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustries();
	unset($industriesJob[0]);
?>

<div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
      	<div>
	    <?php echo $this->htmlLink(array(
			    'route' => 'admin_default',
			    'module' => 'ynresume',
			    'controller' => 'industries'
		), $this->translate('Back to Manage Industries'), array('class'=>'buttonlink ynjobposting_icon_back')) ?>
      	</div>
        
        <br />
        
      	<h3><?php echo $this->translate("Industry Mapping") ?></h3>
      	
        <p><?php echo $this->translate("YNRESUME_ADMIN_INDUSTRIES_MAPPING_DESCRIPTION") ?></p>
        <br />  
        <div>
         <?php foreach($this->industry->getBreadCrumNode() as $node): ?>
        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries-mapping', 'action' => 'index', 'parent_id' =>$node->industry_id), $this->translate($node->shortTitle()), array()) ?>
        		&raquo;
         <?php endforeach; ?>
         <strong><?php
         if(count($this->industry->getBreadCrumNode()) > 0):
            echo $this->industry;
          else:
            echo  $this->translate("All Industries");
          endif; ?></strong>
        </div>
        <br />
        <?php 
        	$settings = Engine_Api::_()->getApi('settings', 'core');
        	$showNotice = $settings->getSetting('ynresume_job_industry_change');
         ?>
         <?php if($showNotice == 1) :?>
         	<div class="tip">
         		<span><?php echo $this -> translate('Industry List in module Job Posting has just been changed. Please recheck your mapping again.');?></span>
         	</div>
         	<?php $settings->setSetting('ynresume_job_industry_change', 0);?>
         <?php endif;?>
          <?php if(count($this->industries)>0):?>
         <table class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Industry Name") ?></th>
              <th><?php echo $this->translate("Sub-Industry") ?></th>
              <th><?php echo $this->translate("Job Posting Industries") ?></th>
              <th>&nbsp;</th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->industries as $industry): ?>
              <tr id='industry_item_<?php echo $industry->getIdentity() ?>'>
                <td><?php echo $industry->title?></td>
                <td><?php echo $industry->countChildren() ?></td>
                <td>
                	<?php if(!empty($industriesJob)) :?>
            		<select data-id = <?php echo $industry -> getIdentity();?> class ="mapping_industry">
            			<?php 
            				$tableIndustrymaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynresume');
            				$row = $tableIndustrymaps -> getRowByIndustryId($industry->getIdentity());
            			?>
            			<option value="none"><?php echo $this -> translate("None");?></option>
            			<?php foreach ($industriesJob as $item) :?>
            				<option <?php if(!empty($row) && ($row -> job_industry_id == $item['industry_id'])) echo "selected"; ?> value="<?php echo $item['industry_id'];?>"><?php echo str_repeat("-- ", $item['level'] - 1) . $item['title'] ?></option>
            			<?php endforeach;?>
            		</select>
            		<?php else:?>
            			<?php echo $this -> translate('No industry found in Job Posting');?>
            		<?php endif;?>
                </td>
                 <td>
                	<?php if($industry->level <= 2) :?>
	                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'industries-mapping', 'action' => 'index', 'parent_id' =>$industry->industry_id), $this->translate('view sub-industry'), array(
	                  )) ?>
					  <?php endif;?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no industries.") ?></span>
      </div>
      <?php endif;?>
		<button onclick="saveMapping();" type='button'><?php echo $this->translate('Save Mapping') ?></button>
    </div>
    </form>
    </div>
  </div>
     

